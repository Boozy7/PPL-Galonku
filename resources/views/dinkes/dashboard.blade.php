@extends('layouts.app', ['title' => 'Dashboard Monitoring & Pengawasan Dinkes Surabaya - Galonku'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full space-y-8">

    <!-- Header Portal Dinkes -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    PORTAL RESMI DINAS KESEHATAN
                </span>
                <span class="text-xs text-slate-500">• Kota Surabaya</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Dashboard Monitoring & Pengawasan Kota</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Peta sebaran analitik sanitasi DAMIU tingkat kota, verifikasi pendaftaran & sertifikasi SLHS, serta tindak lanjut aduan warga.
            </p>
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-600 rounded-xl text-xs font-bold transition">
                Logout (Petugas Dinkes)
            </button>
        </form>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-bold flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-bold flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-bold">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- SCRUM-69: STATISTIK TINGKAT KOTA & PETA SEBARAN ANALITIK       -->
    <!-- ============================================================== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total DAMIU Aktif</span>
                <span class="text-2xl font-black text-slate-900 font-mono mt-1 block">{{ $stats['total_depots'] }}</span>
                <span class="text-[10px] text-slate-500 font-semibold">Terdaftar & tampil di kota</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-brand-50 text-brand-700 flex items-center justify-center text-lg font-bold">🏢</div>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-emerald-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block">Depot Laik Sehat</span>
                <span class="text-2xl font-black text-emerald-700 font-mono mt-1 block">{{ $stats['healthy_depots'] }}</span>
                <span class="text-[10px] text-emerald-600 font-bold">SLHS Aktif & Bebas Aduan</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg font-bold">✅</div>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-rose-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-rose-700 uppercase tracking-wider block">Depot Bermasalah / Pending</span>
                <span class="text-2xl font-black text-rose-700 font-mono mt-1 block">{{ $stats['problematic_depots'] }}</span>
                <span class="text-[10px] text-rose-600 font-semibold">Belum/Cabut SLHS atau Ada Aduan</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg font-bold">⚠️</div>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Aduan & Kualitas Air</span>
                <span class="text-2xl font-black text-slate-900 font-mono mt-1 block">{{ $stats['active_complaints'] }} <span class="text-xs text-slate-400 font-normal">Aktif / {{ $stats['resolved_complaints'] }} Selesai</span></span>
                <span class="text-[10px] text-brand-700 font-bold">Rata-rata TDS Kota: {{ $stats['avg_tds'] }} ppm</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg font-bold">📊</div>
        </div>
    </div>

    <!-- Peta Sebaran Analitik Kota & Rekap Wilayah (SCRUM-69) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 flex flex-col">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Peta Sebaran Analitik Sanitasi DAMIU Surabaya</h2>
                    <p class="text-xs text-slate-500">Pemetaan depot laik sehat vs depot bermasalah untuk evaluasi kesehatan lingkungan</p>
                </div>
                <div class="flex flex-wrap items-center gap-1.5 text-[11px]">
                    <button type="button" onclick="filterDinkesMap('all')" id="btnMapAll" class="map-filter-btn px-2.5 py-1 rounded-xl font-bold bg-slate-900 text-white">Semua</button>
                    <button type="button" onclick="filterDinkesMap('healthy')" id="btnMapHealthy" class="map-filter-btn px-2.5 py-1 rounded-xl font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">🟢 Laik Sehat</button>
                    <button type="button" onclick="filterDinkesMap('problematic')" id="btnMapProblem" class="map-filter-btn px-2.5 py-1 rounded-xl font-bold bg-rose-50 text-rose-700 border border-rose-200">🔴 Bermasalah</button>
                    <button type="button" onclick="filterDinkesMap('renewal')" id="btnMapRenewal" class="map-filter-btn px-2.5 py-1 rounded-xl font-bold bg-amber-50 text-amber-700 border border-amber-200">🟡 Perpanjangan</button>
                </div>
            </div>

            <div id="dinkesMonitoringMap" class="w-full h-80 sm:h-96 rounded-2xl border border-slate-200 overflow-hidden z-0"></div>
        </div>

        <!-- Analitik per Kecamatan -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 flex flex-col">
            <h2 class="text-base font-extrabold text-slate-900">Evaluasi per Kecamatan</h2>
            <p class="text-xs text-slate-500 mb-4">Perbandingan depot laik sehat vs bermasalah per wilayah</p>

            <div class="overflow-y-auto max-h-96 pr-1 space-y-2.5">
                @foreach($districtStats as $ds)
                    <div class="p-3 rounded-2xl border border-slate-100 bg-slate-50/70">
                        <div class="flex items-center justify-between text-xs font-bold text-slate-800">
                            <span>Kec. {{ $ds['district'] }}</span>
                            <span class="font-mono text-[11px] text-slate-500">{{ $ds['total'] }} Depot • {{ $ds['avg_tds'] }} ppm</span>
                        </div>
                        <div class="flex items-center gap-2 mt-1.5 text-[10px] font-bold">
                            <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">Laik Sehat: {{ $ds['healthy'] }}</span>
                            @if($ds['problematic'] > 0)
                                <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-800">Bermasalah: {{ $ds['problematic'] }}</span>
                            @else
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-500">0 Bermasalah</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- PENDAFTARAN DEPOT BARU (MENUNGGU ACC DINKES)                   -->
    <!-- ============================================================== -->
    <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-indigo-200/80">
        <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
            <div>
                <h2 class="text-base font-extrabold text-slate-900">Pendaftaran Depot Baru (Menunggu ACC Dinkes)</h2>
                <p class="text-xs text-slate-500 mt-0.5">Depot baru yang mendaftar tidak akan muncul di dashboard warga sebelum foto toko, lokasi map, dan dokumen sertifikatnya disetujui Dinkes</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-800 border border-indigo-200">
                {{ $pendingRegistrations->count() }} Pendaftar Baru
            </span>
        </div>

        @if($pendingRegistrations->isEmpty())
            <div class="text-center py-8 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                <p class="text-xs font-bold text-slate-600">Tidak ada pendaftaran depot baru yang menunggu ACC saat ini.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($pendingRegistrations as $reg)
                    <div class="p-5 rounded-2xl border border-indigo-200 bg-indigo-50/20 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                        <div class="flex items-start gap-4 flex-1">
                            @if($reg->cover_image)
                                <a href="{{ asset($reg->cover_image) }}" target="_blank" class="flex-shrink-0">
                                    <img src="{{ asset($reg->cover_image) }}" alt="Foto Toko {{ $reg->name }}" class="w-24 h-20 rounded-2xl object-cover border border-slate-200 shadow-sm">
                                </a>
                            @endif
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-sm font-black text-slate-900">{{ $reg->name }}</h3>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800">Menunggu ACC Dinkes</span>
                                </div>
                                <p class="text-xs text-slate-600">📍 {{ $reg->address }} (Kec. {{ $reg->district }})</p>
                                <div class="flex flex-wrap gap-3 text-[11px] text-slate-500">
                                    <span>👤 Pemilik: <strong>{{ $reg->user?->name ?? '-' }}</strong> ({{ $reg->user?->email ?? '-' }})</span>
                                    <span>📞 {{ $reg->phone }}</span>
                                    <span>🗺️ Pin Map: <strong class="font-mono">{{ $reg->lat }}, {{ $reg->lng }}</strong></span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 flex-wrap flex-shrink-0">
                            @if($reg->cover_image)
                                <a href="{{ asset($reg->cover_image) }}" target="_blank" class="px-3 py-2 rounded-xl bg-white border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-50">
                                    Foto Toko &nearr;
                                </a>
                            @endif
                            @if($reg->certificate_document_path)
                                <a href="{{ asset($reg->certificate_document_path) }}" target="_blank" class="px-3 py-2 rounded-xl bg-white border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-50">
                                    Dokumen SLHS &nearr;
                                </a>
                            @endif

                            <form action="{{ route('dinkes.approve_depot', $reg->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
                                    ✓ ACC & Aktifkan Depot
                                </button>
                            </form>

                            <form action="{{ route('dinkes.reject_depot', $reg->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition">
                                    Tolak
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- ============================================================== -->
    <!-- SCRUM-67: PERMOHONAN PERPANJANGAN & VERIFIKASI DOKUMEN LAB     -->
    <!-- ============================================================== -->
    <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-200/80">
        <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
            <div>
                <h2 class="text-base font-extrabold text-slate-900">Antrean Verifikasi Perpanjangan Sertifikat & Uji Lab</h2>
                <p class="text-xs text-slate-500 mt-0.5">Depot terdaftar yang mengunggah pembaruan dokumen lab atau status sertifikasinya belum aktif</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                {{ $pendingRenewals->count() }} Menunggu Verifikasi
            </span>
        </div>

        @if($pendingRenewals->isEmpty())
            <div class="text-center py-8 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                <p class="text-xs font-bold text-slate-600">Semua permohonan perpanjangan dokumen lab sudah tervalidasi.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($pendingRenewals as $depot)
                    <div class="p-5 rounded-2xl border border-amber-200 bg-amber-50/20 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <div class="space-y-1 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-sm font-black text-slate-900">{{ $depot->name }}</h3>
                                @if($depot->user?->email === 'mitra@galonku.com')
                                    <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-brand-100 text-brand-800">DEPOT DEMO UTAMA</span>
                                @endif
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">
                                    {{ $depot->certification_status }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-600">📍 {{ $depot->address }} (Kec. {{ $depot->district }})</p>
                            <div class="flex flex-wrap gap-4 text-[11px] text-slate-500">
                                <span>🔬 Lab: <strong>{{ $depot->lab_name ?? 'BBLK Surabaya' }}</strong></span>
                                <span>📅 Uji Terakhir: <strong>{{ $depot->last_tested_date ?? '-' }}</strong></span>
                            </div>
                            @if($depot->certificate_notes)
                                <p class="text-xs text-slate-600 italic mt-1">"{{ $depot->certificate_notes }}"</p>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 flex-wrap flex-shrink-0">
                            @if(!empty($depot->certificate_document_path))
                                <a href="{{ asset($depot->certificate_document_path) }}" target="_blank"
                                   class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold border border-slate-300 transition">
                                    Periksa Berkas &nearr;
                                </a>
                            @endif
                            <form action="{{ route('dinkes.approve_depot', $depot->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
                                    ✓ Setujui & Aktifkan SLHS
                                </button>
                            </form>
                            <form action="{{ route('dinkes.reject_depot', $depot->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="rejection_reason" value="Dokumen lab belum memenuhi standar baku mutu Permenkes.">
                                <button type="submit" class="px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition">
                                    Tolak
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- ============================================================== -->
    <!-- SCRUM-68: LAPORAN & ADUAN WARGA                                -->
    <!-- ============================================================== -->
    <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-200/80">
        <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
            <div>
                <h2 class="text-base font-extrabold text-slate-900">Laporan Aduan Kualitas Air dari Warga</h2>
                <p class="text-xs text-slate-500 mt-0.5">Daftar pengaduan warga terkait air keruh, bau, lumut, serta tindakan pencabutan badge SLHS</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-800 border border-rose-200">
                {{ $complaints->where('status', '!=', 'SELESAI')->count() }} Aduan Aktif
            </span>
        </div>

        @if($complaints->isEmpty())
            <div class="text-center py-8 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                <p class="text-xs font-bold text-slate-600">Belum ada pengaduan kualitas air yang masuk dari warga.</p>
            </div>
        @else
            <div class="space-y-6">
                @foreach($complaints as $c)
                    <div class="p-5 sm:p-6 rounded-2xl border border-slate-200 bg-slate-50/70">
                        <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-slate-200">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-lg font-mono text-xs font-black bg-rose-100 text-rose-800 border border-rose-200">
                                    {{ $c->ticket_number }}
                                </span>
                                <span class="text-xs font-bold text-slate-700">{{ $c->created_at->format('d M Y, H:i') }} WIB</span>
                            </div>

                            <span class="px-3 py-1 rounded-full text-[11px] font-extrabold uppercase
                                {{ $c->status === 'SELESAI' ? 'bg-emerald-100 text-emerald-800' : ($c->status === 'TERKIRIM_KE_DINKES' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                {{ str_replace('_', ' ', $c->status) }}
                            </span>
                        </div>

                        <div class="mt-4 grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <div class="lg:col-span-2 space-y-2">
                                <h4 class="text-sm font-extrabold text-slate-900">{{ $c->issue_title }}</h4>
                                <p class="text-xs text-slate-700 bg-white p-3 rounded-xl border border-slate-200">"{{ $c->description }}"</p>
                                <div class="flex flex-wrap gap-4 text-[11px] text-slate-500 pt-1">
                                    <span>👤 Pelapor: <strong class="text-slate-800">{{ $c->reporter_name }}</strong> ({{ $c->reporter_phone }})</span>
                                    <span>🏢 Terlapor: <strong class="text-slate-800">{{ $c->depot->name ?? 'Unknown' }}</strong> (Kec. {{ $c->depot->district ?? '-' }})</span>
                                </div>
                                @if($c->depot && !$c->depot->is_certified)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-[11px] font-bold">
                                        ⚠️ Badge SLHS Depot Ini Telah Dicabut
                                    </span>
                                @endif
                            </div>

                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1.5">Foto Bukti Warga:</span>
                                @if($c->photo_path)
                                    <a href="{{ asset($c->photo_path) }}" target="_blank">
                                        <img src="{{ asset($c->photo_path) }}" alt="Bukti Aduan" class="w-full h-32 rounded-xl object-cover border border-slate-200 shadow-sm">
                                    </a>
                                @else
                                    <div class="h-32 rounded-xl bg-slate-100 border border-dashed border-slate-300 flex items-center justify-center text-xs text-slate-400">
                                        Tidak ada lampiran foto
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-5 pt-4 border-t border-slate-200">
                            @if($c->status !== 'SELESAI')
                                <form action="{{ route('dinkes.update_complaint', $c->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                    @csrf
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Status Penanganan</label>
                                            <select name="status" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl bg-white">
                                                <option value="SEDANG_INVESTIGASI" {{ $c->status === 'SEDANG_INVESTIGASI' ? 'selected' : '' }}>1. Sedang Diinvestigasi</option>
                                                <option value="INSPEKSI_LAPANGAN" {{ $c->status === 'INSPEKSI_LAPANGAN' ? 'selected' : '' }}>2. Inspeksi Lapangan</option>
                                                <option value="SELESAI">3. Selesai (Kasus Ditutup)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Foto Bukti Inspeksi (Opsional)</label>
                                            <input type="file" name="proof_image" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:bg-slate-200 file:text-xs file:font-bold">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Catatan Tindak Lanjut Dinkes *</label>
                                        <textarea name="dinkes_notes" rows="2" required placeholder="Tuliskan hasil investigasi atau tindakan pembinaan..." class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl bg-white">{{ old('dinkes_notes', $c->dinkes_notes) }}</textarea>
                                    </div>

                                    @if($c->depot && $c->depot->is_certified)
                                        <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 flex items-start gap-2.5">
                                            <input type="checkbox" name="revoke_badge" value="1" id="revoke_{{ $c->id }}" class="w-4 h-4 mt-0.5 rounded text-rose-600 border-rose-300">
                                            <label for="revoke_{{ $c->id }}" class="text-xs text-rose-900 cursor-pointer">
                                                <strong class="font-bold block">Hapus / Cabut Badge Resmi SLHS Depot Ini</strong>
                                                Centang jika terbukti bermasalah sehingga badge laik sehat depot dinonaktifkan.
                                            </label>
                                        </div>
                                    @endif

                                    <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition">
                                        Simpan Respon & Perbarui Status
                                    </button>
                                </form>
                            @else
                                <div class="p-3.5 bg-emerald-50 rounded-xl border border-emerald-200 text-xs text-emerald-900">
                                    <p class="font-bold">✓ Aduan Selesai Ditangani</p>
                                    <p class="mt-1"><strong>Catatan Dinkes:</strong> {{ $c->dinkes_notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- ============================================================== -->
    <!-- MANAJEMEN STATUS DEPOT TERDAFTAR (URUTAN PRIORITAS DEMO ATAS)  -->
    <!-- ============================================================== -->
    <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-200/80">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 mb-4 border-b border-slate-100">
            <div>
                <h2 class="text-base font-extrabold text-slate-900">Manajemen Status Sertifikasi Depot (Surabaya)</h2>
                <p class="text-xs text-slate-500 mt-0.5">Ubah status sertifikasi depot secara langsung (Terverifikasi Dinkes, Belum Diperpanjang, atau Cabut Badge)</p>
            </div>
            <span class="text-xs font-bold text-slate-500">{{ $allApprovedDepots->count() }} Depot Aktif</span>
        </div>

        <div class="overflow-x-auto max-h-[32rem] overflow-y-auto">
            <table class="w-full text-left text-xs">
                <thead class="sticky top-0 bg-white border-b border-slate-200 text-slate-400 uppercase font-bold text-[10px] z-10">
                    <tr>
                        <th class="py-2.5 pr-3">Nama Depot</th>
                        <th class="py-2.5 pr-3">Kecamatan</th>
                        <th class="py-2.5 pr-3">No. SLHS</th>
                        <th class="py-2.5 pr-3">Status Saat Ini</th>
                        <th class="py-2.5 text-right">Ubah Status Dinkes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($allApprovedDepots as $idx => $d)
                        <tr class="hover:bg-slate-50/80 transition {{ $idx === 0 ? 'bg-brand-50/30' : '' }}">
                            <td class="py-3 pr-3 font-bold text-slate-800">
                                {{ $d->name }}
                                @if($d->user?->email === 'mitra@galonku.com')
                                    <span class="ml-1.5 px-2 py-0.5 rounded-full text-[9px] font-black bg-brand-100 text-brand-800">AKUN DEMO #1</span>
                                @elseif($d->user && !str_ends_with($d->user->email, '@galonku.id'))
                                    <span class="ml-1.5 px-2 py-0.5 rounded-full text-[9px] font-black bg-indigo-100 text-indigo-800">PENDAFTAR BARU</span>
                                @endif
                            </td>
                            <td class="py-3 pr-3 text-slate-500">{{ $d->district }}</td>
                            <td class="py-3 pr-3 font-mono text-slate-600">{{ $d->slhs_number ?? '-' }}</td>
                            <td class="py-3 pr-3">
                                @if($d->is_certified && $d->certification_status === 'AKTIF')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">✓ Terverifikasi (AKTIF)</span>
                                @elseif($d->certification_status === 'PROSES_RENEWAL')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">⏳ Belum Diperpanjang</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">✕ Belum Tersertifikasi / Dicabut</span>
                                @endif
                            </td>
                            <td class="py-3 text-right">
                                <form action="{{ route('dinkes.update_depot_status', $d->id) }}" method="POST" class="inline-flex items-center gap-1.5">
                                    @csrf
                                    <select name="certification_status" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg border border-slate-300 bg-white">
                                        <option value="AKTIF" {{ $d->certification_status === 'AKTIF' ? 'selected' : '' }}>Terverifikasi Dinkes</option>
                                        <option value="PROSES_RENEWAL" {{ $d->certification_status === 'PROSES_RENEWAL' ? 'selected' : '' }}>Belum Diperpanjang</option>
                                        <option value="BELUM_TERSERTIFIKASI" {{ $d->certification_status === 'BELUM_TERSERTIFIKASI' ? 'selected' : '' }}>Cabut / Nonaktif</option>
                                    </select>
                                    <button type="submit" class="px-2.5 py-1 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-[10px] font-bold transition">
                                        Simpan
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    const dinkesMapDepots = @json($monitoringMapDepots);
    let dinkesMap;
    let dinkesMarkerLayer;

    document.addEventListener('DOMContentLoaded', function () {
        dinkesMap = L.map('dinkesMonitoringMap').setView([-7.2850, 112.7380], 12);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(dinkesMap);

        dinkesMarkerLayer = L.layerGroup().addTo(dinkesMap);
        renderDinkesMarkers('all');

        setTimeout(() => dinkesMap.invalidateSize(), 200);
    });

    function renderDinkesMarkers(filter) {
        dinkesMarkerLayer.clearLayers();

        dinkesMapDepots.forEach(d => {
            if (filter !== 'all' && d.category !== filter) return;

            const color = d.category === 'healthy' ? '#10b981' : (d.category === 'renewal' ? '#f59e0b' : '#ef4444');
            const statusText = d.category === 'healthy' ? 'Laik Sehat (SLHS Aktif)' : (d.category === 'renewal' ? 'Belum Diperpanjang / Perlu Uji' : 'Bermasalah / Dicabut');

            const marker = L.circleMarker([d.lat, d.lng], {
                radius: 7,
                fillColor: color,
                color: '#ffffff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.9
            });

            marker.bindPopup(`
                <div style="font-family: sans-serif; min-width: 190px;">
                    <b style="font-size: 12px; color: #0f172a;">${d.name}</b><br>
                    <span style="font-size: 10px; color: #64748b;">Kec. ${d.district} • TDS: ${d.tds_ppm} ppm • pH: ${d.ph_level}</span><br>
                    <span style="display:inline-block; margin-top:4px; padding:2px 6px; border-radius:9999px; font-size:10px; font-weight:700; color:white; background:${color};">${statusText}</span>
                    ${d.active_complaints > 0 ? `<br><span style="font-size:10px; color:#dc2626; font-weight:700;">⚠️ ${d.active_complaints} Aduan Warga Aktif</span>` : ''}
                    <br><a href="${d.show_url}" target="_blank" style="display:inline-block; margin-top:6px; font-size:11px; color:#2563eb; font-weight:700;">Lihat Profil Depot &nearr;</a>
                </div>
            `);

            marker.addTo(dinkesMarkerLayer);
        });
    }

    function filterDinkesMap(filter) {
        document.querySelectorAll('.map-filter-btn').forEach(btn => {
            btn.classList.remove('ring-2', 'ring-slate-900');
        });
        const activeBtn = {
            'all': 'btnMapAll',
            'healthy': 'btnMapHealthy',
            'problematic': 'btnMapProblem',
            'renewal': 'btnMapRenewal'
        }[filter];
        if (activeBtn) {
            document.getElementById(activeBtn).classList.add('ring-2', 'ring-slate-900');
        }
        renderDinkesMarkers(filter);
    }
</script>
@endpush
