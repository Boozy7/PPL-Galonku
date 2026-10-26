<?php

namespace App\Http\Controllers;

use App\Models\Depot;
use App\Models\Order;
use App\Models\WaterProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DepotDashboardController extends Controller
{
    public function index()
    {
        $depot = Depot::where('user_id', Auth::id())->first();

        if (!$depot) {
            // Fallback for demo: jika user adalah depot tapi belum terhubung, hubungkan ke depot pertama
            $depot = Depot::first();
            if ($depot) {
                $depot->user_id = Auth::id();
                $depot->save();
            } else {
                return "Belum ada data depot di sistem.";
            }
        }

        // Antrean pesanan masuk
        $activeOrders = Order::where('depot_id', $depot->id)
            ->whereIn('status', ['DITERIMA', 'SEDANG_DIISI', 'SIAP_DIAMBIL'])
            ->orderBy('created_at', 'asc')
            ->get();

        $completedOrdersCount = Order::where('depot_id', $depot->id)
            ->where('status', 'SELESAI')
            ->whereDate('created_at', today())
            ->count();

        // Manajemen Katalog Produk & Layanan
        $products = WaterProduct::where('depot_id', $depot->id)->orderBy('id', 'asc')->get();

        return view('depot.dashboard', compact('depot', 'activeOrders', 'completedOrdersCount', 'products'));
    }

    // Status Operasional dasar (Buka / Tutup toko sederhana)
    public function toggleStatus()
    {
        $depot = Depot::where('user_id', Auth::id())->firstOrFail();
        $depot->is_open = !$depot->is_open;
        $depot->save();

        return back()->with('success', 'Status operasional depot berhasil diubah menjadi: ' . ($depot->is_open ? 'Buka' : 'Tutup') . '.');
    }

    // Pembaruan status antrean
    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status' => 'required|in:SEDANG_DIISI'
        ]);

        $order->status = $request->status;
        $order->save();

        return back()->with('success', 'Status pesanan ' . $order->queue_number . ' diperbarui menjadi Sedang Diproses.');
    }

    public function finishOrder(Request $request, $id)
    {
        return back()->with('info', 'Status pesanan saat ini dalam antrean proses.');
    }

    public function uploadCert(Request $request)
    {
        $depot = Depot::where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'certificate' => 'required|image|max:5120'
        ]);

        $path = $request->file('certificate')->store('certificates', 'public');

        $depot->certification_status = 'PROSES_RENEWAL';
        $depot->save();

        return back()->with('success', 'Sertifikat baru berhasil diunggah.');
    }

    // ==========================================
    // CRUD Katalog Produk & Layanan
    // ==========================================
    public function storeProduct(Request $request)
    {
        $depot = Depot::where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:ro,mineral,alkali,branded',
            'price' => 'required|numeric|min:1000',
            'volume' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
        ]);

        $validated['depot_id'] = $depot->id;
        if (empty($validated['volume'])) {
            $validated['volume'] = '19 Liter';
        }
        $validated['tds_avg'] = $validated['type'] === 'ro' ? 15 : ($validated['type'] === 'alkali' ? 25 : 60);

        WaterProduct::create($validated);

        return back()->with('success', 'Produk "' . $validated['name'] . '" berhasil ditambahkan ke katalog!');
    }

    public function updateProduct(Request $request, $id)
    {
        $depot = Depot::where('user_id', Auth::id())->firstOrFail();
        $product = WaterProduct::where('depot_id', $depot->id)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:ro,mineral,alkali,branded',
            'price' => 'required|numeric|min:1000',
            'volume' => 'nullable|string|max:50',
        ]);

        $product->update($validated);

        return back()->with('success', 'Produk "' . $product->name . '" berhasil diperbarui!');
    }

    public function deleteProduct($id)
    {
        $depot = Depot::where('user_id', Auth::id())->firstOrFail();
        $product = WaterProduct::where('depot_id', $depot->id)->findOrFail($id);
        $name = $product->name;
        $product->delete();

        return back()->with('success', 'Produk "' . $name . '" telah dihapus dari katalog.');
    }
}
