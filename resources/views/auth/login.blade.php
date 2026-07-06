<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PIL - Sign In</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FEF6F0] font-sans antialiased min-h-screen flex flex-col items-center justify-center p-6 text-[#1A1A1A]">
    <!-- Background Decor -->
    <div class="fixed top-0 right-0 w-80 h-80 bg-[#FFEDD5] rounded-full -mr-20 -mt-20 blur-3xl opacity-60"></div>
    <div class="fixed bottom-0 left-0 w-80 h-80 bg-[#FFEDD5] rounded-full -ml-20 -mb-20 blur-3xl opacity-60"></div>

    <div class="auth-container relative z-10 text-center">
        <!-- Logo Section -->
        <div class="logo-box">
            <span class="text-white font-black text-2xl tracking-tighter">PIL</span>
        </div>
        <h2 class="text-[#FF6B00] font-black text-2xl mb-1 uppercase tracking-tighter">PIL</h2>
        <p class="text-[#1A1A1A]/40 text-[9px] font-black uppercase tracking-[0.2em] mb-12">Point of Sale and Lending System</p>

        <div class="text-left mb-8">
            <h1 class="text-3xl font-black text-[#1A1A1A] tracking-tight mb-2">Welcome back</h1>
            <p class="text-[#1A1A1A]/50 text-sm font-semibold tracking-tight">Sign in to access your PIL dashboard</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
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
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center text-[#1A1A1A]/30 cursor-pointer hover:text-[#FF6B00] transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between py-2">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" name="remember" class="rounded-lg border-[#FFEDD5] text-[#FF6B00] focus:ring-[#FF6B00]">
                    <span class="ms-2 text-xs font-bold text-[#1A1A1A]/60">{{ __('Remember Me') }}</span>
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs font-extrabold text-[#FF6B00] hover:underline uppercase tracking-tight">
                        {{ __('Forgot Password?') }}
                    </a>
                @endif
            </div>

            <button type="submit" class="btn-primary w-full shadow-lg group">
                <svg class="h-4 w-4 mr-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l1-2V8l-1-2m3 5h5M9 11l-5 5m0 0l5 5m-5-5V6"></path></svg>
                {{ __('Sign In') }}
            </button>
        </form>

        <div class="mt-12">
            <div class="relative flex items-center justify-center mb-8">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-[#FFEDD5]"></div></div>
                <span class="relative px-4 bg-[#FEF6F0] text-[10px] font-black text-[#1A1A1A]/30 uppercase tracking-[0.2em]">New to PIL?</span>
            </div>
            <a href="{{ route('register') }}" class="btn-secondary w-full flex items-center justify-center shadow-sm">
                {{ __('Create an Account') }}
            </a>
        </div>
    </div>
</body>
</html>
