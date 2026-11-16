@extends('layouts.app', ['title' => 'Portal Pengawasan Dinkes Surabaya - Galonku'])

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

    <!-- Header Portal Dinkes -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800">
                    PORTAL RESMI PEMERINTAH KOTA
                </span>
                <span class="text-xs text-slate-500">• Dinas Kesehatan Kota Surabaya</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Verifikasi & Pengawasan Sertifikasi DAMIU</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Pemeriksaan berkas uji lab air berkala, validasi kelayakan higiene sanitasi, dan penerbitan izin badge SLHS.
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
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-bold flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-bold flex items-center gap-2">
            <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="space-y-8">

        <!-- 1. TABEL DAFTAR PERMOHONAN VERIFIKASI MASUK (SCRUM-67) -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-200/80">
            <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Antrean Verifikasi Berkas Lab & Sertifikat SLHS</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Depot yang baru mendaftar atau mengunggah pembaruan dokumen hasil uji lab</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                    {{ $pendingDepots->count() }} Menunggu Diperiksa
                </span>
            </div>

            @if($pendingDepots->isEmpty())
                <div class="text-center py-12 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                    <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm font-bold text-slate-700">Tidak ada antrean berkas pending</p>
                    <p class="text-xs text-slate-400 mt-0.5">Semua permohonan sertifikasi depot telah selesai diproses.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($pendingDepots as $depot)
                        <div class="p-5 rounded-2xl border border-amber-200 bg-amber-50/20 hover:bg-white transition flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                            <div class="space-y-1.5 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-sm font-black text-slate-900">{{ $depot->name }}</h3>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                                        {{ $depot->certification_status === 'PROSES_RENEWAL' ? 'Pembaruan Berkas Uji Lab' : 'Permohonan Baru' }}
                                    </span>
                                    <span class="text-[11px] text-slate-400 font-mono">ID #{{ $depot->id }}</span>
                                </div>
                                <p class="text-xs text-slate-600">📍 {{ $depot->address }} (Kec. {{ $depot->district }})</p>

                                <div class="flex flex-wrap gap-4 pt-1 text-[11px] text-slate-500">
                                    <span>🔬 Lab: <strong class="text-slate-800">{{ $depot->lab_name ?? 'BBLK Surabaya' }}</strong></span>
                                    <span>📅 Uji Terakhir: <strong class="text-slate-800">{{ $depot->last_tested_date ?? '-' }}</strong></span>
                                    <span>📞 Kontak: <strong class="text-slate-800">{{ $depot->phone }}</strong></span>
                                </div>

                                @if($depot->certificate_notes)
                                    <p class="text-xs text-slate-500 italic bg-white/70 p-2 rounded-xl border border-slate-100 mt-2">
                                        "{{ $depot->certificate_notes }}"
                                    </p>
                                @endif
                            </div>

                            <!-- Tombol Aksi Verifikasi (Lihat Berkas, Setujui, Tolak) -->
                            <div class="flex items-center gap-2.5 flex-shrink-0 flex-wrap">
                                @if(!empty($depot->certificate_document_path))
                                    <a href="{{ asset($depot->certificate_document_path) }}" target="_blank"
                                       class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition flex items-center gap-1 border border-slate-300">
                                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Periksa Berkas &nearr;</span>
                                    </a>
                                @endif

                                <!-- Form ACC / Setujui SLHS (SCRUM-83) -->
                                <form action="{{ route('dinkes.approve_depot', $depot->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Apakah Anda yakin dokumen lab depot ini telah valid dan siap diterbitkan sertifikat SLHS resmi?')"
                                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        <span>Setujui & Terbitkan SLHS</span>
                                    </button>
                                </form>

                                <!-- Form Tolak Verifikasi -->
                                <form action="{{ route('dinkes.reject_depot', $depot->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="rejection_reason" value="Dokumen hasil uji lab tidak lengkap atau parameter mikrobiologi belum memenuhi syarat baku mutu permenkes.">
                                    <button type="submit" onclick="return confirm('Tolak pengajuan sertifikasi depot ini?')"
                                            class="px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition">
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
        <!-- 3. KELOLA & RESPON LAPORAN AIR BERMASALAH (SCRUM-68 / SCRUM-84) -->
        <!-- ============================================================== -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-200/80">
            <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Laporan Aduan Kualitas Air dari Warga</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar pengaduan warga terkait air keruh, bau, lumut, dan tindakan respon dinas</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-800 border border-rose-200">
                    {{ $complaints->where('status', '!=', 'SELESAI')->count() }} Aduan Aktif
                </span>
            </div>

            @if($complaints->isEmpty())
                <div class="text-center py-10 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                    <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm font-bold text-slate-700">Tidak ada pengaduan warga</p>
                    <p class="text-xs text-slate-400 mt-0.5">Belum ada laporan kualitas air yang masuk ke sistem.</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($complaints as $c)
                        <div class="p-5 sm:p-6 rounded-2xl border border-slate-200 bg-slate-50/70 hover:bg-white transition relative">
                            
                            <!-- Header Status Aduan -->
                            <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-slate-200">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-lg font-mono text-xs font-black bg-rose-100 text-rose-800 border border-rose-200">
                                        {{ $c->ticket_number }}
                                    </span>
                                    <span class="text-xs font-bold text-slate-700">{{ $c->created_at->format('d M Y, H:i') }} WIB</span>
                                </div>

                                <span class="px-3 py-1 rounded-full text-[11px] font-extrabold uppercase
                                    {{ $c->status === 'SELESAI' ? 'bg-emerald-100 text-emerald-800' : 
                                      ($c->status === 'TERKIRIM_KE_DINKES' ? 'bg-rose-100 text-rose-800 animate-pulse' : 'bg-amber-100 text-amber-800') }}">
                                    {{ str_replace('_', ' ', $c->status) }}
                                </span>
                            </div>

                            <!-- Detail Permasalahan -->
                            <div class="mt-4 grid grid-cols-1 lg:grid-cols-3 gap-6">
                                <div class="lg:col-span-2 space-y-2">
                                    <h4 class="text-sm font-extrabold text-slate-900">{{ $c->issue_title }}</h4>
                                    <p class="text-xs text-slate-700 bg-white p-3 rounded-xl border border-slate-200 leading-relaxed">
                                        "{{ $c->description }}"
                                    </p>

                                    <div class="flex flex-wrap gap-4 text-[11px] text-slate-500 pt-1">
                                        <span>👤 Pelapor: <strong class="text-slate-800">{{ $c->reporter_name }}</strong> ({{ $c->reporter_phone }})</span>
                                        <span>🏢 Terlapor: <strong class="text-slate-800">{{ $c->depot->name ?? 'Unknown Depot' }}</strong> (Kec. {{ $c->depot->district ?? '-' }})</span>
                                        <span>🏷️ Kategori: <strong class="uppercase text-rose-700">{{ $c->issue_type }}</strong></span>
                                    </div>

                                    @if($c->depot && !$c->depot->is_certified)
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold">
                                            <span>⚠️ Badge SLHS Depot Ini Telah Dicabut</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Foto Bukti Aduan dari Pelanggan -->
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1.5">Foto Bukti dari Warga:</span>
                                    @if($c->photo_path)
                                        <a href="{{ asset($c->photo_path) }}" target="_blank">
                                            <img src="{{ asset($c->photo_path) }}" alt="Bukti Aduan" class="w-full h-32 rounded-xl object-cover border border-slate-200 shadow-sm hover:opacity-90 transition">
                                        </a>
                                    @else
                                        <div class="h-32 rounded-xl bg-slate-100 border border-dashed border-slate-300 flex items-center justify-center text-xs text-slate-400">
                                            Tidak ada lampiran foto
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- FORM RESPON TINDAK LANJUT DINKES (SCRUM-85) -->
                            <div class="mt-5 pt-4 border-t border-slate-200">
                                @if($c->status !== 'SELESAI')
                                    <form action="{{ route('dinkes.update_complaint', $c->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                        @csrf
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Status Penanganan Aduan</label>
                                                <select name="status" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500">
                                                    <option value="SEDANG_INVESTIGASI" {{ $c->status === 'SEDANG_INVESTIGASI' ? 'selected' : '' }}>1. Sedang Diinvestigasi (Pemeriksaan Awal)</option>
                                                    <option value="INSPEKSI_LAPANGAN" {{ $c->status === 'INSPEKSI_LAPANGAN' ? 'selected' : '' }}>2. Inspeksi Lapangan (Pengambilan Sampel Air)</option>
                                                    <option value="SELESAI">3. Selesai (Kasus Ditutup & Solusi Diterapkan)</option>
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Unggah Bukti Hasil Inspeksi Lapangan (Opsional)</label>
                                                <input type="file" name="proof_image" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:bg-slate-200 file:text-xs file:font-bold">
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Catatan Tindak Lanjut Petugas Dinkes *</label>
                                            <textarea name="dinkes_notes" rows="2" required placeholder="Tuliskan temuan investigasi, tindakan pembinaan, atau hasil uji lab ulang terhadap depot..."
                                                      class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500">{{ old('dinkes_notes', $c->dinkes_notes) }}</textarea>
                                        </div>

                                        <!-- OPSI KRITIS: HAPUS / CABUT BADGE TERKAIT DEPOT AIR BERMASALAH -->
                                        @if($c->depot && $c->depot->is_certified)
                                            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 flex items-start gap-3">
                                                <input type="checkbox" name="revoke_badge" value="1" id="revoke_{{ $c->id }}"
                                                       class="w-4 h-4 mt-0.5 rounded text-rose-600 focus:ring-rose-500 border-rose-300">
                                                <label for="revoke_{{ $c->id }}" class="text-xs text-rose-900 cursor-pointer">
                                                    <strong class="block font-bold">Hapus / Cabut Badge Resmi SLHS Depot Ini</strong>
                                                    Centang jika hasil investigasi membuktikan air depot tidak memenuhi baku mutu kesehatan, sehingga sertifikat dan badge resminya langsung dinonaktifkan dari publik.
                                                </label>
                                            </div>
                                        @endif

                                        <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-sm transition">
                                            Simpan Respon & Perbarui Status Aduan
                                        </button>
                                    </form>
                                @else
                                    <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-200 text-xs text-emerald-900">
                                        <p class="font-bold">✓ Kasus Telah Ditutup & Diselesaikan oleh Dinkes</p>
                                        <p class="mt-1"><strong>Catatan Hasil Akhir:</strong> {{ $c->dinkes_notes }}</p>
                                        @if($c->proof_image_path)
                                            <a href="{{ asset($c->proof_image_path) }}" target="_blank" class="inline-block mt-2 font-bold underline text-emerald-800">
                                                Lihat Bukti Berita Acara / Foto Penyelesaian &nearr;
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>

                        </div>
                    @endforeach
                </div>
            @endif
        </div>       

        <!-- 2. DAFTAR DEPOT YANG SUDAH TERVERIFIKASI RESMI (SAMPLE MONITORING) -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-200/80">
            <h2 class="text-base font-extrabold text-slate-900 mb-1">Depot Terakreditasi SLHS Aktif (Surabaya)</h2>
            <p class="text-xs text-slate-500 mb-4">Daftar depot air minum yang telah memiliki izin resmi Laik Higiene Sanitasi dari Dinkes</p>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-400 uppercase font-bold text-[10px]">
                            <th class="py-2.5">Nama Depot</th>
                            <th class="py-2.5">Kecamatan</th>
                            <th class="py-2.5">Nomor SLHS</th>
                            <th class="py-2.5">Masa Berlaku</th>
                            <th class="py-2.5">Status Badge</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($certifiedDepots as $cd)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 font-bold text-slate-800">{{ $cd->name }}</td>
                                <td class="py-3 text-slate-500">{{ $cd->district }}</td>
                                <td class="py-3 font-mono font-semibold text-slate-700">{{ $cd->slhs_number ?? '-' }}</td>
                                <td class="py-3 text-emerald-700 font-semibold">{{ $cd->expiry_date ?? 'Aktif s/d 2029' }}</td>
                                <td class="py-3">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        ✓ Badge SLHS Aktif
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
