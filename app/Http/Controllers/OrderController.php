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

    /**
     * Pelacakan status antrean pesanan (Customer)
     */
    public function track(string $orderNumber)
    {
        $order = Order::with('depot')->where('order_number', $orderNumber)->firstOrFail();
        return view('orders.track', compact('order'));
    }

    /**
     * Halaman daftar antrean masuk
     */
    public function index()
    {
        $orders = Order::with('depot')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        return view('orders.index', compact('orders'));
    }
}
