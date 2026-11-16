<header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 transition-all duration-200">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            
            <!-- Brand Logo -->
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 sm:gap-3 group">
                    <!-- Galonku Symbol Icon -->
                    <img src="{{ asset('images/logo-symbol.png') }}" 
                         alt="Galonku Icon" 
                         class="h-10 sm:h-11 w-auto object-contain group-hover:scale-105 transition-transform duration-200">
                    
                    <!-- Galonku Text Logo -->
                    <img src="{{ asset('images/logo-text.png') }}" 
                         alt="Galonku - Temukan DAMIU Tersertifikasi" 
                         class="h-8 sm:h-9 w-auto object-contain">

                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-900 border border-blue-200">
                        SURABAYA
                    </span>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center gap-1">
                <a href="{{ route('depots.index') }}" 
                   class="px-3.5 py-2 text-sm font-semibold rounded-xl transition-colors {{ request()->routeIs('depots.*') || request()->is('/') ? 'text-brand-700 bg-brand-50/80 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                   Peta & Depot (GPS)
                </a>
            </nav>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2.5">
                <!-- Check Order Status Direct Link -->
                <a href="{{ route('orders.index') }}" 
                   class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-slate-800 bg-slate-100 hover:bg-brand-50 hover:text-brand-800 rounded-xl transition border border-slate-200/80 shadow-sm">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span>Cek Antrean</span>
                    @if(!empty($navbarActiveCount) && $navbarActiveCount > 0)
                        <span class="w-2 h-2 rounded-full bg-brand-600 animate-ping"></span>
                    @endif
                </a>

                <!-- Dinkes Certified Indicator Badge -->
                <div class="hidden lg:flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>SLHS DAMIU Surabaya</span>
                </div>

                @auth
                    @if(auth()->user()->role === 'depot')
                        <a href="{{ route('depot.dashboard') }}" class="px-3.5 py-2 bg-brand-700 text-white rounded-xl text-xs font-bold transition hover:bg-brand-800 shadow-sm">
                            Dasbor Mitra Depot
                        </a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-2 text-slate-500 hover:text-rose-600 text-xs font-bold transition">Logout</button>
                    </form>
                @else
                    <div class="relative group">
                        <button class="px-3.5 py-2 text-slate-700 hover:text-brand-700 text-xs font-bold transition flex items-center gap-1 bg-slate-100 rounded-xl border border-slate-200">
                            Masuk Akun
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <!-- Dropdown: Customer & Depot -->
                        <div class="absolute right-0 mt-2 w-48 bg-white border border-slate-200 rounded-2xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top-right z-50">
                            <div class="p-2 flex flex-col gap-1">
                                <a href="{{ route('login') }}" class="px-3 py-2 text-xs font-bold text-slate-700 hover:bg-brand-50 hover:text-brand-700 rounded-xl transition">
                                    👤 Masuk Warga
                                </a>
                                <a href="{{ route('depot.login') }}" class="px-3 py-2 text-xs font-bold text-slate-700 hover:bg-brand-50 hover:text-brand-700 rounded-xl transition">
                                    🏢 Portal Mitra Depot
                                </a>
                                <div class="border-t border-slate-100 my-1"></div>
                                <a href="{{ route('register') }}" class="px-3 py-1.5 text-[11px] font-semibold text-slate-500 hover:text-brand-600">
                                    Daftar Akun Baru &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                @endauth
            </div>

        </div>
    </div>
</header>


