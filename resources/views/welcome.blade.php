<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PIL - Point of Sale and Lending System</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Critical Internal CSS to prevent layout explosion if assets fail -->
    <style>
        body { background-color: #FEF6F0; margin: 0; padding: 0; font-family: 'Instrument Sans', sans-serif; color: #1A1A1A; }
        .decor-circle { position: fixed; width: 400px; height: 400px; background-color: #FFEDD5; border-radius: 50%; filter: blur(64px); opacity: 0.5; z-index: -10; pointer-events: none; }
        .decor-top { top: -100px; right: -100px; }
        .decor-bottom { bottom: -100px; left: -100px; }
        .logo-box { background: linear-gradient(135deg, #FF6B00 0%, #FF8533 100%); border-radius: 1.5rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px -5px rgba(255, 107, 0, 0.4); }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Debug Info -->
    @if(config('app.debug'))
        <div style="position: fixed; top: 0; left: 0; background: rgba(0,0,0,0.9); color: #0f0; padding: 10px; font-family: monospace; z-index: 9999; font-size: 10px; border: 1px solid #0f0;">
            URL: {{ config('app.url') }}<br>
            ENV: {{ config('app.env') }}<br>
            SECURE: {{ request()->isSecure() ? 'YES' : 'NO' }}<br>
            VITE_URL: {{ env('VITE_DEV_SERVER_URL') }}<br>
            HOT_FILE: {{ file_exists(public_path('hot')) ? 'YES' : 'NO' }}<br>
            MANIFEST: {{ file_exists(public_path('build/manifest.json')) ? 'YES' : 'NO' }}<br>
            CSS: {{ Vite::asset('resources/css/app.css') }}
        </div>
    @endif
</head>
<body class="bg-cream font-sans antialiased min-h-screen flex flex-col items-center justify-center p-6 text-[#1A1A1A]">
    <!-- Background Decor -->
    <div class="decor-circle decor-top"></div>
    <div class="decor-circle decor-bottom"></div>

    <div class="max-w-4xl w-full relative z-10 text-center">
        <div class="logo-box mx-auto w-20 h-20 mb-6">
            <span class="text-white font-black text-2xl tracking-tighter">PIL</span>
        </div>

        <h2 class="text-brand font-black text-3xl mb-1 uppercase tracking-tighter">PIL</h2>
        <p class="text-[#1A1A1A]/40 text-[10px] font-black uppercase tracking-[0.3em] mb-12">Point of Sale and Lending System</p>

        <h1 class="text-6xl lg:text-8xl font-black text-[#1A1A1A] uppercase tracking-tighter leading-[0.9] mb-8">
            Unified <br>Commerce <br><span class="text-brand">Engine</span>
        </h1>

        <p class="text-xl font-medium text-[#1A1A1A]/60 max-w-2xl mx-auto mb-12 leading-relaxed">
            The all-in-one platform for modern retail and micro-lending operations. Manage inventory, process sales, and automate loan cycles with AI precision.
        </p>

        <div class="flex flex-col sm:flex-row gap-6 justify-center">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn-primary no-underline">Launch Console</a>
            @else
                <a href="{{ route('login') }}" class="btn-primary px-12 no-underline">Sign In</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-secondary px-12 bg-white no-underline">Create Account</a>
                @endif
            @endauth
        </div>

        <footer class="mt-24 text-[#1A1A1A]/20 text-[9px] font-black uppercase tracking-[0.5em]">
            &copy; {{ date('Y') }} PIL Global Infrastructure &bull; Node PIL-{{ config('app.env') }}
        </footer>
    </div>
</body>
</html>
