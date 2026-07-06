<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PIL - Point of Sale and Lending System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FEF6F0] font-sans antialiased min-h-screen flex flex-col items-center justify-center p-6 text-[#1A1A1A]">
    <!-- Decorative background elements -->
    <div class="fixed -top-20 -right-20 w-96 h-96 bg-[#FFEDD5] rounded-full blur-3xl opacity-50 pointer-events-none"></div>
    <div class="fixed -bottom-20 -left-20 w-96 h-96 bg-[#FFEDD5] rounded-full blur-3xl opacity-50 pointer-events-none"></div>

    <div class="max-w-4xl w-full relative z-10 text-center">
        <div class="logo-box mx-auto">
            <span class="text-white font-black text-2xl tracking-tighter">PIL</span>
        </div>

        <h2 class="text-[#FF6B00] font-black text-3xl mb-1 uppercase tracking-tighter">PIL</h2>
        <p class="text-[#1A1A1A]/40 text-xs font-bold uppercase tracking-widest mb-12">Point of Sale and Lending System</p>

        <h1 class="text-6xl lg:text-8xl font-black text-[#1A1A1A] uppercase tracking-tighter leading-[0.9] mb-8">
            Unified <br>Commerce <br><span class="text-[#FF6B00]">Engine</span>
        </h1>

        <p class="text-xl font-medium text-[#1A1A1A]/60 max-w-2xl mx-auto mb-12">
            The all-in-one platform for modern retail and micro-lending operations. Manage inventory, process sales, and automate loan cycles with AI precision.
        </p>

        <div class="flex flex-col sm:flex-row gap-6 justify-center">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn-primary">Go to Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn-primary px-12">Sign In</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-secondary px-12 bg-white flex items-center justify-center">Create Account</a>
                @endif
            @endauth
        </div>

        <footer class="mt-20 text-[#1A1A1A]/20 text-[10px] font-black uppercase tracking-[0.5em]">
            &copy; {{ date('Y') }} PIL Global Infrastructure
        </footer>
    </div>
</body>
</html>
