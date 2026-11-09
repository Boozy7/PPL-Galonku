@extends('layouts.app', ['title' => 'Antrean & Pesanan Saya - Galonku'])

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-brand-100 text-brand-800">
                    STATUS ANTREAN
                </span>
                <span class="text-xs text-slate-500">• Terhubung langsung ke Depot</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Antrean & Pesanan Saya</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Pantau progres pencucian UV, pengisian galon, dan kode PIN pengambilan tanpa perlu menunggu di lokasi depot.
            </p>
        </div>

        <a href="{{ route('depots.index') }}" 
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-brand-600 hover:bg-brand-700 text-white text-xs sm:text-sm font-bold shadow-md shadow-brand-500/20 transition self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Pesan Refill Baru</span>
        </a>
    </div>

    <!-- Error / Flash Message -->
    @if(session('error'))
        <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl flex items-center gap-3">
            <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-xs sm:text-sm font-medium">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Orders Cards List -->
    @if($orders->isNotEmpty())
        <div class="space-y-5 mb-10">
            @foreach($orders as $order)
                @php
                    $isReady = $order->status === 'SIAP_DIAMBIL';
                    $isProcessing = $order->status === 'SEDANG_DIISI';
                    $isReceived = $order->status === 'DITERIMA';
                    $isDone = $order->status === 'SELESAI';
                @endphp

                <div class="bg-white rounded-3xl border {{ $isReady ? 'border-emerald-300 ring-2 ring-emerald-100 shadow-lg' : 'border-slate-200/90 shadow-sm' }} overflow-hidden hover:shadow-md transition">
                    
                    <!-- Card Top Strip -->
                    <div class="p-4 sm:p-5 {{ $isReady ? 'bg-emerald-50/80 border-b border-emerald-100' : 'bg-slate-50 border-b border-slate-100' }} flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1.5 rounded-xl font-mono text-base sm:text-lg font-black {{ $isReady ? 'bg-emerald-600 text-white' : 'bg-brand-600 text-white' }} shadow-sm">
                                {{ $order->queue_number }}
                            </span>
                            <div>
                                <span class="text-[11px] font-mono font-medium text-slate-500 uppercase block">No. Pesanan: {{ $order->order_number }}</span>
                                <h2 class="text-sm sm:text-base font-extrabold text-slate-900">{{ $order->depot->name ?? 'Depot Galonku' }}</h2>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div>
                            @if($isReady)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                                    Siap Diambil!
                                </span>
                            @elseif($isProcessing)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                    Sedang Disterilisasi & Diisi
                                </span>
                            @elseif($isReceived)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-sky-100 text-sky-800 border border-sky-300">
                                    <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                                    Pesanan Diterima
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                    Selesai
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                        <div class="md:col-span-2 space-y-2">
                            <div class="flex items-center gap-2 text-xs text-slate-600">
                                <span class="font-bold text-slate-900">{{ $order->quantity }}x</span>
                                <span>{{ $order->water_product_name }}</span>
                                <span>•</span>
                                <span class="text-slate-500">
                                    {{ $order->gallon_option === 'new_gallon' ? 'Beli Galon Baru' : 'Bawa Galon Sendiri' }}
                                </span>
                            </div>

                            <p class="text-xs text-slate-500">
                                📍 {{ $order->depot->address ?? 'Kota Surabaya' }} (Kec. {{ $order->depot->district ?? '-' }})
                            </p>

                            <div class="flex items-center gap-4 text-xs pt-1">
                                <span class="text-slate-500">Total: <strong class="text-slate-900 font-extrabold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></span>
                                <span class="text-slate-300">•</span>
                                <span class="text-slate-500">Estimasi: <strong class="text-brand-700">{{ $order->pickup_time_estimated }}</strong></span>
                            </div>
                        </div>

                        <!-- Right Info & Action -->
                        <div class="flex flex-col sm:flex-row md:flex-col items-start md:items-end justify-between gap-3 pt-3 md:pt-0 border-t md:border-t-0 border-slate-100">
                            <!-- PIN Badge -->
                            <div class="bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-xl text-left md:text-right">
                                <span class="text-[10px] uppercase font-bold text-amber-700 block">PIN Ambil</span>
                                <span class="text-base font-black font-mono tracking-wider text-amber-900">{{ $order->pickup_pin }}</span>
                            </div>

                            <!-- Open Ticket Button -->
                            <a href="{{ route('orders.track', $order->order_number) }}" 
                               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl {{ $isReady ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-slate-900 hover:bg-brand-600 text-white' }} text-xs font-bold transition shadow-sm">
                                <span>Buka Tiket Antrean</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-3xl p-8 sm:p-12 text-center border border-slate-200/80 shadow-sm mb-10">
            <div class="w-16 h-16 rounded-3xl bg-brand-50 text-brand-600 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Belum Ada Antrean Aktif</h3>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-sm mx-auto">
                Anda belum melakukan pemesanan isi ulang galon hari ini. Temukan depot terdekat dan pesan tanpa perlu antre fisik!
            </p>
            <div class="mt-6">
                <a href="{{ route('depots.index') }}" 
                   class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-brand-600 hover:bg-brand-700 text-white text-xs sm:text-sm font-bold shadow-md shadow-brand-500/20 transition">
                    Lihat Peta Depot di Surabaya
                </a>
            </div>
        </div>
    @endif

    <!-- Quick Search Section (Flexible Queue Number / Order Number / Phone) -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-900">Cari Antrean Tertentu</h3>
                <p class="text-xs text-slate-500">Punya nomor antrean lain? Cukup ketik nomor antrean (contoh: <strong class="text-brand-700">#A-04</strong> atau <strong class="text-slate-700">A-04</strong>) atau nomor pesanan.</p>
            </div>
        </div>

        <form action="{{ route('orders.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2.5 mt-4">
            <input type="text" 
                   name="search" 
                   required
                   value="{{ request('search') }}"
                   placeholder="Ketik #A-04 atau GLK-20260928-XXXXX" 
                   class="flex-1 px-4 py-3 rounded-2xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm font-mono uppercase tracking-wide">
            
            <button type="submit" 
                    class="px-6 py-3 bg-brand-600 hover:bg-brand-700 text-white rounded-2xl font-bold text-sm shadow-md shadow-brand-500/20 transition flex-shrink-0">
                Lacak Tiket
            </button>
        </form>
    </div>

</div>
@endsection
