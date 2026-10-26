@extends('layouts.app')

@push('styles')
<style>
    /* Ensure Leaflet container always fills its panel */
    #surabayaMap {
        width: 100% !important;
        height: 100% !important;
        min-height: 100% !important;
    }

    /* Marker styling */
    .depot-marker-pin {
        cursor: pointer !important;
        transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .depot-marker-pin:hover {
        transform: scale(1.15) translateY(-2px);
        z-index: 1000 !important;
    }

    .depot-active-card {
        border-color: #0891b2 !important;
        box-shadow: 0 10px 25px -5px rgba(8, 145, 178, 0.15) !important;
    }
</style>
@section('body_class', 'lg:overflow-hidden lg:h-screen')
@section('main_class', 'lg:overflow-hidden lg:h-[calc(100vh-4rem)] min-h-0')

@section('content')
<div class="flex-1 flex flex-col lg:flex-row w-full h-full min-h-0 overflow-hidden relative">

    <!-- MOBILE VIEW TOGGLER BAR (Visible only on < lg screens) -->
    <div class="lg:hidden sticky top-16 z-30 bg-white/95 backdrop-blur-md px-4 py-2.5 border-b border-slate-200 flex items-center justify-between gap-2 shadow-sm">
        <span class="text-xs font-bold text-slate-800">
            Ditemukan <strong>{{ $depots->count() }} Depot</strong> di Surabaya
        </span>
        <div class="flex items-center bg-slate-100 p-1 rounded-xl">
            <button type="button"
                    id="mobileTabList"
                    onclick="switchMobileTab('list')"
                    class="px-3 py-1 rounded-lg text-xs font-bold bg-white text-slate-900 shadow-sm transition">
                📋 Daftar
            </button>
            <button type="button"
                    id="mobileTabMap"
                    onclick="switchMobileTab('map')"
                    class="px-3 py-1 rounded-lg text-xs font-bold text-slate-500 hover:text-slate-800 transition">
                🗺️ Peta
            </button>
        </div>
    </div>

    <!-- LEFT PANEL: Search, Filters & Depot Cards List (Scrollable) -->
    <div id="leftListPanel"
         class="w-full lg:w-[50%] xl:w-[48%] h-full flex flex-col min-h-0 bg-slate-50 border-r border-slate-200/80 overflow-y-auto">

        <!-- Sticky Header Filter Area -->
        <div class="p-4 sm:p-5 bg-white border-b border-slate-200/80 sticky top-0 lg:top-0 z-20 shadow-sm">

            <div class="flex items-center justify-between gap-3 mb-3">
                <div>
                    <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                        <span>Depot Air Minum</span>
                        <span class="text-brand-600">Surabaya</span>
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        DAMIU terakreditasi higienis & lulus uji berkala Dinkes Kota Surabaya
                    </p>
                </div>

                <!-- GPS Locate Button -->
                <button type="button"
                        id="btnLocateMe"
                        onclick="detectUserLocation()"
                        title="Gunakan posisi GPS saat ini untuk mencari depot terdekat"
                        class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-brand-50 hover:bg-brand-100 text-brand-700 text-xs font-bold border border-brand-200 transition shadow-sm flex-shrink-0">
                    <svg class="w-4 h-4 text-brand-600 animate-spin hidden" id="gpsSpinner" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <svg class="w-4 h-4 text-brand-600" id="gpsIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span id="gpsBtnText">GPS Saya</span>
                </button>
            </div>

            <!-- Search Form -->
            <form action="{{ route('depots.index') }}" method="GET" id="filterForm" class="space-y-2.5">
                <div class="relative">
                    <input type="text"
                           name="search"
                           value="{{ $currentSearch }}"
                           placeholder="Cari nama depot, jalan, atau kecamatan (Gubeng, Sukolilo, Rungkut...)"
                           class="w-full pl-10 pr-16 py-2.5 bg-slate-100/90 border border-slate-200 rounded-xl text-xs sm:text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    @if($currentSearch)
                        <a href="{{ route('depots.index') }}" class="absolute right-3.5 top-2.5 text-slate-400 hover:text-slate-700 text-xs font-semibold">
                            Reset
                        </a>
                    @endif
                </div>

                <!-- Hidden inputs for preserving GPS lat/lng across filters -->
                <input type="hidden" name="lat" id="formLat" value="{{ $userLat }}">
                <input type="hidden" name="lng" id="formLng" value="{{ $userLng }}">
            </form>
        </div>

        <!-- GPS Alert Notice (if active) -->
        <div id="gpsAlertBox" class="hidden px-5 py-2.5 bg-blue-50 border-b border-blue-200 text-blue-900 text-xs font-medium flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-ping"></span>
                <span id="gpsAlertText">GPS Aktif: Mengurutkan depot berdasarkan jarak ke posisi Anda</span>
            </div>
            <button type="button" onclick="clearGpsLocation()" class="text-blue-700 hover:text-blue-900 font-bold underline text-[11px]">Hapus</button>
        </div>

        <!-- Depot Cards List -->
        <div class="p-4 sm:p-5 pb-20 space-y-3.5">
            <div class="flex items-center justify-between text-xs text-slate-400 px-1 font-medium">
                <span>Menampilkan <strong>{{ $depots->count() }} depot</strong> di Surabaya</span>
                <span class="hidden sm:inline">Klik kartu untuk sorot di peta &rarr;</span>
            </div>

            @forelse($depots as $depot)
                <div id="depotCardWrapper_{{ $depot->id }}"
                     class="cursor-pointer transition-all rounded-3xl"
                     onclick="focusOnMapPin({{ $depot->id }}, {{ $depot->lat }}, {{ $depot->lng }})">
                    @include('components.depot-card', ['depot' => $depot])
                </div>
            @empty
                <div class="bg-white rounded-3xl p-8 text-center border border-dashed border-slate-300">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Tidak ada depot yang cocok</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Coba ubah kata kunci pencarian atau sesuaikan filter kecamatan dan jenis air.</p>
                    <a href="{{ route('depots.index') }}" class="inline-block mt-4 px-4 py-2 rounded-xl text-xs font-bold bg-brand-600 text-white">Reset Semua Filter</a>
                </div>
            @endforelse
        </div>

    </div>

    <!-- RIGHT PANEL: Full Interactive Leaflet Map (Fixed & Non-scrolling) -->
    <div id="rightMapPanel"
         class="hidden lg:block w-full lg:w-[50%] xl:w-[52%] h-full relative bg-slate-100 flex-shrink-0 overflow-hidden">

        <!-- The Map Container -->
        <div id="surabayaMap" class="w-full h-full z-10"></div>

        <!-- Floating Quick Controls Overlay (Top-Left) -->
        <div class="absolute top-4 left-4 z-20 flex flex-col gap-2">
            <button type="button"
                    onclick="resetSurabayaMapView()"
                    class="bg-white/95 hover:bg-white backdrop-blur-md px-3.5 py-2 rounded-2xl shadow-md border border-slate-200/90 text-xs font-bold text-slate-800 flex items-center gap-2 transition">
                <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                </svg>
                <span>Pusatkan Surabaya</span>
            </button>
        </div>

        <!-- Legend Overlay at bottom-right -->
        <div class="absolute bottom-6 right-6 z-20 bg-white/95 backdrop-blur-md px-4 py-3 rounded-2xl shadow-lg border border-slate-200/80 text-xs space-y-1.5 hidden sm:block">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Status Pin DAMIU</div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500 border border-white shadow-sm"></span>
                <span class="text-slate-700 font-semibold">Lulus Uji Dinkes (SLHS Aktif)</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-cyan-500 border border-white shadow-sm"></span>
                <span class="text-slate-700 font-semibold">Proses Perpanjangan</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-blue-600 border border-white shadow-sm"></span>
                <span class="text-slate-700 font-semibold">Titik GPS Anda</span>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    // Depot dataset serialized from controller
    const mapDepots = @json($mapDepots);
    let map = null;
    let markersLayer = null;
    let userMarker = null;
    let depotMarkersMap = {};
    const defaultCenter = [-7.2800, 112.7600];

    document.addEventListener('DOMContentLoaded', function () {
        initSurabayaMap();
        renderDepotMarkers(mapDepots);

        // Check if lat/lng already present in URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('lat') && urlParams.get('lng')) {
            showUserPosition(parseFloat(urlParams.get('lat')), parseFloat(urlParams.get('lng')), false);
        }

        // Force resize recalculation in case container layout adjusted
        setTimeout(function() {
            if (map) {
                map.invalidateSize();
            }
        }, 300);

        window.addEventListener('resize', function() {
            if (map) map.invalidateSize();
        });
    });

    function initSurabayaMap() {
        const container = document.getElementById('surabayaMap');
        if (!container) return;

        // Ensure Leaflet is loaded
        if (typeof L === 'undefined') {
            console.error('Leaflet JS is not loaded yet');
            return;
        }

        markersLayer = L.layerGroup();

        map = L.map('surabayaMap', {
            center: defaultCenter,
            zoom: 13,
            zoomControl: false
        });

        // Zoom control on bottom-left
        L.control.zoom({ position: 'bottomleft' }).addTo(map);

        // Official OpenStreetMap standard raster tiles (No watermarks, clean raster)
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        markersLayer.addTo(map);
    }

    function renderDepotMarkers(depots) {
        if (!map || !markersLayer) return;
        markersLayer.clearLayers();
        depotMarkersMap = {};

        if (!depots || depots.length === 0) return;

        const bounds = [];

        depots.forEach(depot => {
            const isCertified = depot.is_certified;
            const pinBg = isCertified ? 'linear-gradient(135deg, #059669, #10b981)' : 'linear-gradient(135deg, #0891b2, #06b6d4)';
            const labelText = isCertified ? '✓ DINKES' : 'DAMIU';

            // Explicit dimensions so Leaflet creates a proper clickable hit-box
            const customIcon = L.divIcon({
                className: 'depot-marker-wrapper',
                html: `
                    <div class="depot-marker-pin" style="width: 100px; text-align: center;">
                        <div style="background: ${pinBg}; color: white; padding: 4px 8px; border-radius: 9999px; font-size: 11px; font-weight: 800; border: 2px solid white; box-shadow: 0 4px 14px rgba(0,0,0,0.22); display: inline-flex; align-items: center; gap: 4px;">
                            <span>${labelText}</span>
                            <span style="background: rgba(255,255,255,0.28); padding: 1px 5px; border-radius: 9999px; font-size: 10px;">★ ${depot.rating}</span>
                        </div>
                        <div style="width: 0; height: 0; border-left: 6px solid transparent; border-right: 6px solid transparent; border-top: 7px solid ${isCertified ? '#059669' : '#0891b2'}; margin: -1px auto 0;"></div>
                    </div>
                `,
                iconSize: [100, 36],
                iconAnchor: [50, 36],
                popupAnchor: [0, -38]
            });

            const marker = L.marker([depot.lat, depot.lng], { icon: customIcon });

            // Popup HTML
            const popupContent = `
                <div style="font-family: 'Plus Jakarta Sans', sans-serif; width: 250px; padding: 12px;">
                    <div style="position: relative; border-radius: 12px; overflow: hidden; height: 110px; margin-bottom: 10px;">
                        <img src="${depot.cover_image}"
                             onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1550989460-0adf9ea622e2?auto=format&fit=crop&w=800&q=80';"
                             style="width: 100%; height: 100%; object-fit: cover;"
                             alt="${depot.name}" />
                        <span style="position: absolute; bottom: 6px; left: 6px; background: rgba(15,23,42,0.85); color: white; padding: 2px 7px; border-radius: 6px; font-size: 10px; font-weight: 700;">
                            📍 ${depot.district}
                        </span>
                    </div>
                    <h4 style="font-weight: 800; font-size: 13px; color: #0f172a; margin: 0 0 6px 0; line-height: 1.35;">
                        ${depot.name}
                    </h4>
                    <div style="display: flex; gap: 8px; font-size: 11px; margin-bottom: 10px; padding: 4px 8px; background: #f8fafc; border-radius: 8px;">
                        <span style="color: #059669; font-weight: 700;">TDS: ${depot.tds_ppm} ppm</span>
                        <span style="color: #cbd5e1;">•</span>
                        <span style="color: #0891b2; font-weight: 700;">pH: ${depot.ph_level}</span>
                    </div>
                    <div style="display: flex; gap: 6px;">
                        <a href="https://www.google.com/maps/dir/?api=1&destination=${depot.lat},${depot.lng}&travelmode=driving"
                           target="_blank"
                           rel="noopener noreferrer"
                           style="flex: 1; text-align: center; background: #f1f5f9; color: #1e293b; padding: 7px 4px; border-radius: 10px; font-size: 11px; font-weight: 700; text-decoration: none; border: 1px solid #e2e8f0;">
                            📍 G-Maps
                        </a>
                        <a href="${depot.order_url}"
                           style="flex: 1; text-align: center; background: #0891b2; color: white; padding: 7px 4px; border-radius: 10px; font-size: 11px; font-weight: 700; text-decoration: none; box-shadow: 0 2px 8px rgba(8,145,178,0.3);">
                            Pesan Refill
                        </a>
                    </div>
                </div>
            `;

            marker.bindPopup(popupContent, { maxWidth: 290, className: 'modern-map-popup' });
            markersLayer.addLayer(marker);

            bounds.push([depot.lat, depot.lng]);
            depotMarkersMap[depot.id] = marker;
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [50, 50], maxZoom: 14 });
        }
    }

    // Fly to depot when user clicks card
    window.focusOnMapPin = function(depotId, lat, lng) {
        // If on mobile, switch to map view
        if (window.innerWidth < 1024) {
            switchMobileTab('map');
        }

        // Highlight card
        document.querySelectorAll('[id^="depotCardWrapper_"]').forEach(el => {
            el.classList.remove('depot-active-card');
        });
        const activeCard = document.getElementById(`depotCardWrapper_${depotId}`);
        if (activeCard) {
            activeCard.classList.add('depot-active-card');
        }

        if (map && depotMarkersMap[depotId]) {
            map.flyTo([lat, lng], 15, { duration: 1.2 });
            setTimeout(() => {
                depotMarkersMap[depotId].openPopup();
            }, 500);
        }
    };

    window.resetSurabayaMapView = function() {
        if (!map) return;
        if (mapDepots && mapDepots.length > 0) {
            const bounds = mapDepots.map(d => [d.lat, d.lng]);
            map.fitBounds(bounds, { padding: [50, 50], maxZoom: 14 });
        } else {
            map.setView(defaultCenter, 13);
        }
    };

    // Mobile list vs map tab switcher
    window.switchMobileTab = function(tab) {
        const listPanel = document.getElementById('leftListPanel');
        const mapPanel = document.getElementById('rightMapPanel');
        const btnList = document.getElementById('mobileTabList');
        const btnMap = document.getElementById('mobileTabMap');

        if (tab === 'map') {
            listPanel.classList.add('hidden');
            mapPanel.classList.remove('hidden');
            mapPanel.classList.add('block');
            btnMap.classList.add('bg-white', 'text-slate-900', 'shadow-sm');
            btnMap.classList.remove('text-slate-500');
            btnList.classList.remove('bg-white', 'text-slate-900', 'shadow-sm');
            btnList.classList.add('text-slate-500');

            setTimeout(() => {
                if (map) map.invalidateSize();
            }, 100);
        } else {
            mapPanel.classList.add('hidden');
            mapPanel.classList.remove('block');
            listPanel.classList.remove('hidden');
            btnList.classList.add('bg-white', 'text-slate-900', 'shadow-sm');
            btnList.classList.remove('text-slate-500');
            btnMap.classList.remove('bg-white', 'text-slate-900', 'shadow-sm');
            btnMap.classList.add('text-slate-500');
        }
    };

    // Real Browser GPS Geolocation
    window.detectUserLocation = function() {
        const btnText = document.getElementById('gpsBtnText');
        const spinner = document.getElementById('gpsSpinner');
        const icon = document.getElementById('gpsIcon');

        if (!navigator.geolocation) {
            alert('Browser Anda tidak mendukung fitur GPS Geolocation.');
            return;
        }

        btnText.innerText = 'Mencari...';
        spinner.classList.remove('hidden');
        icon.classList.add('hidden');

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const userLat = position.coords.latitude;
                const userLng = position.coords.longitude;

                btnText.innerText = 'GPS Aktif';
                spinner.classList.add('hidden');
                icon.classList.remove('hidden');

                // Update form lat & lng
                document.getElementById('formLat').value = userLat;
                document.getElementById('formLng').value = userLng;

                // Render user marker on map
                showUserPosition(userLat, userLng, true);

                // Auto reload with query params for calculating distances
                const url = new URL(window.location.href);
                url.searchParams.set('lat', userLat);
                url.searchParams.set('lng', userLng);
                url.searchParams.set('sort', 'distance_asc');
                window.location.href = url.toString();
            },
            function(error) {
                btnText.innerText = 'GPS Gagal';
                spinner.classList.add('hidden');
                icon.classList.remove('hidden');

                let msg = 'Tidak dapat mendeteksi lokasi GPS.';
                if (error.code === error.PERMISSION_DENIED) {
                    msg = 'Izin akses lokasi GPS ditolak di browser Anda. Menampilkan posisi standar Surabaya.';
                }
                alert(msg);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    };

    function showUserPosition(lat, lng, fly = true) {
        if (!map) return;
        if (userMarker) {
            map.removeLayer(userMarker);
        }

        const userIcon = L.divIcon({
            className: 'user-pulse-icon',
            html: `
                <div style="position: relative; width: 22px; height: 22px;">
                    <div class="pulse-marker" style="width: 22px; height: 22px; background: #2563eb; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 12px rgba(37,99,235,0.6);"></div>
                </div>
            `,
            iconSize: [22, 22],
            iconAnchor: [11, 11]
        });

        userMarker = L.marker([lat, lng], { icon: userIcon }).addTo(map);
        userMarker.bindPopup('<b style="font-size:12px; font-family:sans-serif;">📍 Lokasi GPS Anda Saat Ini</b>').openPopup();

        document.getElementById('gpsAlertBox').classList.remove('hidden');

        if (fly) {
            map.flyTo([lat, lng], 14, { duration: 1.2 });
        }
    }

    window.clearGpsLocation = function() {
        const url = new URL(window.location.href);
        url.searchParams.delete('lat');
        url.searchParams.delete('lng');
        url.searchParams.delete('sort');
        window.location.href = url.toString();
    };
</script>
@endpush
