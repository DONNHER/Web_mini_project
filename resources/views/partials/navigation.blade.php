<nav class="bg-black text-brand shadow-lg border-b border-black/10" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center no-underline">
                    <span class="text-xl font-black text-brand uppercase tracking-tighter">LendingSystem</span>
                </a>

                <!-- Navigation Links -->
                <div class="hidden md:flex ml-10 space-x-4">
                    <a href="{{ route('home') }}" class="text-brand hover:bg-white/10 px-3 py-2 rounded-md transition font-bold no-underline uppercase text-xs">
                        Home
                    </a>
                    <a href="{{ route('loan_products.index') }}" class="text-brand hover:bg-white/10 px-3 py-2 rounded-md transition font-bold no-underline uppercase text-xs">
                        Loan Products
                    </a>

                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.users.index') }}" class="text-brand hover:bg-white/10 px-3 py-2 rounded-md transition font-bold no-underline uppercase text-xs">
                                Users
                            </a>
                            <a href="{{ route('admin.audit-logs.index') }}" class="text-brand hover:bg-white/10 px-3 py-2 rounded-md transition font-bold no-underline uppercase text-xs">
                                Audit Logs
                            </a>
                            <a href="{{ route('admin.reports.index') }}" class="text-brand hover:bg-white/10 px-3 py-2 rounded-md transition font-bold no-underline uppercase text-xs">
                                Reports
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Right Side -->
            <div class="flex items-center space-x-4">
                <div class="hidden md:flex items-center space-x-4">
                    @guest
                        <a href="{{ route('login') }}" class="text-brand hover:bg-white/10 px-3 py-2 rounded-md transition font-bold no-underline uppercase text-xs">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="bg-brand text-black px-4 py-2 rounded-md font-black hover:opacity-90 transition uppercase text-xs no-underline">
                            Register
                        </a>
                    @endguest

                    @auth
                        <!-- Notification Bell -->
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" class="p-2 text-brand hover:text-white transition relative">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <span id="notification-count" class="hidden absolute top-1 right-1 bg-white text-black text-[8px] font-black px-1.5 py-0.5 rounded-full border border-black animate-pulse">0</span>
                            </button>

                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 class="absolute right-0 mt-2 w-80 bg-black border border-brand/20 rounded-xl shadow-2xl z-50 overflow-hidden">
                                <div class="p-4 border-b border-brand/10 flex justify-between items-center">
                                    <h3 class="text-brand font-bold text-xs uppercase tracking-widest">Notifications</h3>
                                    <button onclick="markAllNotificationsRead()" class="text-[10px] text-brand/70 hover:text-brand font-bold uppercase no-underline">Mark all read</button>
                                </div>
                                <div id="notification-list" class="max-h-64 overflow-y-auto">
                                    <p class="p-4 text-center text-brand/50 text-xs italic">No new notifications</p>
                                </div>
                                <a href="{{ route('notifications.index') }}" class="block p-3 text-center bg-brand/5 text-brand/50 hover:text-brand text-[10px] font-bold uppercase tracking-widest transition no-underline">
                                    View All
                                </a>
                            </div>
                        </div>

                        <!-- Chatbot Link -->
                        <a href="{{ route('chatbot.index') }}" class="text-brand hover:bg-white/10 px-3 py-2 rounded-md transition flex items-center font-black no-underline uppercase text-xs">
                            <span class="mr-1">🤖</span> Assistant
                        </a>

                        @if(!auth()->user()->isAdmin())
                            <a href="{{ route('user.dashboard') }}" class="text-brand hover:bg-white/10 px-3 py-2 rounded-md transition font-bold no-underline uppercase text-xs">Dashboard</a>
                            <a href="{{ route('user.recommendations') }}" class="text-brand hover:bg-white/10 px-3 py-2 rounded-md transition flex items-center font-bold no-underline uppercase text-xs">✨ AI Insights</a>
                        @else
                            <a href="{{ route('admin.dashboard') }}" class="text-brand hover:bg-white/10 px-3 py-2 rounded-md transition font-bold no-underline uppercase text-xs">Admin</a>
                        @endif

                        <div class="flex items-center space-x-2">
                            <div class="h-8 w-8 rounded-full bg-brand flex items-center justify-center overflow-hidden border border-black/10">
                                @if(auth()->user()->avatar)
                                    <img src="{{ Storage::url(auth()->user()->avatar) }}" class="h-full w-full object-cover">
                                @else
                                    <span class="text-xs font-black text-black">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                @endif
                            </div>
                            <a href="{{ route('profile.edit') }}" class="text-brand hover:text-white transition font-bold no-underline text-xs uppercase">{{ auth()->user()->name }}</a>
                        </div>

                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="hover:bg-white/10 px-3 py-2 rounded-md transition text-[10px] uppercase font-black text-brand/50 hover:text-brand">
                                Logout
                            </button>
                        </form>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="flex items-center md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-brand hover:text-white p-2">
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
    <div :class="mobileMenuOpen ? 'block' : 'hidden'" class="md:hidden bg-black border-t border-brand/10">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="{{ route('home') }}" class="block px-3 py-2 text-brand font-bold uppercase text-xs no-underline">Home</a>
            <a href="{{ route('loan_products.index') }}" class="block px-3 py-2 text-brand font-bold uppercase text-xs no-underline">Loan Products</a>

            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 text-brand font-bold uppercase text-xs no-underline">Dashboard</a>
                    <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 text-brand font-bold uppercase text-xs no-underline">Users</a>
                @else
                    <a href="{{ route('user.dashboard') }}" class="block px-3 py-2 text-brand font-bold uppercase text-xs no-underline">Dashboard</a>
                    <a href="{{ route('chatbot.index') }}" class="block px-3 py-2 text-brand font-bold uppercase text-xs no-underline">AI Assistant</a>
                @endif

                <div class="pt-4 pb-1 border-t border-brand/10">
                    <div class="px-3 flex items-center">
                        <div class="h-8 w-8 rounded-full bg-brand flex items-center justify-center text-black font-black text-xs">{{ substr(auth()->user()->name, 0, 1) }}</div>
                        <div class="ml-3">
                            <div class="text-sm font-bold text-brand">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-brand/40">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                    <div class="mt-3 px-2 space-y-1">
                        <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-brand/60 hover:text-brand font-bold uppercase text-[10px] no-underline">Profile Settings</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-3 py-2 text-red-500 font-bold uppercase text-[10px]">Logout</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="block px-3 py-2 text-brand font-bold uppercase text-xs no-underline">Login</a>
                <a href="{{ route('register') }}" class="block px-3 py-2 text-brand font-bold uppercase text-xs no-underline">Register</a>
            @endauth
        </div>
    </div>
</nav>
