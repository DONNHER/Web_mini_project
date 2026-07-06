<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PIL - Activation Successful</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FEF6F0] font-sans antialiased text-[#1A1A1A]">
    <!-- Background Decor -->
    <div class="fixed top-0 right-0 w-80 h-80 bg-[#FFEDD5] rounded-full -mr-20 -mt-20 blur-3xl opacity-60"></div>
    <div class="fixed bottom-0 left-0 w-80 h-80 bg-[#FFEDD5] rounded-full -ml-20 -mb-20 blur-3xl opacity-60"></div>

    <div class="flex min-h-screen relative z-10 items-center justify-center p-8">
        <div class="max-w-md w-full bg-white/60 backdrop-blur-2xl rounded-[3rem] border border-green-200 p-12 shadow-2xl text-center">
            <div class="mb-8">
                <div class="w-20 h-20 bg-green-500 rounded-3xl flex items-center justify-center mx-auto shadow-lg shadow-green-500/20">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>

            <h1 class="text-3xl font-black text-[#1A1A1A] tracking-tighter uppercase mb-2">Confirm Successful</h1>
            <p class="text-[#1A1A1A]/50 text-sm font-bold mb-10 tracking-tight leading-relaxed">
                The administrative terminal for <span class="text-[#1A1A1A]">{{ $name }}</span> has been successfully initialized and activated.
            </p>

            <div class="space-y-4">
                <p class="text-[10px] font-black text-[#1A1A1A]/30 uppercase tracking-[0.2em] mb-6">Continue to Platform?</p>

                <a href="{{ route('home') }}" class="btn-primary w-full flex items-center justify-center shadow-lg uppercase tracking-[0.2em] font-black text-xs no-underline">
                    Proceed to PIL
                </a>

                <a href="javascript:window.close();" onclick="if(window.opener){window.close()}else{history.back()}" class="block w-full py-4 text-[10px] font-black text-[#1A1A1A]/40 hover:text-red-500 transition-colors uppercase tracking-[0.2em] no-underline">
                    Cancel & Close
                </a>
            </div>
        </div>
    </div>
</body>
</html>
