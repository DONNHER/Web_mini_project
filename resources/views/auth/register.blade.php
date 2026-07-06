<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PIL - Create Account</title>
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
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-12">
                    <div class="logo-box mx-auto mb-4 w-16 h-16">
                        <span class="text-white font-black text-xl tracking-tighter">PIL</span>
                    </div>
                </div>

                <div class="text-left mb-10">
                    <h1 class="text-4xl font-black text-[#1A1A1A] tracking-tight mb-3">Join the Platform</h1>
                    <p class="text-[#1A1A1A]/50 text-base font-semibold tracking-tight">Create your PIL infrastructure account</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-5" x-data="{ showRequirements: false }">
                    @csrf

                    <!-- Name -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-[#1A1A1A]/30">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Full Name" required autofocus autocomplete="name" class="w-full pl-12 pr-10 border-[#FFEDD5] focus:border-[#FF6B00] focus:ring-0 rounded-2xl py-4 font-semibold text-[#1A1A1A] shadow-sm">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-red-500 font-bold text-lg">*</span>
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email Address -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-[#1A1A1A]/30">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Email Address" required autocomplete="username" class="w-full pl-12 pr-10 border-[#FFEDD5] focus:border-[#FF6B00] focus:ring-0 rounded-2xl py-4 font-semibold text-[#1A1A1A] shadow-sm">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-red-500 font-bold text-lg">*</span>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Phone Number -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-[#1A1A1A]/30">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 011.94.445l-.992 2.84a1 1 0 01-1.24.58L6.22 5.22a10.603 10.603 0 005.56 5.56l1.76-1.76a1 1 0 011.24-.58l2.84.992a1 1 0 01.445 1.94V19a2 2 0 01-2 2h-2.28a1 1 0 01-1.94-.445l.992-2.84a1 1 0 011.24-.58l1.76 1.76a10.603 10.603 0 00-5.56-5.56l-1.76 1.76a1 1 0 01-1.24.58l-2.84-.992a1 1 0 01-.445-1.94V5z"></path></svg>
                        </div>
                        <input id="phone" type="text" name="phone" value="{{ old('phone') }}" placeholder="Mobile Number (e.g. +639...)" required class="w-full pl-12 pr-10 border-[#FFEDD5] focus:border-[#FF6B00] focus:ring-0 rounded-2xl py-4 font-semibold text-[#1A1A1A] shadow-sm">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-red-500 font-bold text-lg">*</span>
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <!-- Invite Code -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-[#1A1A1A]/30">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                        </div>
                        <input id="invite_code" type="text" name="invite_code" placeholder="Admin Invite Code" required class="w-full pl-12 pr-10 border-[#FFEDD5] focus:border-[#FF6B00] focus:ring-0 rounded-2xl py-4 font-semibold text-[#1A1A1A] shadow-sm">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-red-500 font-bold text-lg">*</span>
                        <x-input-error :messages="$errors->get('invite_code')" class="mt-2" />
                    </div>

                    <!-- Password Requirements -->
                    <div x-show="showRequirements" x-transition class="text-[10px] font-black text-red-600 uppercase tracking-wider space-y-1 mb-2 px-2">
                        <p class="m-0">• The password field must contain at least one uppercase and one lowercase letter.</p>
                        <p class="m-0">• The password field must contain at least one letter.</p>
                        <p class="m-0">• The password field must contain at least one symbol.</p>
                    </div>

                    <!-- Password -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-[#1A1A1A]/30">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input id="password" type="password" name="password" placeholder="Security Token (Password)" required autocomplete="new-password"
                            @focus="showRequirements = true"
                            @blur="showRequirements = false"
                            class="w-full pl-12 pr-10 border-[#FFEDD5] focus:border-[#FF6B00] focus:ring-0 rounded-2xl py-4 font-semibold text-[#1A1A1A] shadow-sm">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-red-500 font-bold text-lg">*</span>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-[#1A1A1A]/30">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04M12 2.944V12m0 0l4 4m-4-4l-4 4"></path></svg>
                        </div>
                        <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Confirm Token" required autocomplete="new-password" class="w-full pl-12 pr-10 border-[#FFEDD5] focus:border-[#FF6B00] focus:ring-0 rounded-2xl py-4 font-semibold text-[#1A1A1A] shadow-sm">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-red-500 font-bold text-lg">*</span>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>


                    <button type="submit" class="btn-primary w-full shadow-lg mt-4 uppercase tracking-[0.2em] font-black text-xs">
                        {{ __('Initialize Account') }}
                    </button>
                </form>

                <div class="mt-12">
                    <div class="relative flex items-center justify-center mb-8">
                        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-[#FFEDD5]"></div></div>
                        <span class="relative px-4 bg-[#FEF6F0] text-[10px] font-black text-[#1A1A1A]/30 uppercase tracking-[0.2em]">Already have access?</span>
                    </div>
                    <a href="{{ route('login') }}" class="btn-secondary w-full flex items-center justify-center shadow-sm uppercase tracking-[0.2em] font-black text-xs">
                        {{ __('Sign In to Terminal') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
