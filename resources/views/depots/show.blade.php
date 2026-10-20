@extends('layouts.app', ['title' => $depot->name . ' - Galonku Surabaya'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full">

    <!-- Back button -->
    <div class="mb-4">
        <a href="{{ route('depots.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Peta & Daftar Depot
        </a>
    </div>

    <!-- Review Success Notification -->
    @if(session('review_success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <p class="text-sm font-semibold">{{ session('review_success') }}</p>
        </div>
    @endif

    <!-- Hero Header Banner -->
    <div class="relative bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden mb-6">
        <div class="h-64 sm:h-80 w-full relative">
            <img src="{{ asset('images/images.jpg') }}"
                 alt="{{ $depot->name }}"
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/40 to-transparent"></div>

            <!-- Badges on top -->
            <div class="absolute top-4 left-4 sm:top-6 sm:left-6 flex flex-wrap gap-2">
                @if($depot->is_certified)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-500 text-white shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        SLHS Dinkes Surabaya: Terakreditasi {{ $depot->grade ?? 'Grade A' }}
                    </span>
                @endif
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-slate-900/80 backdrop-blur-sm text-emerald-400">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    {{ $depot->is_open ? 'Buka Sekarang (' . $depot->open_hours . ')' : 'Sedang Tutup' }}
                </span>
            </div>

            <!-- Header Info bottom -->
            <div class="absolute bottom-4 left-4 right-4 sm:bottom-6 sm:left-6 sm:right-6 text-white">
                <span class="text-xs font-bold uppercase tracking-wider text-cyan-300">DAMIU • {{ $depot->district }}, Kota Surabaya</span>
                <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight mt-1 text-white">{{ $depot->name }}</h1>
                <p class="text-xs sm:text-sm text-slate-200 mt-1 max-w-2xl">{{ $depot->tagline }}</p>
                <div class="flex flex-wrap items-center gap-4 mt-3 text-xs text-slate-300">
                    <span class="flex items-center gap-1 text-amber-400 font-bold">
                        ★ {{ number_format($depot->rating, 1) }}
                        <span class="text-slate-300 font-normal">({{ $depot->review_count }} Ulasan Warga)</span>
                    </span>
                    <span>•</span>
                    <span>📍 {{ $depot->address }}</span>
                </div>
            </div>
        </div>

        <!-- Quick Action Bar -->
        <div class="p-4 sm:p-6 bg-slate-50 border-t border-slate-100 flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3">
                <!-- Google Maps Direction -->
                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $depot->lat }},{{ $depot->lng }}&travelmode=driving"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white border border-slate-200 text-slate-800 text-xs font-bold hover:bg-slate-100 shadow-sm transition">
                    <svg class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                    Buka Rute di Google Maps
                </a>

                <!-- WhatsApp Contact -->
                @if($depot->whatsapp)
                    <a href="https://wa.me/{{ $depot->whatsapp }}?text=Halo%20{{ urlencode($depot->name) }},%20saya%20ingin%20tanya%20jadwal%20isi%20ulang%20air%20galon."
                       target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold hover:bg-emerald-100 transition">
                        <svg class="w-4 h-4 fill-current text-emerald-600" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.301-.15-1.78-.879-2.056-.98-.276-.1-.476-.15-.676.15-.2.3-.776.98-.952 1.18-.175.2-.351.226-.652.075-.301-.15-1.27-.468-2.42-1.493-.895-.798-1.5-1.784-1.676-2.084-.175-.3-.018-.462.132-.612.136-.134.301-.35.452-.525.15-.175.2-.3.301-.5.1-.2.05-.376-.025-.526-.075-.15-.676-1.63-.927-2.23-.244-.585-.492-.506-.676-.515l-.577-.01c-.2 0-.526.075-.802.375-.276.3-1.053 1.03-1.053 2.512s1.078 2.91 1.228 3.11c.15.2 2.122 3.24 5.14 4.544.718.31 1.279.495 1.716.634.721.23 1.378.197 1.897.12.578-.087 1.78-.727 2.03-1.43.251-.703.251-1.305.176-1.43-.075-.125-.276-.201-.577-.35z"/>
                        </svg>
                        Hubungi WhatsApp
                    </a>
                @endif
            </div>

            <!-- Order Refill Button -->
            <a href="{{ route('orders.create', $depot->id) }}"
               class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold shadow-lg shadow-brand-500/25 transition">
                <span>Pesan Refill Pickup (Ambil Sendiri)</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>
    </div>

    <!-- Main 2-Column Content Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- LEFT COLUMN (2 Cols): SLHS Certificate, Lab Tests, Facilities & Map -->
        <div class="lg:col-span-2 space-y-6">

            <!-- 1. DINKES SURABAYA SLHS CERTIFICATE CARD -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm relative overflow-hidden">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Sertifikat Laik Higiene Sanitasi (SLHS)</h2>
                            <p class="text-xs text-slate-500">Diterbitkan oleh Dinas Kesehatan Kota Surabaya</p>
                        </div>
                    </div>

                    <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800">
                        {{ $depot->certification_status }}
                    </span>
                </div>

                <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                    <div>
                        <span class="block text-[11px] font-semibold text-slate-400 uppercase">Nomor SLHS</span>
                        <span class="text-xs font-extrabold text-slate-800 font-mono">{{ $depot->slhs_number }}</span>
                    </div>
                    <div>
                        <span class="block text-[11px] font-semibold text-slate-400 uppercase">Masa Berlaku</span>
                        <span class="text-xs font-bold text-slate-800">{{ $depot->issued_date }} - {{ $depot->expiry_date }}</span>
                    </div>
                    <div>
                        <span class="block text-[11px] font-semibold text-slate-400 uppercase">Grade Sanitasi</span>
                        <span class="text-xs font-extrabold text-emerald-700">{{ $depot->grade }}</span>
                    </div>
                </div>
            </div>

            <!-- 2. LAB TEST QUALITY REPORT CARD (BBLK SURABAYA) -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Hasil Uji Laboratorium Air Minum</h2>
                        <p class="text-xs text-slate-500">Lembaga Penguji: <span class="font-semibold text-slate-700">{{ $depot->lab_name }}</span></p>
                    </div>
                    <span class="text-xs text-slate-400">Uji Terakhir: <strong>{{ $depot->last_tested_date }}</strong></span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <!-- TDS -->
                    <div class="p-4 rounded-2xl bg-cyan-50/60 border border-cyan-100 text-center">
                        <span class="text-[10px] font-bold text-cyan-800 uppercase tracking-wide">TDS (Zat Terlarut)</span>
                        <div class="text-2xl font-black text-cyan-900 font-mono mt-1">{{ $depot->tds_ppm }} <span class="text-xs font-bold">ppm</span></div>
                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-cyan-200/80 text-cyan-900">
                            Batas Aman &lt; 300
                        </span>
                    </div>

                    <!-- pH Level -->
                    <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100 text-center">
                        <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wide">Tingkat Keasaman (pH)</span>
                        <div class="text-2xl font-black text-emerald-900 font-mono mt-1">{{ $depot->ph_level }}</div>
                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-200/80 text-emerald-900">
                            Netral & Sehat (6.5 - 8.5)
                        </span>
                    </div>

                    <!-- E. Coli -->
                    <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100 text-center">
                        <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wide">Bakteri E. Coli</span>
                        <div class="text-sm font-black text-emerald-900 mt-2.5">{{ $depot->ecoli_status }}</div>
                        <span class="inline-block mt-2 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-200/80 text-emerald-900">
                            100% Negatif Bebas Bakteri
                        </span>
                    </div>

                    <!-- Coliform -->
                    <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100 text-center">
                        <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wide">Total Coliform</span>
                        <div class="text-sm font-black text-emerald-900 mt-2.5">{{ $depot->coliform_status }}</div>
                        <span class="inline-block mt-2 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-200/80 text-emerald-900">
                            Standar Baku Mutu Terpenuhi
                        </span>
                    </div>
                </div>
            </div>

            <!-- 3. FACILITIES & PURIFICATION TECHNOLOGY -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                <h2 class="text-base font-bold text-slate-900 mb-3">Teknologi Penyaringan & Standar Higienis</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    @foreach($depot->facilities ?? [] as $facility)
                        <div class="flex items-center gap-2.5 p-3 rounded-2xl bg-slate-50 border border-slate-100 text-xs font-semibold text-slate-700">
                            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ $facility }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- 4. INTERACTIVE MINI MAP & DIRECTIONS -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Lokasi Depot di Surabaya</h2>
                        <p class="text-xs text-slate-500">{{ $depot->address }}</p>
                    </div>
                    <a href="https://www.google.com/maps/dir/?api=1&destination={{ $depot->lat }},{{ $depot->lng }}&travelmode=driving"
                       target="_blank"
                       class="text-xs font-bold text-brand-600 hover:text-brand-700">
                        Petunjuk Arah Google Maps &rarr;
                    </a>
                </div>

                <div id="detailMap" class="w-full h-64 rounded-2xl overflow-hidden border border-slate-200"></div>
            </div>

        </div>

        <!-- RIGHT COLUMN (1 Col): Water Products Menu, Order CTA, and Reviews -->
        <div class="space-y-6">

            <!-- Water Products & Ordering Box -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                <h2 class="text-base font-bold text-slate-900 mb-1">Daftar Jenis Air Minum</h2>
                <p class="text-xs text-slate-500 mb-4">Harga isi ulang galon standar (19 Liter) - Ambil Sendiri</p>

                <div class="space-y-3 mb-6">
                    @foreach($depot->products as $product)
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/70 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-extrabold text-slate-900 block">{{ $product->name }}</span>
                                <span class="text-[11px] text-slate-500">{{ $product->volume }} • Kategori: {{ strtoupper($product->type) }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-extrabold text-brand-700 block">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                <span class="text-[10px] text-slate-400">/ galon</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="p-3 rounded-2xl bg-emerald-50 border border-emerald-200/80 mb-5 flex items-center gap-2.5 text-xs text-emerald-900 font-medium">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Layanan Pickup: Galon dicuci & disterilkan UV sebelum diisi. Ongkos kirim: <strong>Rp 0</strong>.</span>
                </div>

                <a href="{{ route('orders.create', $depot->id) }}"
                   class="block w-full py-3.5 bg-brand-600 hover:bg-brand-700 text-white rounded-2xl text-center font-bold text-sm shadow-lg shadow-brand-500/25 transition">
                    Pesan Refill Galon Sekarang
                </a>
            </div>

            <!-- Customer Reviews Card -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-bold text-slate-900">Ulasan Higienitas Warga</h2>
                    <span class="text-xs font-bold text-amber-600">★ {{ number_format($depot->rating, 1) }} / 5.0</span>
                </div>

                <!-- Existing Reviews -->
                <div class="space-y-3.5 mb-6 max-h-96 overflow-y-auto pr-1">
                    @forelse($depot->reviews as $rev)
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 text-xs">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="font-bold text-slate-800">{{ $rev->user_name }}</span>
                                <span class="text-amber-500 font-bold">★ {{ number_format($rev->rating, 1) }}</span>
                            </div>
                            <p class="text-slate-600 leading-relaxed">{{ $rev->comment }}</p>
                            <div class="mt-2 flex flex-wrap gap-2 text-[10px] text-slate-400 font-medium">
                                <span>Kejernihan: {{ $rev->clarity_rating }}/5</span>
                                <span>•</span>
                                <span>Rasa: {{ $rev->taste_rating }}/5</span>
                                <span>•</span>
                                <span>Kebersihan: {{ $rev->cleanliness_rating }}/5</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Belum ada ulasan untuk depot ini. Jadilah yang pertama memberikan ulasan!</p>
                    @endforelse
                </div>

                <!-- Write Review Form -->
                <div class="pt-4 border-t border-slate-100">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-3">Tulis Ulasan & Penilaian Air</h3>
                    <form action="{{ route('reviews.store', $depot->id) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <input type="text" name="user_name" required placeholder="Nama Anda"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 mb-1">Rating Utama (1-5)</label>
                                <select name="rating" class="w-full px-2 py-1.5 text-xs rounded-xl border border-slate-200">
                                    <option value="5">★★★★★ (5.0 - Sempurna)</option>
                                    <option value="4">★★★★☆ (4.0 - Bagus)</option>
                                    <option value="3">★★★☆☆ (3.0 - Cukup)</option>
                                    <option value="2">★★☆☆☆ (2.0 - Kurang)</option>
                                    <option value="1">★☆☆☆☆ (1.0 - Buruk)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 mb-1">Kejernihan Air (1-5)</label>
                                <select name="clarity_rating" class="w-full px-2 py-1.5 text-xs rounded-xl border border-slate-200">
                                    <option value="5">5 - Sangat Jernih & Bening</option>
                                    <option value="4">4 - Jernih</option>
                                    <option value="3">3 - Sedikit Berbayang</option>
                                    <option value="2">2 - Keruh</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <textarea name="comment" rows="2" required placeholder="Ceritakan kesegaran rasa air, kejernihan, dan kebersihan depot..."
                                      class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500"></textarea>
                        </div>
                        <button type="submit" class="w-full py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition">
                            Kirim Ulasan Warga
                        </button>
                    </form>
                </div>

            </div>

        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const depotLat = {{ $depot->lat }};
        const depotLng = {{ $depot->lng }};

        const map = L.map('detailMap', {
            center: [depotLat, depotLng],
            zoom: 16,
            zoomControl: false
        });

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        const customIcon = L.divIcon({
            className: 'depot-pin',
            html: `
                <div style="display: flex; flex-direction: column; align-items: center; width: 140px; text-align: center;">
                    <div style="background: linear-gradient(135deg, #059669, #10b981); color: white; padding: 4px 10px; border-radius: 9999px; font-size: 11px; font-weight: 800; border: 2px solid white; box-shadow: 0 4px 12px rgba(0,0,0,0.22); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px;">
                        📍 {{ $depot->name }}
                    </div>
                    <div style="width: 0; height: 0; border-left: 6px solid transparent; border-right: 6px solid transparent; border-top: 7px solid #059669; margin: -1px auto 0;"></div>
                </div>
            `,
            iconSize: [140, 36],
            iconAnchor: [70, 36],
            popupAnchor: [0, -36]
        });

        const marker = L.marker([depotLat, depotLng], { icon: customIcon }).addTo(map);
        marker.bindPopup('<b style="font-family:sans-serif; font-size:12px;">{{ $depot->name }}</b><br><span style="font-size:11px; color:#64748b;">{{ $depot->address }}</span>').openPopup();

        setTimeout(function() {
            map.invalidateSize();
        }, 200);
    });
</script>
@endpush
