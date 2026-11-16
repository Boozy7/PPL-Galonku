@extends('layouts.app', ['title' => 'Lacak Antrean #' . $order->order_number . ' - Galonku'])

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

    <!-- Back Navigation Button -->
    <div class="mb-6">
        <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:text-brand-700 hover:border-brand-300 shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Daftar Antrean</span>
        </a>
    </div>

    <!-- MAIN ORDER CARD -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden mb-6">

        <!-- Header Strip -->
        <div class="bg-gradient-to-r from-slate-900 via-brand-900 to-slate-900 text-white p-6 relative">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400 block mb-1">TIKET ANTREAN REFILL</span>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white font-mono">#{{ $order->order_number }}</h1>
                    <p class="text-xs text-slate-300 mt-1">Waktu Pesan: {{ $order->created_at->format('d M Y, H:i') }}</p>
                </div>

                <div class="bg-white/10 backdrop-blur-md px-4 py-3 rounded-2xl border border-white/14">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-300 block">PIN Ambil Galon</span>
                    <span class="text-xl font-black font-mono tracking-widest text-amber-400">{{ $order->pickup_pin }}</span>
                    <span class="text-[10px] text-slate-300 block mt-0.5">Tunjukkan ke kasir depot</span>
                </div>
            </div>

            <!-- 4-STEP VISUAL PROGRESS STEPPER -->
            @php
                $steps = [
                    ['id' => 'MENUNGGU', 'label' => '1. Pesanan Diterima', 'desc' => 'Tercatat di sistem (Menunggu)'],
                    ['id' => 'SEDANG_DIPROSES', 'label' => '2. Sedang Diproses', 'desc' => 'Sterilisasi UV & Isi Air'],
                    ['id' => 'SIAP_DIAMBIL', 'label' => '3. Siap Diambil', 'desc' => 'Menunggu Pengambilan'],
                    ['id' => 'SELESAI', 'label' => '4. Selesai', 'desc' => 'Transaksi Selesai'],
                ];
                $statusOrder = ['MENUNGGU' => 1, 'SEDANG_DIPROSES' => 2, 'SIAP_DIAMBIL' => 3, 'SELESAI' => 4];
                $currentStep = $statusOrder[$order->status] ?? 1;
            @endphp

            <div class="mt-8 pt-6 border-t border-white/10">
                <div class="relative">
                    <div class="absolute top-1/2 left-0 -translate-y-1/2 h-1 bg-slate-700 w-full rounded-full"></div>
                    <div class="absolute top-1/2 left-0 -translate-y-1/2 h-1 bg-brand-400 rounded-full transition-all duration-500"
                         style="width: {{ (($currentStep - 1) / 3) * 100 }}%"></div>

                    <div class="grid grid-cols-4 gap-2 relative z-10">
                        @foreach($steps as $index => $step)
                            @php $stepIndex = $index + 1; @endphp
                            <div class="flex flex-col items-center text-center">
                                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300 shadow-md {{ $stepIndex <= $currentStep ? 'bg-brand-500 text-white ring-4 ring-brand-500/30 scale-105' : 'bg-slate-700 text-slate-400 border border-slate-600' }}">
                                    @if($stepIndex < $currentStep)
                                        ✓
                                    @else
                                        {{ $stepIndex }}
                                    @endif
                                </div>
                                <span class="text-[10px] sm:text-xs font-bold mt-2 {{ $stepIndex <= $currentStep ? 'text-white' : 'text-slate-400' }}">
                                    {{ $step['label'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- LIVE STATUS BANNER & SIMULATION -->
        @php
            $statusBanner = match($order->status) {
                'SEDANG_DIISI' => [
                    'dot' => 'bg-amber-500 animate-pulse',
                    'title' => 'Status Saat Ini: Sedang Diproses (Sterilisasi UV & Isi Air)',
                    'desc' => 'Galon sedang diproses di mesin sterilisasi otomatis oleh operator depot.',
                    'btn' => 'Simulasi Status →',
                ],
                'SIAP_DIAMBIL' => [
                    'dot' => 'bg-emerald-500 animate-ping',
                    'title' => 'Status Saat Ini: Siap Diambil di Depot!',
                    'desc' => 'Galon sudah selesai diisi & disegel. Tunjukkan PIN ' . $order->pickup_pin . ' ke kasir depot.',
                    'btn' => 'Simulasi Status →',
                ],
                'SELESAI' => [
                    'dot' => 'bg-emerald-600',
                    'title' => 'Status Saat Ini: Pesanan Selesai',
                    'desc' => 'Galon telah diambil. Anda dapat memberikan ulasan higienitas untuk depot ini.',
                    'btn' => 'Reset Simulasi ↺',
                ],
                default => [
                    'dot' => 'bg-brand-600',
                    'title' => 'Status Saat Ini: Menunggu Antrean (Diterima)',
                    'desc' => 'Menunggu giliran antrean pengisian (Estimasi ambil: ' . ($order->pickup_time_estimated ?? '15-30 Menit Lagi') . ').',
                    'btn' => 'Simulasi Status →',
                ],
            };
        @endphp
        <div class="px-6 sm:px-7 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="w-3 h-3 rounded-full flex-shrink-0 {{ $statusBanner['dot'] }}"></span>
                <div>
                    <span class="text-xs font-bold text-slate-900 block">{{ $statusBanner['title'] }}</span>
                    <p class="text-[11px] text-slate-500">{{ $statusBanner['desc'] }}</p>
                </div>
            </div>
            <form action="{{ route('orders.advance', $order->order_number) }}" method="POST">
                @csrf
                <button type="submit" title="Klik untuk simulasi tahap status berikutnya"
                        class="px-3 py-1.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 text-[10px] font-bold transition flex items-center gap-1 flex-shrink-0">
                    <span>{{ $statusBanner['btn'] }}</span>
                </button>
            </form>
        </div>

        <!-- Current Status & Polling Indicator -->
        <div class="p-6 bg-slate-50 border-b border-slate-200/80 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full bg-brand-500 animate-ping"></div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Status Terkini</span>
                    <span class="text-sm font-extrabold text-slate-900">{{ $order->status_label }}</span>
                </div>
            </div>
            <span class="text-xs text-slate-400 italic">Live Update setiap 5 detik</span>
        </div>

        <!-- INFORMASI & RINCIAN PESANAN -->
        <div class="p-6 sm:p-7 space-y-6">

            <!-- Detail Depot Pengisian -->
            <div class="p-4 rounded-2xl bg-white border border-slate-200/80 flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-brand-600 block">Depot Pengisian Air</span>
                    <h3 class="text-sm font-black text-slate-900">{{ $order->depot->name }}</h3>
                    <p class="text-xs text-slate-500">📍 {{ $order->depot->address }} (Kec. {{ $order->depot->district }})</p>
                </div>
                <a href="{{ route('depots.show', $order->depot->slug) }}" target="_blank"
                   class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-brand-50 hover:text-brand-800 text-xs font-bold transition text-slate-700 flex-shrink-0">
                    Detail Depot &nearr;
                </a>
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

                    <div class="flex items-center justify-between text-xs py-2 text-emerald-700 font-semibold">
                        <span>Metode Pengambilan</span>
                        <span>Ambil Sendiri di Depot (Gratis)</span>
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
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// SCRUM-62: Polling Realtime Status Pesanan setiap 5 detik
const orderNumber = "{{ $order->order_number }}";
const currentStatus = "{{ $order->status }}";

function pollOrderStatus() {
    if (currentStatus === 'SELESAI' || currentStatus === 'BATAL') return;

    fetch(`/api/orders/${orderNumber}/status`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.status !== currentStatus) {
                window.location.reload();
            }
        })
        .catch(err => console.log('Polling status error:', err));
}

setInterval(pollOrderStatus, 5000);
</script>
@endpush
