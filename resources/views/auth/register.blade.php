@extends('layouts.auth')

@section('title', 'Daftar Warga - Galonku')

@section('content')
<div class="flex min-h-screen w-full bg-white">
    
    <!-- Left Side (Image Area - Sticky) -->
    <div class="hidden lg:block lg:w-1/2 sticky top-0 h-screen overflow-hidden border-r border-slate-200 z-10">
        <img src="{{ asset('images/mascot.jpg') }}" class="absolute inset-0 w-full h-full object-cover" alt="Galonku Mascot Background" />
        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent"></div>
        <div class="absolute bottom-16 left-12 xl:left-16 right-12 xl:right-16">
            <h2 class="text-5xl lg:text-6xl font-black text-white leading-[1.05] tracking-tight drop-shadow-lg">PESAN.<br>PANTAU.<br>SEHAT.</h2>
            <p class="text-brand-100 mt-4 text-lg font-medium opacity-90 max-w-sm">
                Bergabunglah dan temukan Depot Air Minum Tersertifikasi di sekitar Anda.
            </p>
        </div>
    </div>
    
    <!-- Right Side (Form Area) -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-16 min-h-screen bg-white">
        
        <div class="w-full max-w-md py-10">
            <!-- Logo -->
            <div class="flex flex-col items-center justify-center mb-8">
                <img src="{{ asset('images/logo-symbol.png') }}" alt="Logo" class="h-12 mb-3" />
                <span class="text-xs font-black tracking-widest text-slate-800 uppercase px-3 py-1 bg-slate-100 rounded-full">Daftar Warga</span>
            </div>
            
            <div class="text-center mb-8">
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">CREATE ACCOUNT</h1>
                <p class="text-sm text-slate-500 mt-2 font-medium">Isi data diri Anda untuk membuat akun baru</p>
            </div>
            
            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-50 text-rose-600 text-sm font-medium border border-rose-200 text-center">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('register.submit') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2 ml-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required 
                           class="w-full bg-[#f4f7f9] border-transparent rounded-[1rem] px-5 py-3.5 text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-slate-300 focus:ring-4 focus:ring-brand-50 transition-all" 
                           placeholder="Contoh: Budi Santoso">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2 ml-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required 
                           class="w-full bg-[#f4f7f9] border-transparent rounded-[1rem] px-5 py-3.5 text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-slate-300 focus:ring-4 focus:ring-brand-50 transition-all" 
                           placeholder="Masukkan email aktif">
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2 ml-1">Password</label>
                    <input type="password" name="password" required 
                           class="w-full bg-[#f4f7f9] border-transparent rounded-[1rem] px-5 py-3.5 text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-slate-300 focus:ring-4 focus:ring-brand-50 transition-all" 
                           placeholder="Minimal 6 karakter">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2 ml-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required 
                           class="w-full bg-[#f4f7f9] border-transparent rounded-[1rem] px-5 py-3.5 text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-slate-300 focus:ring-4 focus:ring-brand-50 transition-all" 
                           placeholder="Ketik ulang password">
                </div>
                
                <div class="pt-4">
                    <button type="submit" class="w-full py-4 bg-black hover:bg-slate-800 text-white rounded-[1rem] text-sm font-bold shadow-[0_8px_16px_rgba(0,0,0,0.15)] hover:shadow-[0_4px_8px_rgba(0,0,0,0.1)] transition-all">
                        Daftar Akun Baru
                    </button>
                </div>
            </form>
            
            <div class="mt-8 text-center text-xs font-semibold text-slate-500">
                Sudah punya akun? <a href="{{ route('login') }}" class="text-black font-bold hover:underline">Masuk di sini</a>
            </div>
            
        </div>
    </div>
</div>
@endsection
