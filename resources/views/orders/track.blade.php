@extends('layouts.app', ['title' => 'Lacak Antrean ' . $order->order_number . ' - Galonku'])

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

    <!-- Back Navigation Buttons -->
    <div class="mb-5 flex items-center justify-between">
        <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:text-brand-600 hover:border-brand-300 shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Daftar Antrean Saya</span>
        </a>

        <a href="{{ route('depots.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
            Peta Depot &rarr;
        </a>
    </div>

    <!-- Status notification if simulation triggered -->
    @if(session('status_updated'))
        <div class="mb-6 bg-cyan-50 border border-cyan-200 text-cyan-900 p-4 rounded-2xl flex items-center gap-3">
            <span class="w-2.5 h-2.5 rounded-full bg-cyan-500 animate-pulse"></span>
            <p class="text-xs sm:text-sm font-bold">{{ session('status_updated') }}</p>
        </div>
    @endif

    <!-- MAIN QUEUE CARD -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-lg overflow-hidden mb-6">

        <!-- Header Strip -->
        <div class="p-6 sm:p-8 bg-gradient-to-r from-slate-900 via-brand-900 to-slate-900 text-white relative">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span class="text-xs font-mono font-semibold text-cyan-300 uppercase tracking-widest block mb-1">TIKET ANTREAN REFILL</span>
                    <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-white font-mono">{{ $order->queue_number }}</h1>
                    <p class="text-xs text-slate-300 mt-1">Nomor Pesanan: <span class="font-mono text-white">{{ $order->order_number }}</span></p>
                </div>

                <div class="sm:text-right bg-white/10 backdrop-blur-md px-5 py-3 rounded-2xl border border-white/10">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-300 block">PIN Ambil Galon</span>
                    <span class="text-2xl font-black font-mono tracking-widest text-amber-300">{{ $order->pickup_pin }}</span>
                    <span class="text-[10px] text-slate-300 block mt-0.5">Tunjukkan ke kasir depot</span>
                </div>
            </div>
        </div>

        <!-- 4-STEP VISUAL PROGRESS STEPPER -->
        <div class="p-6 sm:p-8 border-b border-slate-100">
            @php
                $steps = [
                    ['id' => 'DITERIMA', 'label' => '1. Pesanan Diterima', 'desc' => 'Tercatat di sistem (Menunggu)'],
                    ['id' => 'SEDANG_DIISI', 'label' => '2. Sedang Diproses', 'desc' => 'Sterilisasi UV & Isi Air'],
                    ['id' => 'SIAP_DIAMBIL', 'label' => '3. Siap Diambil', 'desc' => 'Menunggu Pengambilan'],
                    ['id' => 'SELESAI', 'label' => '4. Selesai', 'desc' => 'Pesanan Selesai']
                ];
                $statusOrder = ['DITERIMA' => 1, 'SEDANG_DIISI' => 2, 'SIAP_DIAMBIL' => 3, 'SELESAI' => 4];
                $currentStepNum = $statusOrder[$order->status] ?? 1;
            @endphp

            <div class="relative">
                <!-- Line background -->
                <div class="hidden sm:block absolute top-1/2 left-0 right-0 h-1 bg-slate-100 -translate-y-1/2 z-0"></div>
                <!-- Active Line -->
                <div class="hidden sm:block absolute top-1/2 left-0 h-1 bg-brand-600 -translate-y-1/2 z-0 transition-all duration-500"
                     style="width: {{ (($currentStepNum - 1) / 3) * 100 }}%;"></div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 relative z-10">
                    @foreach($steps as $idx => $step)
                        @php
                            $stepIndex = $idx + 1;
                            $isPassed = $stepIndex < $currentStepNum;
                            $isCurrent = $stepIndex === $currentStepNum;
                        @endphp
                        <div class="flex flex-col sm:items-center text-left sm:text-center">
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-bold text-sm mb-2 transition-all {{ $isCurrent ? 'bg-brand-600 text-white ring-4 ring-brand-100 shadow-md scale-110' : ($isPassed ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-400') }}">
                                @if($isPassed)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                @else
                                    {{ $stepIndex }}
                                @endif
                            </div>
                            <span class="text-xs font-bold {{ $isCurrent ? 'text-brand-700' : ($isPassed ? 'text-slate-800' : 'text-slate-400') }}">
                                {{ $step['label'] }}
                            </span>
                            <span class="text-[10px] text-slate-400 hidden sm:block">
                                {{ $step['desc'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Status Banner Message -->
            <div class="mt-6 p-4 rounded-2xl bg-slate-50 border border-slate-200 text-slate-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full {{ $order->status === 'SEDANG_DIISI' ? 'bg-amber-500 animate-pulse' : 'bg-brand-600' }}"></span>
                    <div>
                        <span class="text-xs font-bold block">Status Saat Ini: {{ $order->status === 'SEDANG_DIISI' ? 'Diproses (Sedang Disterilisasi & Diisi)' : 'Menunggu Antrean (Diterima)' }}</span>
                        <p class="text-[11px] text-slate-500">
                            @if($order->status === 'SEDANG_DIISI')
                                Galon sedang diproses di mesin sterilisasi otomatis oleh operator depot.
                            @else
                                Menunggu giliran antrean pengisian (Estimasi ambil: {{ $order->pickup_time_estimated ?? '15-30 Menit Lagi' }}).
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Simulation button: Toggles between DITERIMA and SEDANG_DIISI -->
                <form action="{{ route('orders.advance', $order->order_number) }}" method="POST">
                    @csrf
                    <button type="submit" title="Klik untuk simulasi status (Menunggu / Diproses)"
                            class="px-3 py-1.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 text-[10px] font-bold transition flex items-center gap-1 flex-shrink-0">
                        <span>Simulasi Status &rarr;</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- DESTINATION & GOOGLE MAPS NAVIGATION BUTTON -->
        <div class="p-6 sm:p-8 bg-slate-50/60 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Lokasi Pengambilan Depot</span>
                <h3 class="text-base font-extrabold text-slate-900">{{ $order->depot->name }}</h3>
                <p class="text-xs text-slate-500">{{ $order->depot->address }} (Kec. {{ $order->depot->district }})</p>
                <p class="text-xs text-emerald-600 font-semibold mt-1">Buka: {{ $order->depot->open_hours }}</p>
            </div>

            <!-- Direct Google Maps Turn-by-Turn Navigation -->
            <a href="{{ $googleMapsUrl }}"
               target="_blank"
               rel="noopener noreferrer"
               class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold shadow-md shadow-emerald-600/20 transition flex-shrink-0">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                </svg>
                <span>Buka Rute di Google Maps</span>
            </a>
        </div>

    </div>

    <!-- ORDER SUMMARY DETAILS -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
        <h3 class="text-sm font-bold text-slate-900 mb-4">Rincian Pesanan</h3>

        <div class="space-y-3 mb-6">
            <div class="flex items-center justify-between text-xs py-2 border-b border-slate-100">
                <div>
                    <span class="font-bold text-slate-900">{{ $order->water_product_name }}</span>
                    <span class="text-slate-400 block">{{ $order->quantity }} Galon x Rp {{ number_format($order->unit_price, 0, ',', '.') }}</span>
                </div>
                <span class="font-mono font-bold text-slate-800">Rp {{ number_format($order->unit_price * $order->quantity, 0, ',', '.') }}</span>
            </div>

            @if($order->gallon_option === 'new_gallon' || $order->gallon_fee > 0)
                <div class="flex items-center justify-between text-xs py-2 border-b border-slate-100">
                    <div>
                        <span class="font-bold text-slate-900">Beli Galon Baru Bersih</span>
                        <span class="text-slate-400 block">{{ $order->quantity }} Galon Baru</span>
                    </div>
                    <span class="font-mono font-bold text-slate-800">Rp {{ number_format($order->gallon_fee, 0, ',', '.') }}</span>
                </div>
            @endif

            <div class="flex items-center justify-between text-xs py-2 border-b border-slate-100 text-emerald-700 font-semibold">
                <span>Biaya Pengantaran</span>
                <span>Rp 0 (Self-Pickup di Lokasi)</span>
            </div>

            <div class="pt-2 flex items-center justify-between text-base">
                <span class="font-extrabold text-slate-900">Total Tagihan</span>
                <span class="text-xl font-black text-brand-700 font-mono">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs bg-slate-50 p-4 rounded-2xl">
            <div>
                <span class="text-slate-400 block font-medium">Nama Pemesan:</span>
                <span class="font-bold text-slate-800">{{ $order->customer_name }}</span>
            </div>
            <div>
                <span class="text-slate-400 block font-medium">Nomor WhatsApp:</span>
                <span class="font-bold text-slate-800 font-mono">{{ $order->customer_phone }}</span>
            </div>
            @if($order->notes)
                <div class="sm:col-span-2 pt-2 border-t border-slate-200">
                    <span class="text-slate-400 block font-medium">Catatan:</span>
                    <span class="text-slate-700">{{ $order->notes }}</span>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
