<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PIL - Admin Sign In</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FEF6F0] font-sans antialiased text-[#1A1A1A]">
    <!-- Background Decor -->
    <div class="fixed top-0 right-0 w-80 h-80 bg-[#FFEDD5] rounded-full -mr-20 -mt-20 blur-3xl opacity-60"></div>
    <div class="fixed bottom-0 left-0 w-80 h-80 bg-[#FFEDD5] rounded-full -ml-20 -mb-20 blur-3xl opacity-60"></div>

    <div class="flex min-h-screen relative z-10">
        <!-- Left Side: Logo & Brand -->
        <div class="hidden lg:flex w-1/2 items-center justify-center border-r border-[#FFEDD5]">
            <div class="text-center">
                <div class="logo-box mx-auto mb-6">
                    <span class="text-white font-black text-2xl tracking-tighter">PIL</span>
                </div>
                <p class="text-[#1A1A1A]/40 text-[9px] font-black uppercase tracking-[0.4em]">Point of Sale and Lending System</p>
            </div>
        </div>

        <!-- Right Side: Form Content -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 lg:p-20">
            <div class="max-w-md w-full">
                <!-- Mobile Logo (Visible only on small screens) -->
                <div class="lg:hidden text-center mb-12">
                    <div class="logo-box mx-auto mb-4 w-16 h-16">
                        <span class="text-white font-black text-xl tracking-tighter">PIL</span>
                    </div>
                </div>

                <div class="text-left mb-10">
                    <h1 class="text-4xl font-black text-[#1A1A1A] tracking-tight mb-3">Admin Portal</h1>
                    <p class="text-[#1A1A1A]/50 text-base font-semibold tracking-tight">Sign in to access your PIL dashboard</p>
                </div>

                <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                    @csrf

                    <!-- Email Address -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-[#1A1A1A]/30">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Email Address" required autofocus class="w-full pl-12 border-[#FFEDD5] focus:border-[#FF6B00] focus:ring-0 rounded-2xl py-4 font-semibold text-[#1A1A1A] shadow-sm">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-[#1A1A1A]/30">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input id="password" type="password" name="password" placeholder="Password" required autocomplete="current-password" class="w-full pl-12 pr-12 border-[#FFEDD5] focus:border-[#FF6B00] focus:ring-0 rounded-2xl py-4 font-semibold text-[#1A1A1A] shadow-sm">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between py-2">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" name="remember" class="rounded-lg border-[#FFEDD5] text-[#FF6B00] focus:ring-[#FF6B00]">
                            <span class="ms-2 text-xs font-bold text-[#1A1A1A]/60">{{ __('Remember Me') }}</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[10px] font-black text-[#FF6B00] hover:underline uppercase tracking-widest">
                                {{ __('Forgot Password?') }}
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="btn-primary w-full shadow-lg group uppercase tracking-[0.2em] font-black text-xs">
                        {{ __('Sign In') }}
                    </button>
                </form>

                <div class="mt-16">
                    <div class="relative flex items-center justify-center mb-8">
                        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-[#FFEDD5]"></div></div>
                        <span class="relative px-4 bg-[#FEF6F0] text-[10px] font-black text-[#1A1A1A]/30 uppercase tracking-[0.2em]">New Admin?</span>
                    </div>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-secondary w-full flex items-center justify-center shadow-sm uppercase tracking-[0.2em] font-black text-xs">
                            {{ __('Create Admin Account') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
