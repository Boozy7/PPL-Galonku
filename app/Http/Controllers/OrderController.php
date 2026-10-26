<?php

namespace App\Http\Controllers;

use App\Models\Depot;
use App\Models\Order;
use App\Models\WaterProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Display list of current / active orders.
     */
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));

        if ($search !== '') {
            $foundOrder = Order::with('depot')
                ->where('order_number', $search)
                ->orWhere('queue_number', $search)
                ->orWhere('queue_number', '#' . ltrim($search, '#'))
                ->orWhere('customer_phone', $search)
                ->latest()
                ->first();

            if (!$foundOrder) {
                $foundOrder = Order::with('depot')
                    ->whereRaw('LOWER(order_number) = ?', [strtolower($search)])
                    ->orWhereRaw('LOWER(queue_number) = ?', [strtolower($search)])
                    ->orWhereRaw('LOWER(queue_number) = ?', ['#' . strtolower(ltrim($search, '#'))])
                    ->latest()
                    ->first();
            }

            if ($foundOrder) {
                return redirect()->route('orders.track', $foundOrder->order_number);
            }

            return redirect()->route('orders.index')
                ->with('error', "Antrean dengan nomor '{$search}' tidak ditemukan. Silakan periksa kembali.");
        }

        // Get orders from session
        $sessionOrders = session()->get('my_order_numbers', []);
        $orders = collect();

        if (!empty($sessionOrders)) {
            $orders = Order::with('depot')
                ->whereIn('order_number', $sessionOrders)
                ->latest()
                ->get();
        }

        // Fallback: If no orders in session (or new session), show today's / recent orders so user immediately sees their order
        if ($orders->isEmpty()) {
            $orders = Order::with('depot')->latest()->take(6)->get();
        }

        return view('orders.index', [
            'orders' => $orders,
        ]);
    }

    /**
     * Show pickup order form for a specific depot.
     */
    public function create(int $depotId)
    {
        $depot = Depot::with('products')->findOrFail($depotId);

        return view('orders.create', [
            'depot' => $depot,
        ]);
    }

    /**
     * Store new pickup order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'depot_id' => 'required|exists:depots,id',
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:20',
            'pickup_time' => 'nullable|string|max:50',
            'gallon_action' => 'required|in:bawa_sendiri,beli_baru,tukar_galon',
            'notes' => 'nullable|string|max:500',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:water_products,id',
            'products.*.qty' => 'required|integer|min:0',
        ]);

        $depot = Depot::findOrFail($validated['depot_id']);

        $orderItems = [];
        $totalQuantity = 0;
        $waterSubtotal = 0;

        foreach ($validated['products'] as $item) {
            $qty = (int)$item['qty'];
            if ($qty > 0) {
                $product = WaterProduct::where('depot_id', $depot->id)->findOrFail($item['id']);
                $subtotal = $product->price * $qty;
                $waterSubtotal += $subtotal;
                $totalQuantity += $qty;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'type' => $product->type,
                    'volume' => $product->volume,
                    'price' => $product->price,
                    'qty' => $qty,
                    'subtotal' => $subtotal,
                ];
            }
        }

        if ($totalQuantity <= 0) {
            return back()->withErrors(['products' => 'Pilih minimal 1 galon atau produk air yang ingin diisi.'])->withInput();
        }

        // Additional fee if buying new gallon
        $newGallonFee = 0;
        if ($validated['gallon_action'] === 'beli_baru') {
            $newGallonFee = $depot->new_gallon_fee * $totalQuantity;
        }

        $totalPrice = $waterSubtotal + $newGallonFee;

        // Generate Queue Number e.g. #A-18
        $todayOrderCount = Order::whereDate('created_at', today())->count();
        $queueLetter = chr(65 + (int)floor($todayOrderCount / 50)); // A, B, C...
        $queueSeq = str_pad(($todayOrderCount % 50) + 1, 2, '0', STR_PAD_LEFT);
        $queueNumber = "#{$queueLetter}-{$queueSeq}";

        // Generate Order Number & 4-digit PIN
        $orderNumber = 'GLK-' . date('Ymd') . '-' . strtoupper(Str::random(5));
        $pickupPin = str_pad(mt_rand(1000, 9999), 4, '0', STR_PAD_LEFT);

        $firstItem = $orderItems[0] ?? null;

        $order = Order::create([
            'order_number' => $orderNumber,
            'queue_number' => $queueNumber,
            'depot_id' => $depot->id,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'water_product_id' => $firstItem['product_id'] ?? null,
            'water_product_name' => $firstItem ? ($totalQuantity > $firstItem['qty'] ? $firstItem['name'] . ' (+varian lain)' : $firstItem['name']) : 'Isi Ulang Air Galon',
            'water_type' => $firstItem['type'] ?? 'ro',
            'unit_price' => $firstItem['price'] ?? 8000,
            'quantity' => $totalQuantity,
            'gallon_option' => $validated['gallon_action'] === 'beli_baru' ? 'new_gallon' : 'bring_own',
            'gallon_fee' => $newGallonFee,
            'total_amount' => $totalPrice,
            'pickup_time_estimated' => $validated['pickup_time'] ?? '15-30 Menit Lagi',
            'pickup_pin' => $pickupPin,
            'status' => 'DITERIMA',
            'notes' => $validated['notes'] ?? null,
        ]);

        // Save order to session list
        $myOrders = session()->get('my_order_numbers', []);
        $myOrders[] = $order->order_number;
        session()->put('my_order_numbers', array_values(array_unique($myOrders)));

        return redirect()->route('orders.track', $order->order_number)
            ->with('success', 'Pesanan pickup berhasil dibuat! Silakan pantau antrean Anda.');
    }

    /**
     * Real-time tracking view for an order.
     */
    public function track(string $orderNumber)
    {
        $clean = trim($orderNumber);

        // Search by order_number or queue_number (e.g. #A-04 or A-04)
        $order = Order::with('depot')
            ->where('order_number', $clean)
            ->orWhere('queue_number', $clean)
            ->orWhere('queue_number', '#' . ltrim($clean, '#'))
            ->latest()
            ->first();

        if (!$order) {
            $order = Order::with('depot')
                ->whereRaw('LOWER(order_number) = ?', [strtolower($clean)])
                ->orWhereRaw('LOWER(queue_number) = ?', [strtolower($clean)])
                ->orWhereRaw('LOWER(queue_number) = ?', ['#' . strtolower(ltrim($clean, '#'))])
                ->latest()
                ->first();
        }

        if (!$order) {
            return redirect()->route('orders.index')
                ->with('error', "Antrean dengan nomor '{$orderNumber}' tidak ditemukan.");
        }

        // Remember in session
        $myOrders = session()->get('my_order_numbers', []);
        if (!in_array($order->order_number, $myOrders)) {
            $myOrders[] = $order->order_number;
            session()->put('my_order_numbers', array_values(array_unique($myOrders)));
        }

        // Canonical redirect if accessed via queue_number (e.g. #A-04)
        if ($clean !== $order->order_number) {
            return redirect()->route('orders.track', $order->order_number);
        }

        // Check if there are orders ahead in queue
        $ordersAhead = Order::where('depot_id', $order->depot_id)
            ->whereIn('status', ['DITERIMA', 'SEDANG_DIISI'])
            ->where('id', '<', $order->id)
            ->count();

        // Direct Google Maps Route URL
        $googleMapsUrl = "https://www.google.com/maps/dir/?api=1&destination=" . urlencode("{$order->depot->lat},{$order->depot->lng}") . "&travelmode=driving";

        return view('orders.track', [
            'order' => $order,
            'ordersAhead' => $ordersAhead,
            'googleMapsUrl' => $googleMapsUrl,
        ]);
    }

    /**
     * Advance status simulation (for demo / testing workflow).
     */
    public function advanceStatus(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        // Alur antrean status
        if ($order->status === 'DITERIMA') {
            $order->status = 'SEDANG_DIISI';
            $order->save();
            return redirect()->route('orders.track', $order->order_number)
                ->with('status_updated', "Status antrean diperbarui: Diproses (Sedang Disterilisasi & Diisi).");
        } else {
            $order->status = 'DITERIMA';
            $order->save();
            return redirect()->route('orders.track', $order->order_number)
                ->with('status_updated', "Status antrean direset ke: Menunggu Antrean (Diterima).");
        }
    }
}