

<div class="group bg-white rounded-3xl border border-slate-200/80 hover:border-brand-500/50 hover:shadow-xl hover:shadow-brand-500/5 transition-all duration-300 overflow-hidden flex flex-col justify-between"
     data-depot-id="{{ $depot->id }}"
     data-lat="{{ $depot->lat }}"
     data-lng="{{ $depot->lng }}">

    <div>
        <!-- Card Image Header -->
        <div class="relative h-44 w-full overflow-hidden bg-slate-100">
            <img src="{{ asset('images/images.jpg') }}"
                 alt="{{ $depot->name }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                 loading="lazy">

            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-transparent to-transparent"></div>

            <!-- Badges overlay -->
            <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
                @if($depot->is_certified)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-500/95 text-white shadow-sm backdrop-blur-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        SLHS Dinkes Grade {{ substr($depot->grade ?? 'A', 0, 1) }}
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-500/90 text-white backdrop-blur-sm">
                        Proses Verifikasi
                    </span>
                @endif
            </div>

            <!-- Open status -->
            <div class="absolute top-3 right-3">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $depot->is_open ? 'bg-slate-900/80 text-emerald-400' : 'bg-slate-900/80 text-rose-400' }} backdrop-blur-sm">
                    <span class="w-1.5 h-1.5 rounded-full {{ $depot->is_open ? 'bg-emerald-400' : 'bg-rose-400' }}"></span>
                    {{ $depot->is_open ? 'Buka' : 'Tutup' }}
                </span>
            </div>

            <!-- Distance & District overlay at bottom of image -->
            <div class="absolute bottom-2.5 left-3 right-3 flex items-center justify-between text-white text-xs font-semibold">
                <span class="px-2 py-0.5 rounded-lg bg-slate-900/60 backdrop-blur-sm">
                    📍 {{ $depot->district }}, Surabaya
                </span>
                @if(isset($depot->distance_km))
                    <span class="px-2 py-0.5 rounded-lg bg-brand-600/90 text-white backdrop-blur-sm font-bold">
                        {{ $depot->distance_km }} km
                    </span>
                @endif
            </div>
        </div>

        <!-- Content Body -->
        <div class="p-4 sm:p-5">
            <!-- Title -->
            <div class="flex items-start justify-between gap-2">
                <h3 class="font-bold text-slate-900 text-base leading-snug group-hover:text-brand-700 transition">
                    <a href="{{ route('depots.show', $depot->slug) }}">
                        {{ $depot->name }}
                    </a>
                </h3>
            </div>

            <!-- Address excerpt -->
            <p class="text-xs text-slate-500 mt-1 line-clamp-1">
                {{ $depot->address }}
            </p>

            <!-- Lab Water Metrics Badge Strip -->
            <div class="mt-3.5 p-2.5 rounded-2xl bg-slate-50 border border-slate-100 grid grid-cols-3 gap-1 text-center">
                <div class="border-r border-slate-200/80 pr-1">
                    <span class="block text-[10px] text-slate-400 font-semibold uppercase">TDS Air</span>
                    <span class="text-xs font-extrabold text-brand-700 font-mono">{{ $depot->tds_ppm ?? '-' }} ppm</span>
                </div>
                <div class="border-r border-slate-200/80 px-1">
                    <span class="block text-[10px] text-slate-400 font-semibold uppercase">Tingkat pH</span>
                    <span class="text-xs font-extrabold text-emerald-700 font-mono">{{ $depot->ph_level ?? '-' }}</span>
                </div>
                <div class="pl-1">
                    <span class="block text-[10px] text-slate-400 font-semibold uppercase">E. Coli</span>
                    <span class="text-[11px] font-extrabold text-emerald-600 truncate block">0 CFU (Aman)</span>
                </div>
            </div>

            <!-- Products Pills -->
            <div class="mt-3 flex flex-wrap gap-1">
                @foreach($depot->products as $prod)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-slate-100 text-slate-600">
                        {{ $prod->name }} • Rp {{ number_format($prod->price, 0, ',', '.') }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Card Bottom Footer / CTAs -->
    <div class="p-4 sm:p-5 pt-0 border-t border-slate-100 mt-2">
        <div class="flex items-center justify-between pt-3">
            <div>
                <span class="block text-[10px] text-slate-400 font-medium">Harga Isi Ulang</span>
                <span class="text-sm font-extrabold text-slate-900">
                    Rp {{ number_format($depot->products->min('price') ?? 6000, 0, ',', '.') }}
                    <span class="text-[10px] font-normal text-slate-500">/ galon</span>
                </span>
            </div>

            <div class="flex items-center gap-1.5">
                <!-- Google Maps Direction Button -->
                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $depot->lat }},{{ $depot->lng }}&travelmode=driving"
                   target="_blank"
                   rel="noopener noreferrer"
                   title="Buka Rute di Google Maps"
                   class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition"
                   onclick="event.stopPropagation();">
                    <svg class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                </a>

                <!-- Detail Button -->
                <a href="{{ route('depots.show', $depot->slug) }}"
                   class="px-3 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                    Detail
                </a>

                <!-- Order Pickup Button -->
                <a href="{{ route('orders.create', $depot->id) }}"
                   class="px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 shadow-md shadow-brand-500/20 transition flex items-center gap-1">
                    <span>Pesan</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
    </div>

</div>
