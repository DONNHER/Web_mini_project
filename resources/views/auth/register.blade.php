<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PIL - Create Account</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
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
            <div class="max-w-md w-full" x-data="addressPicker()">
                <div class="text-left mb-10">
                    <h1 class="text-4xl font-black text-[#1A1A1A] tracking-tight mb-3">Join the Platform</h1>
                    <p class="text-[#1A1A1A]/50 text-base font-semibold tracking-tight">Create your PIL infrastructure account</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <!-- Name -->
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-black/20 group-focus-within:text-[#FF6B00] transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Full Name" required autofocus class="w-full pl-14 pr-6 py-4 bg-white border-black/5 focus:border-[#FF6B00] focus:ring-4 focus:ring-[#FF6B00]/5 rounded-2xl font-bold text-black shadow-sm outline-none transition-all placeholder-black/20">
                    </div>
                    <x-input-error :messages="$errors->get('name')" />

                    <!-- Email -->
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-black/20 group-focus-within:text-[#FF6B00] transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Email Address" required class="w-full pl-14 pr-6 py-4 bg-white border-black/5 focus:border-[#FF6B00] focus:ring-4 focus:ring-[#FF6B00]/5 rounded-2xl font-bold text-black shadow-sm outline-none transition-all placeholder-black/20">
                    </div>
                    <x-input-error :messages="$errors->get('email')" />

                    <!-- Phone -->
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-black/20 group-focus-within:text-[#FF6B00] transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 011.94.445l-.992 2.84a1 1 0 01-1.24.58L6.22 5.22a10.603 10.603 0 005.56 5.56l1.76-1.76a1 1 0 011.24-.58l2.84.992a1 1 0 01.445 1.94V19a2 2 0 01-2 2h-2.28a1 1 0 01-1.94-.445l.992-2.84a1 1 0 011.24-.58l1.76 1.76a10.603 10.603 0 00-5.56-5.56l-1.76 1.76a1 1 0 01-1.24.58l-2.84-.992a1 1 0 01-.445-1.94V5z"></path></svg>
                        </div>
                        <input id="phone" type="text" name="phone" value="{{ old('phone') }}" placeholder="Mobile Number" required class="w-full pl-14 pr-6 py-4 bg-white border-black/5 focus:border-[#FF6B00] focus:ring-4 focus:ring-[#FF6B00]/5 rounded-2xl font-bold text-black shadow-sm outline-none transition-all placeholder-black/20">
                    </div>
                    <x-input-error :messages="$errors->get('phone')" />

                    <div class="space-y-4 pt-4 border-t border-black/5">
                        <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-black/40">Geographical Node</h3>

                        <div class="grid grid-cols-2 gap-4">
                            <select name="region" x-model="selectedRegion" @change="fetchProvinces()" class="bg-white border-black/5 rounded-2xl px-6 py-4 font-bold text-black text-xs outline-none focus:ring-4 focus:ring-[#FF6B00]/5">
                                <option value="">Select Region</option>
                                <template x-for="region in regions" :key="region.code">
                                    <option :value="region.code" x-text="region.name"></option>
                                </template>
                            </select>

                            <select name="province" x-model="selectedProvince" @change="fetchCities()" class="bg-white border-black/5 rounded-2xl px-6 py-4 font-bold text-black text-xs outline-none focus:ring-4 focus:ring-[#FF6B00]/5">
                                <option value="">Select Province</option>
                                <template x-for="province in provinces" :key="province.code">
                                    <option :value="province.code" x-text="province.name"></option>
                                </template>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <select name="city" x-model="selectedCity" @change="fetchBarangays()" class="bg-white border-black/5 rounded-2xl px-6 py-4 font-bold text-black text-xs outline-none focus:ring-4 focus:ring-[#FF6B00]/5">
                                <option value="">Select City</option>
                                <template x-for="city in cities" :key="city.code">
                                    <option :value="city.code" x-text="city.name"></option>
                                </template>
                            </select>

                            <select name="barangay" x-model="selectedBarangay" class="bg-white border-black/5 rounded-2xl px-6 py-4 font-bold text-black text-xs outline-none focus:ring-4 focus:ring-[#FF6B00]/5">
                                <option value="">Select Barangay</option>
                                <template x-for="barangay in barangays" :key="barangay.code">
                                    <option :value="barangay.code" x-text="barangay.name"></option>
                                </template>
                            </select>
                        </div>

                        <input type="text" name="street_address" placeholder="Street Address / House No." class="w-full bg-white border-black/5 rounded-2xl px-6 py-4 font-bold text-black text-xs outline-none focus:ring-4 focus:ring-[#FF6B00]/5">
                    </div>

                    <!-- Invite Code -->
                    <div class="pt-4 border-t border-black/5">
                        <input id="invite_code" type="text" name="invite_code" placeholder="Admin Invite Code" required class="w-full px-6 py-4 bg-white border-black/5 focus:border-[#FF6B00] focus:ring-4 focus:ring-[#FF6B00]/5 rounded-2xl font-bold text-black shadow-sm outline-none transition-all placeholder-black/20">
                    </div>
                    <x-input-error :messages="$errors->get('invite_code')" />

                    <!-- Password -->
                    <div class="grid grid-cols-2 gap-4">
                        <input id="password" type="password" name="password" placeholder="Password" required class="w-full px-6 py-4 bg-white border-black/5 focus:border-[#FF6B00] focus:ring-4 focus:ring-[#FF6B00]/5 rounded-2xl font-bold text-black shadow-sm outline-none transition-all placeholder-black/20">
                        <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Confirm" required class="w-full px-6 py-4 bg-white border-black/5 focus:border-[#FF6B00] focus:ring-4 focus:ring-[#FF6B00]/5 rounded-2xl font-bold text-black shadow-sm outline-none transition-all placeholder-black/20">
                    </div>

                    <button type="submit" class="w-full bg-[#FF6B00] text-white font-black py-4 rounded-2xl shadow-lg mt-6 uppercase tracking-[0.2em] text-xs">
                        {{ __('Initialize Account') }}
                    </button>
                </form>

                <div class="mt-12">
                    <div class="relative flex items-center justify-center mb-8">
                        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-black/5"></div></div>
                        <span class="relative px-4 bg-[#FEF6F0] text-[10px] font-black text-black/20 uppercase tracking-[0.2em]">Already have access?</span>
                    </div>
                    <a href="{{ route('login') }}" class="w-full flex items-center justify-center py-4 border-2 border-black/5 rounded-2xl text-[10px] font-black text-black uppercase tracking-widest no-underline">
                        {{ __('Sign In') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function addressPicker() {
            return {
                regions: [],
                provinces: [],
                cities: [],
                barangays: [],
                selectedRegion: '',
                selectedProvince: '',
                selectedCity: '',
                selectedBarangay: '',

                async init() {
                    const res = await fetch('https://psgc.gitlab.io/api/regions/');
                    this.regions = await res.json();
                },

                async fetchProvinces() {
                    this.provinces = []; this.cities = []; this.barangays = [];
                    const res = await fetch(`https://psgc.gitlab.io/api/regions/${this.selectedRegion}/provinces/`);
                    this.provinces = await res.json();
                    if(this.provinces.length === 0) { // Some regions like NCR don't have provinces
                        const resCities = await fetch(`https://psgc.gitlab.io/api/regions/${this.selectedRegion}/cities-municipalities/`);
                        this.cities = await resCities.json();
                    }
                },

                async fetchCities() {
                    this.cities = []; this.barangays = [];
                    const res = await fetch(`https://psgc.gitlab.io/api/provinces/${this.selectedProvince}/cities-municipalities/`);
                    this.cities = await res.json();
                },

                async fetchBarangays() {
                    this.barangays = [];
                    const res = await fetch(`https://psgc.gitlab.io/api/cities-municipalities/${this.selectedCity}/barangays/`);
                    this.barangays = await res.json();
                }
            }
        }
    </script>
</body>
</html>
