@extends('layouts.app', ['title' => 'Lapor Masalah Kualitas Air ke Dinkes Surabaya - Galonku'])

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-3xl bg-rose-50 text-rose-600 mb-3 shadow-sm border border-rose-100">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Kanal Pengaduan Kualitas Air</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-md mx-auto">
            Laporkan air galon berbau, keruh, atau depot yang tidak higienis langsung ke pengawas Dinas Kesehatan Kota Surabaya.
        </p>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
            <p class="font-bold mb-1">Periksa kembali isian formulir:</p>
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-lg">
        <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- 1. PILIH DEPOT TERLAPOR -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    1. Pilih Depot Air Minum (DAMIU) Terlapor *
                </label>
                <select name="depot_id" required class="w-full px-4 py-3 rounded-2xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm font-medium">
                    <option value="">-- Pilih Depot Terlapor di Surabaya --</option>
                    @foreach($depots as $d)
                        <option value="{{ $d->id }}" {{ old('depot_id', $selectedDepotId) == $d->id ? 'selected' : '' }}>
                            {{ $d->name }} ({{ $d->district }} - {{ $d->address }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 2. JENIS PERMASALAHAN -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    2. Jenis Permasalahan Kualitas Air *
                </label>
                <select name="issue_type" required class="w-full px-4 py-3 rounded-2xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm font-medium">
                    <option value="keruh" {{ old('issue_type') === 'keruh' ? 'selected' : '' }}>Air Keruh / Ada Partikel Melayang</option>
                    <option value="berbau" {{ old('issue_type') === 'berbau' ? 'selected' : '' }}>Air Berbau (Anyir / Tanah / Klorin Menyengat)</option>
                    <option value="berasa" {{ old('issue_type') === 'berasa' ? 'selected' : '' }}>Rasa Air Aneh / Pahit / Asam</option>
                    <option value="lumut" {{ old('issue_type') === 'lumut' ? 'selected' : '' }}>Ditemukan Lumut / Endapan Kotoran di Galon</option>
                    <option value="serangga" {{ old('issue_type') === 'serangga' ? 'selected' : '' }}>Ditemukan Serangga / Hewan di Dalam Galon</option>
                    <option value="lainnya" {{ old('issue_type') === 'lainnya' ? 'selected' : '' }}>Lainnya / Higienitas Alat Buruk</option>
                </select>
            </div>

            <!-- 3. KRONOLOGI / DESKRIPSI -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    3. Kronologi & Detail Masalah *
                </label>
                <textarea name="description" rows="4" required placeholder="Jelaskan secara rinci permasalahan air (contoh: air berbau got saat dibuka, warna agak kekuningan, dibeli kemarin siang...)"
                          class="w-full px-4 py-3 rounded-2xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm">{{ old('description') }}</textarea>
            </div>

            <!-- 4. UNGGAH FOTO BUKTI -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    4. Unggah Foto Bukti Air / Galon (Opsional, Max 5MB)
                </label>
                <input type="file" name="photo" accept="image/*"
                       class="block w-full text-xs text-slate-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100">
                <span class="text-[11px] text-slate-400 mt-1 block">Format: JPG, PNG, WEBP. Lampirkan foto galon atau kondisi air untuk mempercepat investigasi dinkes.</span>
            </div>

            <!-- 5. TANGGAL PEMBELIAN & DATA PELAPOR -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal Pembelian Galon</label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Nama Lengkap Pelapor *</label>
                    <input type="text" name="reporter_name" required value="{{ old('reporter_name', auth()->user()->name ?? '') }}" placeholder="Nama Anda"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-600 mb-1">Nomor WhatsApp Aktif *</label>
                    <input type="text" name="reporter_phone" required value="{{ old('reporter_phone') }}" placeholder="Contoh: 081234567890 (Untuk konfirmasi hasil investigasi dinkes)"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>
            </div>

            <button type="submit" class="w-full py-4 bg-rose-600 hover:bg-rose-700 text-white rounded-2xl font-black text-sm shadow-lg shadow-rose-600/25 transition">
                Kirim Laporan Pengaduan ke Dinkes
            </button>
        </form>
    </div>
</div>
@endsection
