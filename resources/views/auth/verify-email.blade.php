<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PIL - Verification Required</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FEF6F0] font-sans antialiased text-[#1A1A1A]">
    <!-- Background Decor -->
    <div class="fixed top-0 right-0 w-80 h-80 bg-[#FFEDD5] rounded-full -mr-20 -mt-20 blur-3xl opacity-60"></div>
    <div class="fixed bottom-0 left-0 w-80 h-80 bg-[#FFEDD5] rounded-full -ml-20 -mb-20 blur-3xl opacity-60"></div>

    <div class="flex min-h-screen relative z-10 items-center justify-center p-8">
        <div class="max-w-md w-full bg-white/40 backdrop-blur-xl rounded-[2.5rem] border border-[#FFEDD5] p-10 shadow-2xl shadow-orange-500/5">
            <div class="text-center mb-10">
                <div class="logo-box mx-auto mb-8 w-20 h-20">
                    <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>

                <h1 class="text-3xl font-black text-[#1A1A1A] tracking-tight mb-4 uppercase">Verification Pending</h1>
                <p class="text-[#1A1A1A]/60 text-sm font-semibold leading-relaxed">
                    Thanks for initializing your admin account. Please click the link we just emailed to you to activate your terminal access.
                </p>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-8 bg-green-500/10 border border-green-500/20 rounded-2xl p-4 text-green-600 text-[10px] font-black uppercase tracking-widest text-center">
                    A new security token has been dispatched to your address.
                </div>
            @endif

            <div class="space-y-4">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn-primary w-full shadow-lg uppercase tracking-[0.2em] font-black text-xs">
                        {{ __('Resend Activation Token') }}
                    </button>
                </form>

                <div class="relative flex items-center justify-center py-4">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-[#FFEDD5]"></div></div>
                    <span class="relative px-4 bg-[#FEF6F0] text-[8px] font-black text-[#1A1A1A]/20 uppercase tracking-[0.2em]">Safety First</span>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-[10px] font-black text-[#1A1A1A]/40 hover:text-[#FF6B00] transition uppercase tracking-[0.2em] flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        {{ __('Back to Gate') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
