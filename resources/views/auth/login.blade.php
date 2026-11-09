@extends('layouts.auth')

@php
    $pageTitle = match($type) {
        'depot' => 'Portal Mitra Depot - Galonku',
        default => 'Login Warga - Galonku'
    };
    
    $overlayText = match($type) {
        'depot' => 'KELOLA.<br>LAYANI.<br>TUMBUH.',
        default => 'PESAN.<br>PANTAU.<br>SEHAT.'
    };
    
    $portalName = match($type) {
        'depot' => 'Portal Mitra Depot',
        default => 'Login Warga'
    };
@endphp

@section('title', $pageTitle)

@section('content')
<div class="flex min-h-screen w-full bg-white">
    
    <!-- Left Side (Image Area - Sticky) -->
    <div class="hidden lg:block lg:w-1/2 sticky top-0 h-screen overflow-hidden border-r border-slate-200 z-10">
        <img src="{{ asset('images/mascot.jpg') }}" class="absolute inset-0 w-full h-full object-cover" alt="Galonku Mascot Background" />
        
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent"></div>
        
        <!-- Text Overlay -->
        <div class="absolute bottom-16 left-12 xl:left-16 right-12 xl:right-16">
            <h2 class="text-5xl lg:text-6xl font-black text-white leading-[1.05] tracking-tight drop-shadow-lg">{!! $overlayText !!}</h2>
            <p class="text-brand-100 mt-4 text-lg font-medium opacity-90 max-w-sm">
                {{ $type == 'user' ? 'Platform pemesanan air minum isi ulang tersertifikasi Dinas Kesehatan.' : 'Portal manajemen sistem informasi Galonku terpadu.' }}
            </p>
        </div>
    </div>
    
    <!-- Right Side (Form Area) -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-16 min-h-screen bg-white">
        
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="flex flex-col items-center justify-center mb-10">
                <img src="{{ asset('images/logo-symbol.png') }}" alt="Logo" class="h-12 mb-3" />
                <span class="text-xs font-black tracking-widest text-slate-800 uppercase px-3 py-1 bg-slate-100 rounded-full">{{ $portalName }}</span>
            </div>
            
            <div class="text-center mb-10">
                <h1 class="text-3xl lg:text-4xl font-black text-slate-900 tracking-tight">WELCOME BACK</h1>
                <p class="text-sm text-slate-500 mt-2 font-medium">Enter your email and password to access your account</p>
            </div>
            
            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-50 text-rose-600 text-sm font-medium border border-rose-200 text-center animate-pulse">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('authenticate') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">
                
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2 ml-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required 
                           class="w-full bg-[#f4f7f9] border-transparent rounded-[1rem] px-5 py-4 text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-slate-300 focus:ring-4 focus:ring-brand-50 transition-all" 
                           placeholder="Enter your email">
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2 ml-1">Password</label>
                    <input type="password" name="password" required 
                           class="w-full bg-[#f4f7f9] border-transparent rounded-[1rem] px-5 py-4 text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-slate-300 focus:ring-4 focus:ring-brand-50 transition-all" 
                           placeholder="Enter your password">
                </div>
                
                <div class="flex items-center justify-between text-xs px-1 pt-1">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" class="w-4 h-4 rounded text-black bg-slate-100 border-slate-300 focus:ring-black focus:ring-2 cursor-pointer transition">
                        <span class="text-slate-500 font-semibold group-hover:text-slate-800 transition">Remember me</span>
                    </label>
                    <a href="#" class="font-bold text-slate-500 hover:text-black transition">Forgot Password?</a>
                </div>
                
                <div class="pt-4">
                    <button type="submit" class="w-full py-4 bg-black hover:bg-slate-800 text-white rounded-[1rem] text-sm font-bold shadow-[0_8px_16px_rgba(0,0,0,0.15)] hover:shadow-[0_4px_8px_rgba(0,0,0,0.1)] transition-all transform hover:-translate-y-0.5 active:translate-y-0">
                        Sign In
                    </button>
                </div>
                
                <div>
                    <button type="button" class="w-full py-4 bg-white border-2 border-slate-100 hover:border-slate-200 hover:bg-slate-50 text-slate-700 rounded-[1rem] text-sm font-bold shadow-sm flex items-center justify-center gap-3 transition-all">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M23.766 12.2764C23.766 11.4607 23.6999 10.6406 23.5588 9.83807H12.24V14.4591H18.7217C18.4528 15.9494 17.5885 17.2678 16.323 18.1056V21.1039H20.19C22.4608 19.0139 23.766 15.9274 23.766 12.2764Z" fill="#4285F4"/>
                            <path d="M12.2401 24.0008C15.4766 24.0008 18.2059 22.9382 20.1945 21.1039L16.3276 18.1056C15.2513 18.8375 13.8627 19.252 12.2445 19.252C9.11388 19.252 6.45946 17.1399 5.50705 14.3003H1.5166V17.3912C3.55371 21.4434 7.7029 24.0008 12.2401 24.0008Z" fill="#34A853"/>
                            <path d="M5.50253 14.3003C5.00015 12.8099 5.00015 11.1961 5.50253 9.70575V6.61481H1.51649C-0.18551 10.0056 -0.18551 14.0004 1.51649 17.3912L5.50253 14.3003Z" fill="#FBBC05"/>
                            <path d="M12.2401 4.74966C13.9509 4.7232 15.6044 5.36697 16.8434 6.54867L20.2695 3.12262C18.1001 1.0855 15.2208 -0.034466 12.2401 0.000808666C7.7029 0.000808666 3.55371 2.55822 1.5166 6.61481L5.50264 9.70575C6.45064 6.86173 9.10947 4.74966 12.2401 4.74966Z" fill="#EA4335"/>
                        </svg>
                        Sign in with Google
                    </button>
                </div>
            </form>
            
            <div class="mt-10 text-center text-xs font-semibold text-slate-500">
                Don't have an account? 
                <a href="{{ $type === 'depot' ? route('depot.register') : route('register') }}" class="text-black font-bold hover:underline">Sign up</a>
            </div>

            <!-- Role switcher -->
            <div class="mt-12 pt-6 border-t border-slate-100 flex justify-center gap-4">
                <a href="{{ route('login') }}" class="text-[10px] font-bold tracking-wide {{ $type == 'user' ? 'text-black bg-slate-100 px-3 py-1.5 rounded-lg' : 'text-slate-400 hover:text-slate-600 px-3 py-1.5' }} transition">Login Warga</a>
                <a href="{{ route('depot.login') }}" class="text-[10px] font-bold tracking-wide {{ $type == 'depot' ? 'text-black bg-slate-100 px-3 py-1.5 rounded-lg' : 'text-slate-400 hover:text-slate-600 px-3 py-1.5' }} transition">Portal Mitra Depot</a>
            </div>
            
        </div>
    </div>
</div>
@endsection
