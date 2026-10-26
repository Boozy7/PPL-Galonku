@extends('layouts.app', ['title' => 'Dasbor Mitra Depot - Galonku'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

    <!-- Top Banner -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-brand-100 text-brand-900 border border-brand-200">
                    PORTAL MITRA
                </span>
                <span class="text-xs text-slate-500 font-semibold">• Portal Mitra DAMIU Surabaya</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ $depot->name ?? 'Depot Mitra' }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Pengelola: <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->email }}) • {{ $depot->address ?? 'Kota Surabaya' }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('depots.show', $depot->slug) }}" target="_blank" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                <span>Lihat Profil Publik</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl text-xs font-bold transition">
                    Keluar
                </button>
            </form>
        </div>
    </div>

    <!-- Flash Alerts -->
    @if(session('success'))
        <div class="mb-6 bg-emerald-50 text-emerald-800 p-4 rounded-2xl text-xs sm:text-sm font-semibold border border-emerald-200 flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('info'))
        <div class="mb-6 bg-blue-50 text-blue-800 p-4 rounded-2xl text-xs sm:text-sm font-semibold border border-blue-200 flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('info') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="mb-6 bg-rose-50 text-rose-800 p-4 rounded-2xl text-xs sm:text-sm font-semibold border border-rose-200">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- KOLOM KIRI (1 Col): Status Toko & Dokumen Sertifikasi -->
        <div class="space-y-6">

            <!-- Operasional Toko (Buka / Tutup) -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Status Operasional</h3>
                        <p class="text-[11px] text-slate-500">Buka/tutup penerimaan antrean isi ulang</p>
                    </div>
                    <span class="w-2.5 h-2.5 rounded-full {{ $depot->is_open ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></span>
                </div>

                <div class="p-4 rounded-2xl {{ $depot->is_open ? 'bg-emerald-50/80 border border-emerald-200' : 'bg-rose-50/80 border border-rose-200' }} flex items-center justify-between mb-4">
                    <span class="font-extrabold text-sm {{ $depot->is_open ? 'text-emerald-900' : 'text-rose-900' }}">
                        {{ $depot->is_open ? 'TOKO SEDANG BUKA' : 'TOKO SEDANG TUTUP' }}
                    </span>
                    <form action="{{ route('depot.toggle_status') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3.5 py-1.5 text-xs font-bold rounded-xl text-white shadow-sm transition {{ $depot->is_open ? 'bg-rose-600 hover:bg-rose-700' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                            {{ $depot->is_open ? 'Tutup Toko' : 'Buka Toko' }}
                        </button>
                    </form>
                </div>
                <div class="text-[11px] text-slate-400">
                    Jam Operasional Reguler: <strong>{{ $depot->open_hours ?? '08.00 - 20.00 WIB' }}</strong>
                </div>
            </div>

            <!-- Status Sertifikat SLHS Dinkes -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Sertifikasi Higiene (DAMIU)</h3>

                @if($depot->certification_status === 'AKTIF')
                    <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-2xl mb-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <p class="text-xs font-bold text-emerald-900">Sertifikat SLHS Terverifikasi</p>
                        </div>
                        <p class="text-[11px] text-emerald-700 mt-0.5">No. SLHS: <span class="font-mono font-bold">{{ $depot->slhs_number ?? '443.5/SLHS-DAMIU/2026' }}</span></p>
                        <p class="text-[10px] text-emerald-600 mt-0.5">Berlaku sampai: {{ $depot->expiry_date }} (Grade {{ $depot->grade ?? 'A' }})</p>
                    </div>
                @else
                    <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-2xl mb-4">
                        <p class="text-xs font-bold text-amber-900">Dalam Proses Verifikasi Dinkes</p>
                        <p class="text-[11px] text-amber-700 mt-0.5">Dokumen uji lab sedang diproses dinas.</p>
                    </div>
                @endif

                <form action="{{ route('depot.upload_cert') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label class="block text-[11px] font-bold text-slate-600 mb-1.5">Unggah Pembaruan Sertifikat / Hasil Lab</label>
                    <input type="file" name="certificate" required class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                    <button type="submit" class="w-full mt-3 px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition">
                        Unggah Dokumen SLHS
                    </button>
                </form>
            </div>

        </div>

        <!-- KOLOM TENGAH & KANAN (2 Cols): Katalog & Antrean -->
        <div class="lg:col-span-2 space-y-6">

            <!-- ============================================================= -->
            <!-- MANAJEMEN KATALOG PRODUK & LAYANAN (CRUD PRODUK) -->
            <!-- ============================================================= -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-200/80">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5 pb-4 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-extrabold text-slate-900">Manajemen Katalog Produk & Layanan</h2>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Kelola daftar varian air isi ulang galon dan tarif harga per galon</p>
                    </div>

                    <button type="button"
                            onclick="document.getElementById('modalTambahProduk').classList.remove('hidden')"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl shadow-md shadow-brand-500/20 transition self-start sm:self-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah Produk Galon</span>
                    </button>
                </div>

                <!-- Product Catalog Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                <th class="pb-3 pl-2">Nama Produk Air</th>
                                <th class="pb-3">Kategori</th>
                                <th class="pb-3">Volume</th>
                                <th class="pb-3">Harga Isi Ulang</th>
                                <th class="pb-3 text-right pr-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($products as $prod)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 pl-2 font-bold text-slate-900">
                                        {{ $prod->name }}
                                    </td>
                                    <td class="py-3.5">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase
                                            {{ $prod->type === 'ro' ? 'bg-cyan-100 text-cyan-800' : ($prod->type === 'alkali' ? 'bg-purple-100 text-purple-800' : 'bg-emerald-100 text-emerald-800') }}">
                                            {{ $prod->type }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 text-slate-600 font-medium">
                                        {{ $prod->volume ?? '19 Liter' }}
                                    </td>
                                    <td class="py-3.5 font-bold text-brand-700 font-mono">
                                        Rp {{ number_format($prod->price, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 text-right pr-2">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <!-- Edit Price Button -->
                                            <button type="button"
                                                    onclick="openEditProductModal({{ $prod->id }}, '{{ addslashes($prod->name) }}', '{{ $prod->type }}', {{ $prod->price }}, '{{ $prod->volume ?? '19 Liter' }}')"
                                                    class="p-1.5 rounded-lg bg-slate-100 hover:bg-brand-50 text-slate-600 hover:text-brand-700 transition"
                                                    title="Ubah Harga / Nama">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            </button>

                                            <!-- Delete Product Form -->
                                            <form action="{{ route('depot.products.destroy', $prod->id) }}" method="POST" onsubmit="return confirm('Hapus produk {{ $prod->name }} dari katalog?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 transition" title="Hapus Produk">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-400">
                                        Belum ada produk di katalog. Klik "Tambah Produk Galon" di atas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ============================================================= -->
            <!-- DASHBOARD MANAJEMEN ANTREAN DEPOT -->
            <!-- ============================================================= -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-200/80">
                <div class="flex items-center justify-between mb-5 pb-4 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-extrabold text-slate-900">Manajemen Antrean Pesanan Masuk</h2>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar antrean pemesanan isi ulang galon hari ini</p>
                    </div>
                    <span class="text-xs font-bold text-brand-700 bg-brand-50 px-3 py-1 rounded-full border border-brand-200">
                        {{ $activeOrders->count() }} Antrean Aktif
                    </span>
                </div>

                @if($activeOrders->isEmpty())
                    <div class="text-center py-12 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                        <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <p class="text-sm font-bold text-slate-700">Belum ada antrean masuk saat ini</p>
                        <p class="text-xs text-slate-400 mt-0.5">Pesanan dari customer akan otomatis muncul di sini</p>
                    </div>
                @else
                    <div class="space-y-3.5">
                        @foreach($activeOrders as $order)
                            <div class="p-4 sm:p-5 rounded-2xl border border-slate-200/90 bg-slate-50/70 hover:bg-white transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-1 bg-brand-600 text-white font-mono font-black rounded-xl text-sm shadow-sm">
                                            {{ $order->queue_number }}
                                        </span>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border
                                            {{ $order->status == 'DITERIMA' ? 'bg-sky-50 text-sky-800 border-sky-300' : 'bg-amber-50 text-amber-800 border-amber-300' }}">
                                            {{ $order->status === 'DITERIMA' ? '1. Menunggu Giliran' : '2. Sedang Diproses' }}
                                        </span>
                                        <span class="text-[11px] font-mono text-slate-400">{{ $order->order_number }}</span>
                                    </div>
                                    <div class="text-xs font-bold text-slate-900 pt-1">
                                        {{ $order->customer_name }} <span class="text-slate-500 font-mono font-normal">({{ $order->customer_phone }})</span>
                                    </div>
                                    <div class="text-xs text-slate-600">
                                        {{ $order->quantity }}x {{ $order->water_product_name }} • Total: <strong class="text-slate-900 font-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>
                                    </div>
                                </div>

                                <!-- Action Button -->
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    @if($order->status == 'DITERIMA')
                                        <form action="{{ route('depot.update_order_status', $order->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="SEDANG_DIISI">
                                            <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl shadow-sm transition flex items-center gap-1.5">
                                                <span>Mulai Proses Cuci & Isi</span>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                            </button>
                                        </form>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-100 text-amber-900 text-xs font-bold">
                                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                            <span>Sedang Diproses</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

    </div>
</div>

<!-- ========================================== -->
<!-- MODAL TAMBAH PRODUK -->
<!-- ========================================== -->
<div id="modalTambahProduk" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <h3 class="text-sm font-extrabold text-slate-900">Tambah Produk Air Baru</h3>
            <button type="button" onclick="document.getElementById('modalTambahProduk').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">✕</button>
        </div>

        <form action="{{ route('depot.products.store') }}" method="POST" class="space-y-3.5">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Produk Air *</label>
                <input type="text" name="name" required placeholder="Contoh: Air RO Hexagonal Murni" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Air *</label>
                    <select name="type" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500">
                        <option value="ro">Reverse Osmosis (RO)</option>
                        <option value="mineral">Air Mineral</option>
                        <option value="alkali">Air Alkali</option>
                        <option value="branded">Galon Bermerek</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Volume *</label>
                    <input type="text" name="volume" value="19 Liter" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Harga Isi Ulang (Rp) *</label>
                <input type="number" name="price" required min="1000" step="500" placeholder="Contoh: 8000" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 font-mono">
            </div>

            <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalTambahProduk').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold">Batal</button>
                <button type="submit" class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold transition">Simpan ke Katalog</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL EDIT PRODUK -->
<!-- ========================================== -->
<div id="modalEditProduk" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <h3 class="text-sm font-extrabold text-slate-900">Ubah Produk Air</h3>
            <button type="button" onclick="document.getElementById('modalEditProduk').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">✕</button>
        </div>

        <form id="formEditProduk" method="POST" class="space-y-3.5">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Produk Air *</label>
                <input type="text" id="editName" name="name" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Air *</label>
                    <select id="editType" name="type" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500">
                        <option value="ro">Reverse Osmosis (RO)</option>
                        <option value="mineral">Air Mineral</option>
                        <option value="alkali">Air Alkali</option>
                        <option value="branded">Galon Bermerek</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Volume *</label>
                    <input type="text" id="editVolume" name="volume" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Harga Isi Ulang (Rp) *</label>
                <input type="number" id="editPrice" name="price" required min="1000" step="500" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 font-mono">
            </div>

            <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalEditProduk').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold">Batal</button>
                <button type="submit" class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold transition">Perbarui Produk</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditProductModal(id, name, type, price, volume) {
        const form = document.getElementById('formEditProduk');
        form.action = "{{ url('/depot/products') }}/" + id;
        document.getElementById('editName').value = name;
        document.getElementById('editType').value = type;
        document.getElementById('editPrice').value = price;
        document.getElementById('editVolume').value = volume;
        document.getElementById('modalEditProduk').classList.remove('hidden');
    }
</script>
@endsection
