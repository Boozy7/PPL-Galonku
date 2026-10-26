@extends('layouts.app', ['title' => 'Pesan Pickup - ' . $depot->name])

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

    <!-- Breadcrumb -->
    <div class="mb-6">
        <a href="{{ route('depots.show', $depot->slug) }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Detail Depot
        </a>
    </div>

    <!-- Error notice if any -->
    @if($errors->any())
        <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl">
            <p class="text-xs font-bold uppercase tracking-wider mb-1">Periksa kembali data pesanan Anda:</p>
            <ul class="list-disc pl-5 text-xs space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- LEFT (2 Cols): Form Order -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
                
                <div class="border-b border-slate-100 pb-5 mb-6">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-brand-600">Layanan Ambil Sendiri (Takeaway)</span>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 mt-1">Formulir Pesan Isi Ulang Air</h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Pesan terlebih dahulu agar galon disterilkan dan diisi siap diambil tanpa perlu antre lama di lokasi.
                    </p>
                </div>

                <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
                    @csrf
                    <input type="hidden" name="depot_id" value="{{ $depot->id }}">

                    <!-- 1. PILIH PRODUK AIR -->
                    <div class="mb-6">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">
                            1. Pilih Jenis Air & Jumlah Galon
                        </label>

                        <div class="space-y-3">
                            @foreach($depot->products as $index => $prod)
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-4">
                                    <input type="hidden" name="products[{{ $index }}][id]" value="{{ $prod->id }}">
                                    <div>
                                        <div class="text-sm font-extrabold text-slate-900">{{ $prod->name }}</div>
                                        <div class="text-xs text-brand-700 font-bold">
                                            Rp {{ number_format($prod->price, 0, ',', '.') }} 
                                            <span class="text-slate-400 font-normal">/ galon ({{ $prod->volume }})</span>
                                        </div>
                                    </div>

                                    <!-- Stepper Quantity Button -->
                                    <div class="flex items-center gap-2 bg-white px-2 py-1 rounded-xl border border-slate-200">
                                        <button type="button" 
                                                onclick="updateProductQty({{ $index }}, -1)"
                                                class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center transition">
                                            -
                                        </button>
                                        <input type="number" 
                                               id="qtyInput_{{ $index }}" 
                                               name="products[{{ $index }}][qty]" 
                                               value="{{ $index === 0 ? 1 : 0 }}" 
                                               min="0" 
                                               max="20"
                                               data-price="{{ $prod->price }}"
                                               onchange="recalculateTotal()"
                                               class="w-10 text-center font-bold text-sm border-none focus:outline-none focus:ring-0">
                                        <button type="button" 
                                                onclick="updateProductQty({{ $index }}, 1)"
                                                class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center transition">
                                            +
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- 2. STATUS GALON -->
                    <div class="mb-6">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">
                            2. Ketentuan Galon
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <label class="p-3.5 rounded-2xl border cursor-pointer transition flex flex-col justify-between" id="opt_bawa_sendiri">
                                <div class="flex items-center gap-2">
                                    <input type="radio" name="gallon_action" value="bawa_sendiri" checked onchange="recalculateTotal()" class="text-brand-600 focus:ring-brand-500">
                                    <span class="text-xs font-bold text-slate-900">Bawa Sendiri</span>
                                </div>
                                <span class="text-[11px] text-slate-500 mt-2">Bawa galon kosong Anda untuk diisi (Gratis).</span>
                            </label>

                            <label class="p-3.5 rounded-2xl border cursor-pointer transition flex flex-col justify-between" id="opt_tukar_galon">
                                <div class="flex items-center gap-2">
                                    <input type="radio" name="gallon_action" value="tukar_galon" onchange="recalculateTotal()" class="text-brand-600 focus:ring-brand-500">
                                    <span class="text-xs font-bold text-slate-900">Tukar Galon</span>
                                </div>
                                <span class="text-[11px] text-slate-500 mt-2">Tukar galon kosong merk standar di kasir (Gratis).</span>
                            </label>

                            <label class="p-3.5 rounded-2xl border cursor-pointer transition flex flex-col justify-between" id="opt_beli_baru">
                                <div class="flex items-center gap-2">
                                    <input type="radio" name="gallon_action" value="beli_baru" onchange="recalculateTotal()" class="text-brand-600 focus:ring-brand-500">
                                    <span class="text-xs font-bold text-slate-900">Beli Galon Baru</span>
                                </div>
                                <span class="text-[11px] text-brand-700 font-bold mt-2">+Rp {{ number_format($depot->new_gallon_fee, 0, ',', '.') }}/galon</span>
                            </label>
                        </div>
                    </div>

                    <!-- 3. DATA PEMESAN & JADWAL AMBIL -->
                    <div class="mb-6 space-y-4">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            3. Informasi Pemesan
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Nama Pemesan *</label>
                                <input type="text" name="customer_name" required value="{{ old('customer_name') }}" placeholder="Contoh: Budi Santoso"
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Nomor WhatsApp Aktif *</label>
                                <input type="text" name="customer_phone" required value="{{ old('customer_phone') }}" placeholder="Contoh: 081234567890"
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Perkiraan Waktu Pengambilan Galon</label>
                            <select name="pickup_time" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                                <option value="15-30 Menit Lagi">15 - 30 Menit Lagi (Segera)</option>
                                <option value="1 Jam Lagi">1 Jam Lagi</option>
                                <option value="Sore Nanti (16:00 - 18:00 WIB)">Sore Nanti (16:00 - 18:00 WIB)</option>
                                <option value="Malam Ini (19:00 - 21:00 WIB)">Malam Ini (19:00 - 21:00 WIB)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Catatan Tambahan (Opsional)</label>
                            <input type="text" name="notes" placeholder="Contoh: Tolong sikat bersih bagian luar & dalam galon"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                        </div>
                    </div>

                    <!-- SUBMIT BUTTON -->
                    <button type="submit" 
                            class="w-full py-4 bg-brand-600 hover:bg-brand-700 text-white rounded-2xl font-extrabold text-sm shadow-xl shadow-brand-500/25 transition">
                        Ambil Antrean & Konfirmasi Pickup
                    </button>
                </form>

            </div>
        </div>

        <!-- RIGHT (1 Col): Order Summary & Depot Info -->
        <div class="space-y-6">

            <!-- Depot Quick Info Card -->
            <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Lokasi Pengambilan Galon</span>
                <div class="flex items-center gap-3">
                    <img src="{{ $depot->cover_image }}" 
                         alt="{{ $depot->name }}" 
                         onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1550989460-0adf9ea622e2?auto=format&fit=crop&w=800&q=80';"
                         class="w-14 h-14 rounded-2xl object-cover">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">{{ $depot->name }}</h3>
                        <p class="text-xs text-slate-500 line-clamp-1">{{ $depot->address }}</p>
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 mt-0.5">
                            ✓ Lulus Uji Dinkes Surabaya
                        </span>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-500">Jam Operasional:</span>
                    <span class="font-bold text-slate-800">{{ $depot->open_hours }}</span>
                </div>
            </div>

            <!-- Price Breakdown Box -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 mb-4">Rincian Pembayaran</h3>

                <div class="space-y-2.5 text-xs">
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Subtotal Isi Ulang Air (<span id="summaryTotalQty">1</span> Galon)</span>
                        <span class="font-bold text-slate-900" id="summaryWaterCost">Rp 0</span>
                    </div>

                    <div class="flex items-center justify-between text-slate-600">
                        <span>Biaya Beli Galon Baru</span>
                        <span class="font-bold text-slate-900" id="summaryGallonCost">Rp 0</span>
                    </div>

                    <div class="flex items-center justify-between text-emerald-700 bg-emerald-50 px-2.5 py-1.5 rounded-xl font-bold">
                        <span>Biaya Pengiriman (Ongkir)</span>
                        <span>Rp 0 (Self-Pickup)</span>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-sm">
                        <span class="font-extrabold text-slate-900">Total Pembayaran</span>
                        <span class="text-lg font-black text-brand-700" id="summaryTotalCost">Rp 0</span>
                    </div>
                </div>

                <div class="mt-4 text-[11px] text-slate-400 text-center">
                    Pembayaran dilakukan langsung di depot saat Anda mengambil galon (Tunai / QRIS).
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    const newGallonFee = {{ $depot->new_gallon_fee ?? 35000 }};

    function updateProductQty(index, delta) {
        const input = document.getElementById(`qtyInput_${index}`);
        let current = parseInt(input.value) || 0;
        let next = Math.max(0, current + delta);
        input.value = next;
        recalculateTotal();
    }

    function recalculateTotal() {
        const qtyInputs = document.querySelectorAll('[id^="qtyInput_"]');
        let totalQty = 0;
        let waterCost = 0;

        qtyInputs.forEach(input => {
            const qty = parseInt(input.value) || 0;
            const price = parseInt(input.dataset.price) || 0;
            totalQty += qty;
            waterCost += (qty * price);
        });

        // Check gallon action
        const gallonAction = document.querySelector('input[name="gallon_action"]:checked').value;
        let gallonCost = 0;
        if (gallonAction === 'beli_baru') {
            gallonCost = totalQty * newGallonFee;
        }

        const totalCost = waterCost + gallonCost;

        // Update Summary DOM
        document.getElementById('summaryTotalQty').innerText = totalQty;
        document.getElementById('summaryWaterCost').innerText = 'Rp ' + waterCost.toLocaleString('id-ID');
        document.getElementById('summaryGallonCost').innerText = 'Rp ' + gallonCost.toLocaleString('id-ID');
        document.getElementById('summaryTotalCost').innerText = 'Rp ' + totalCost.toLocaleString('id-ID');
    }

    document.addEventListener('DOMContentLoaded', function() {
        recalculateTotal();
    });
</script>
@endpush