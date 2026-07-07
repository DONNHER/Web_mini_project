<nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-[#FFEDD5]" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex items-center">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center no-underline group">
                    <div class="bg-[#FF6B00] w-10 h-10 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/20 group-hover:scale-105 transition-transform">
                        <span class="text-white font-black text-xs tracking-tighter">PIL</span>
                    </div>
                </a>

                <!-- Navigation Links -->
                <div class="hidden md:flex ml-10 space-x-1">
                    <a href="{{ route('home') }}" class="text-[#1A1A1A]/60 hover:text-[#FF6B00] hover:bg-[#FF6B00]/5 px-4 py-2 rounded-xl transition font-extrabold no-underline uppercase text-[10px] tracking-widest">
                        Home
                    </a>
                    <a href="{{ route('loan_products.index') }}" class="text-[#1A1A1A]/60 hover:text-[#FF6B00] hover:bg-[#FF6B00]/5 px-4 py-2 rounded-xl transition font-extrabold no-underline uppercase text-[10px] tracking-widest">
                        Loan Management
                    </a>

                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.users.index') }}" class="text-[#1A1A1A]/60 hover:text-[#FF6B00] hover:bg-[#FF6B00]/5 px-4 py-2 rounded-xl transition font-extrabold no-underline uppercase text-[10px] tracking-widest">
                                Users
                            </a>
                            <a href="{{ route('admin.audit-logs.index') }}" class="text-[#1A1A1A]/60 hover:text-[#FF6B00] hover:bg-[#FF6B00]/5 px-4 py-2 rounded-xl transition font-extrabold no-underline uppercase text-[10px] tracking-widest">
                                Audit Logs
                            </a>
                            <a href="{{ route('admin.reports.index') }}" class="text-[#1A1A1A]/60 hover:text-[#FF6B00] hover:bg-[#FF6B00]/5 px-4 py-2 rounded-xl transition font-extrabold no-underline uppercase text-[10px] tracking-widest">
                                Reports
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Right Side -->
            <div class="flex items-center space-x-4">
                <div class="hidden md:flex items-center space-x-2">
                    @guest
                        <a href="{{ route('login') }}" class="text-[#1A1A1A] hover:text-[#FF6B00] px-4 py-2 rounded-xl transition font-extrabold no-underline uppercase text-[10px] tracking-widest">
                            Log In
                        </a>
                    @endguest

                    @auth
                        <!-- Notification Bell (Simple Blade Link) -->
                        <div class="relative">
                            <a href="{{ route('notifications.index') }}" class="p-2 text-[#1A1A1A]/40 hover:text-[#FF6B00] transition relative flex items-center no-underline">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                @php $unreadCount = auth()->user()->unreadNotifications()->count(); @endphp
                                @if($unreadCount > 0)
                                    <span class="absolute top-1.5 right-1.5 bg-[#FF6B00] text-white text-[7px] font-black px-1 py-0.5 rounded-full ring-2 ring-white">
                                        {{ $unreadCount }}
                                    </span>
                                @endif
                            </a>
                        </div>

                        @if(!auth()->user()->isAdmin())
                            <a href="{{ route('user.dashboard') }}" class="text-[#1A1A1A]/60 hover:text-[#FF6B00] px-3 py-2 rounded-xl transition font-extrabold no-underline uppercase text-[10px] tracking-widest">Dashboard</a>
                        @else
                            <a href="{{ route('admin.dashboard') }}" class="text-[#1A1A1A]/60 hover:text-[#FF6B00] px-3 py-2 rounded-xl transition font-extrabold no-underline uppercase text-[10px] tracking-widest">Dashboard</a>
                        @endif

                        <div class="h-8 w-px bg-[#FFEDD5] mx-2"></div>

                        <div class="flex items-center group">
                            <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 no-underline">
                                <div class="h-8 w-8 rounded-xl bg-[#FFEDD5] flex items-center justify-center overflow-hidden border border-[#FF6B00]/10 group-hover:border-[#FF6B00]/30 transition-colors">
                                    @if(auth()->user()->avatar)
                                        <img src="{{ Storage::url(auth()->user()->avatar) }}" class="h-full w-full object-cover">
                                    @else
                                        <span class="text-[10px] font-black text-[#FF6B00]">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                    @endif
                                </div>
                                <span class="text-[#1A1A1A] font-black no-underline text-[10px] uppercase tracking-widest hidden lg:block">{{ auth()->user()->name }}</span>
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="ml-4">
                                @csrf
                                <button type="submit" class="p-2 text-[#1A1A1A]/20 hover:text-red-500 transition-colors">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                </button>
                            </form>
                        </div>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="flex items-center md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-[#1A1A1A] p-2">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path :class="mobileMenuOpen ? 'hidden' : 'inline-flex'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="mobileMenuOpen ? 'inline-flex' : 'hidden'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileMenuOpen" x-cloak x-transition class="md:hidden bg-white border-t border-[#FFEDD5]" style="display: none;">
        <div class="px-4 pt-2 pb-6 space-y-2">
            <a href="{{ route('home') }}" class="block px-4 py-3 text-[#1A1A1A] font-black uppercase text-[10px] tracking-widest no-underline">Home</a>
            <a href="{{ route('loan_products.index') }}" class="block px-4 py-3 text-[#1A1A1A] font-black uppercase text-[10px] tracking-widest no-underline">Loan Management</a>
            @auth
                <a href="{{ route('user.dashboard') }}" class="block px-4 py-3 text-[#1A1A1A] font-black uppercase text-[10px] tracking-widest no-underline">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-3 text-red-500 font-black uppercase text-[10px] tracking-widest">Sign Out</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block px-4 py-3 text-[#FF6B00] font-black uppercase text-[10px] tracking-widest no-underline">Log In</a>
            @endauth
        </div>
    </div>
</nav>
