@extends('layouts.auth')

@section('title', 'Daftar Mitra Depot - Galonku')

@section('content')
<div class="flex min-h-screen w-full bg-white">
    
    <!-- Left Side (Image Area - Sticky) -->
    <div class="hidden lg:block lg:w-1/2 sticky top-0 h-screen overflow-hidden border-r border-slate-200 z-10">
        <img src="{{ asset('images/mascot.jpg') }}" class="absolute inset-0 w-full h-full object-cover" alt="Galonku Mascot Background" />
        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent"></div>
        <div class="absolute bottom-16 left-12 xl:left-16 right-12 xl:right-16">
            <h2 class="text-5xl lg:text-6xl font-black text-white leading-[1.05] tracking-tight drop-shadow-lg">KELOLA.<br>LAYANI.<br>TUMBUH.</h2>
            <p class="text-brand-100 mt-4 text-lg font-medium opacity-90 max-w-sm">
                Daftarkan Depot Air Minum Anda dan jangkau lebih banyak pelanggan di Surabaya.
            </p>
        </div>
    </div>
    
    <!-- Right Side (Form Area) -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-12 min-h-screen bg-white">
        
        <div class="w-full max-w-lg py-8">
            <!-- Logo -->
            <div class="flex flex-col items-center justify-center mb-8">
                <img src="{{ asset('images/logo-symbol.png') }}" alt="Logo" class="h-12 mb-3" />
                <span class="text-xs font-black tracking-widest text-slate-800 uppercase px-3 py-1 bg-slate-100 rounded-full">Pendaftaran Mitra Depot</span>
            </div>
            
            <div class="text-center mb-8">
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">GABUNG JADI MITRA</h1>
                <p class="text-sm text-slate-500 mt-2 font-medium">Lengkapi profil Anda dan informasi depot untuk mendaftar</p>
            </div>
            
            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-50 text-rose-600 text-sm font-medium border border-rose-200 text-center">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('depot.register.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 space-y-4">
                    <h3 class="text-sm font-black text-slate-800 mb-2 border-b border-slate-200 pb-2">1. Data Pemilik Akun</h3>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2 ml-1">Nama Pemilik / Admin</label>
                        <input type="text" name="owner_name" value="{{ old('owner_name') }}" required 
                            class="w-full bg-white border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-800 focus:outline-none focus:border-slate-300 focus:ring-4 focus:ring-brand-50 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2 ml-1">Email Aktif</label>
                        <input type="email" name="email" value="{{ old('email') }}" required 
                            class="w-full bg-white border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-800 focus:outline-none focus:border-slate-300 focus:ring-4 focus:ring-brand-50 transition-all">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2 ml-1">Password</label>
                            <input type="password" name="password" required 
                                class="w-full bg-white border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-800 focus:outline-none focus:border-slate-300 focus:ring-4 focus:ring-brand-50 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2 ml-1">Konfirmasi Pass</label>
                            <input type="password" name="password_confirmation" required 
                                class="w-full bg-white border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-800 focus:outline-none focus:border-slate-300 focus:ring-4 focus:ring-brand-50 transition-all">
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 space-y-4">
                    <h3 class="text-sm font-black text-slate-800 mb-2 border-b border-slate-200 pb-2">2. Profil Depot Air Minum</h3>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2 ml-1">Nama Depot</label>
                        <input type="text" name="depot_name" value="{{ old('depot_name') }}" required placeholder="Contoh: Depot Banyu Biru"
                            class="w-full bg-white border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-800 focus:outline-none focus:border-slate-300 focus:ring-4 focus:ring-brand-50 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2 ml-1">Nomor WhatsApp Depot</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="0812xxxxxx"
                            class="w-full bg-white border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-800 focus:outline-none focus:border-slate-300 focus:ring-4 focus:ring-brand-50 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2 ml-1">Kecamatan (Di Surabaya)</label>
                        <select name="district" required class="w-full bg-white border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-800 focus:outline-none focus:border-slate-300 focus:ring-4 focus:ring-brand-50 transition-all">
                            <option value="">Pilih Kecamatan...</option>
                            <option value="Gubeng">Gubeng</option>
                            <option value="Tegalsari">Tegalsari</option>
                            <option value="Simokerto">Simokerto</option>
                            <option value="Genteng">Genteng</option>
                            <option value="Sukolilo">Sukolilo</option>
                            <option value="Rungkut">Rungkut</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2 ml-1">Alamat Lengkap Depot</label>
                        <textarea name="address" rows="2" required placeholder="Jl. Contoh No 123, RT 1 RW 2..."
                            class="w-full bg-white border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-800 focus:outline-none focus:border-slate-300 focus:ring-4 focus:ring-brand-50 transition-all">{{ old('address') }}</textarea>
                    </div>
                </div>

                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 space-y-4">
                    <h3 class="text-sm font-black text-slate-800 mb-2 border-b border-slate-200 pb-2">3. Dokumen Verifikasi (SLHS)</h3>
                    <p class="text-xs text-slate-500 mb-3 leading-relaxed">Depot Anda harus mendapatkan verifikasi Dinas Kesehatan untuk dapat tampil di publik. Silakan unggah Sertifikat Laik Higiene Sanitasi (SLHS) terbaru Anda.</p>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2 ml-1">Unggah Sertifikat / Bukti Lab (.jpg, .png)</label>
                        <input type="file" name="certificate" required accept="image/*"
                            class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs font-medium text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-white hover:file:bg-slate-700 focus:outline-none">
                    </div>
                </div>
                
                <div class="pt-4">
                    <button type="submit" class="w-full py-4 bg-black hover:bg-slate-800 text-white rounded-[1rem] text-sm font-bold shadow-[0_8px_16px_rgba(0,0,0,0.15)] hover:shadow-[0_4px_8px_rgba(0,0,0,0.1)] transition-all">
                        Daftarkan Depot Saya
                    </button>
                </div>
            </form>
            
            <div class="mt-8 text-center text-xs font-semibold text-slate-500">
                Sudah punya akun mitra? <a href="{{ route('depot.login') }}" class="text-black font-bold hover:underline">Masuk di sini</a>
            </div>
            
        </div>
    </div>
</div>
@endsection
