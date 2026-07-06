<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LendingSystem - Modern Financial Solutions</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#C06C3E] font-sans antialiased min-h-screen flex flex-col items-center justify-center p-6 text-black">
    <div class="max-w-4xl w-full">
        <header class="flex justify-between items-center mb-12">
            <div class="text-2xl font-black uppercase tracking-tighter text-white">LendingSystem</div>
            <nav class="flex gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="bg-black text-[#C06C3E] px-6 py-2 rounded-xl font-black text-xs uppercase tracking-widest no-underline hover:opacity-80 transition">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-white font-bold text-xs uppercase tracking-widest no-underline hover:underline">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="bg-black text-[#C06C3E] px-6 py-2 rounded-xl font-black text-xs uppercase tracking-widest no-underline hover:opacity-80 transition">Register</a>
                    @endif
                @endauth
            </nav>
        </header>

        <main class="bg-white/10 backdrop-blur-md rounded-[3rem] p-12 lg:p-20 border border-white/20 shadow-2xl relative overflow-hidden">
            <div class="relative z-10">
                <h1 class="text-6xl lg:text-8xl font-black text-white uppercase tracking-tighter leading-none mb-8">
                    Smart <br>Capital <br><span class="text-black">Solutions</span>
                </h1>
                <p class="text-xl font-medium text-white/70 max-w-lg mb-12">
                    Experience the future of personal and business lending with our AI-driven assessment system. Fast approvals, competitive rates.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('loan_products.index') }}" class="bg-black text-white px-10 py-5 rounded-2xl font-black uppercase tracking-widest text-sm text-center no-underline hover:scale-105 transition transform duration-300">
                        Explore Assets
                    </a>
                    @guest
                    <a href="{{ route('register') }}" class="bg-white text-black px-10 py-5 rounded-2xl font-black uppercase tracking-widest text-sm text-center no-underline border border-transparent hover:border-white hover:bg-transparent hover:text-white transition duration-300">
                        Get Started
                    </a>
                    @endguest
                </div>
            </div>

            <div class="absolute -right-20 -bottom-20 opacity-5 select-none pointer-events-none">
                <svg class="w-96 h-96" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
            </div>
        </main>

        <footer class="mt-12 text-center text-white/40 text-[10px] font-black uppercase tracking-[0.4em]">
            &copy; {{ date('Y') }} LendingSystem Global Infrastructure
        </footer>
    </div>
</body>
</html>
