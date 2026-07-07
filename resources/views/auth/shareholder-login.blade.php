<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PIL - Shareholder Sign In</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-[#FEF6F0] font-sans antialiased text-[#1A1A1A]">
    <!-- Background Decor -->
    <div class="fixed top-0 right-0 w-80 h-80 bg-[#FFEDD5] rounded-full -mr-20 -mt-20 blur-3xl opacity-60 pointer-events-none"></div>
    <div class="fixed bottom-0 left-0 w-80 h-80 bg-[#FFEDD5] rounded-full -ml-20 -mb-20 blur-3xl opacity-60 pointer-events-none"></div>

    <div class="flex min-h-screen relative z-10">
        <!-- Left Side: Logo & Brand -->
        <div class="hidden lg:flex w-1/2 items-center justify-center border-r border-black/5 bg-white/30 backdrop-blur-sm">
            <div class="text-center">
                <div class="bg-[#FF6B00] w-20 h-20 rounded-2xl flex items-center justify-center shadow-2xl shadow-orange-500/30 mx-auto mb-6">
                    <span class="text-white font-black text-3xl tracking-tighter">PIL</span>
                </div>
                <p class="text-black/40 text-[10px] font-black uppercase tracking-[0.4em]">Point of Sale and Lending System</p>
            </div>
        </div>

        <!-- Right Side: Form Content -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 lg:p-20">
            <div class="max-w-md w-full" x-data="{ showPassword: false }">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-12">
                    <div class="bg-[#FF6B00] w-16 h-16 rounded-2xl flex items-center justify-center shadow-xl mx-auto mb-4">
                        <span class="text-white font-black text-2xl tracking-tighter">PIL</span>
                    </div>
                </div>

                <div class="text-left mb-10">
                    <h1 class="text-4xl font-black text-black tracking-tight mb-2">Welcome back</h1>
                    <p class="text-black/40 text-sm font-bold tracking-tight">Sign in to access your shareholder dashboard</p>
                </div>

                <form method="POST" action="{{ route('login.post') }}" class="space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div class="space-y-2">
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-black/20 group-focus-within:text-[#FF6B00] transition-colors">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Email Address" required autofocus
                                   class="w-full pl-14 pr-6 py-5 bg-white border-black/5 focus:border-[#FF6B00] focus:ring-4 focus:ring-[#FF6B00]/5 rounded-2xl font-bold text-black shadow-sm outline-none transition-all placeholder-black/20">
                        </div>
                        <x-input-error :messages="$errors->get('email')" />
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-black/20 group-focus-within:text-[#FF6B00] transition-colors">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <input :type="showPassword ? 'text' : 'password'" id="password" name="password" placeholder="Password" required autocomplete="current-password"
                                   class="w-full pl-14 pr-14 py-5 bg-white border-black/5 focus:border-[#FF6B00] focus:ring-4 focus:ring-[#FF6B00]/5 rounded-2xl font-bold text-black shadow-sm outline-none transition-all placeholder-black/20">

                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-5 flex items-center text-black/20 hover:text-black transition-colors">
                                <svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer">
                            <input id="remember_me" type="checkbox" name="remember" class="rounded border-black/10 text-[#FF6B00] focus:ring-[#FF6B00] transition-colors w-4 h-4">
                            <span class="ms-3 text-xs font-black text-black/40 uppercase tracking-widest">{{ __('Remember Me') }}</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[10px] font-black text-[#FF6B00] hover:text-[#EA580C] no-underline uppercase tracking-widest transition-colors">
                                {{ __('Forgot Password?') }}
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="w-full bg-[#FF6B00] hover:bg-[#EA580C] text-white font-black py-5 rounded-2xl transition-all shadow-xl shadow-orange-500/20 uppercase tracking-widest text-xs flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        <span>{{ __('Sign In') }}</span>
                    </button>
                </form>

                <div class="mt-12">
                    <div class="relative flex items-center justify-center mb-8">
                        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-black/5"></div></div>
                        <span class="relative px-6 bg-[#FEF6F0] text-[9px] font-black text-black/20 uppercase tracking-[0.3em]">Partner with PIL</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
