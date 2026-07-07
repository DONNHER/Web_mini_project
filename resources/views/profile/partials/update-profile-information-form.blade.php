<section x-data="addressPicker({
    region: '{{ old('region', $user->region) }}',
    province: '{{ old('province', $user->province) }}',
    city: '{{ old('city', $user->city) }}',
    barangay: '{{ old('barangay', $user->barangay) }}'
})">
    <header>
        <h2 class="text-2xl font-black uppercase tracking-tighter text-[#1A1A1A]">
            {{ __('Profile Information') }}
        </h2>
        <p class="mt-4 text-[10px] font-black uppercase tracking-[0.2em] text-[#1A1A1A]/40 leading-relaxed">
            {{ __("Update your node's primary identifier, contact, and geographical location.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-8 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-6">
                <div>
                    <x-input-label for="avatar" :value="__('System Avatar')" />
                    <div class="mt-2 flex items-center space-x-4">
                        <div class="h-16 w-16 rounded-2xl bg-[#FFEDD5] overflow-hidden border-2 border-[#FF6B00]/10 shrink-0">
                            @if($user->avatar)
                                <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                            @else
                                <div class="h-full w-full flex items-center justify-center text-[#FF6B00] font-black text-2xl">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <input id="avatar" name="avatar" type="file" class="block w-full text-[10px] text-[#1A1A1A]/40 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-[#FF6B00] file:text-white hover:file:opacity-80 cursor-pointer" />
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
                </div>

                <div>
                    <x-input-label for="name" :value="__('Full Name')" />
                    <x-text-input id="name" name="name" type="text" class="mt-2 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Registry Email')" />
                    <x-text-input id="email" name="email" type="email" class="mt-2 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="mt-4 p-4 bg-red-500/5 rounded-2xl border border-red-500/10">
                            <p class="text-[10px] font-black uppercase text-red-600 tracking-widest">
                                {{ __('Email identity unverified.') }}
                                <button form="send-verification" class="ml-2 underline hover:text-red-800 focus:outline-none">
                                    {{ __('Re-send Link') }}
                                </button>
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Address Section --}}
            <div class="space-y-4 pt-4 md:pt-0 md:pl-8 border-t md:border-t-0 md:border-l border-black/5">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-black/40 mb-4">Geographical Node</h3>

                <div class="grid grid-cols-1 gap-4">
                    <select name="region" x-model="selectedRegion" @change="fetchProvinces()" class="bg-[#FEF6F0] border-none rounded-xl px-6 py-4 font-bold text-black text-xs outline-none focus:ring-4 focus:ring-[#FF6B00]/5">
                        <option value="">Select Region</option>
                        <template x-for="region in regions" :key="region.code">
                            <option :value="region.code" x-text="region.name" :selected="region.code === selectedRegion"></option>
                        </template>
                    </select>

                    <select name="province" x-model="selectedProvince" @change="fetchCities()" class="bg-[#FEF6F0] border-none rounded-xl px-6 py-4 font-bold text-black text-xs outline-none focus:ring-4 focus:ring-[#FF6B00]/5">
                        <option value="">Select Province</option>
                        <template x-for="province in provinces" :key="province.code">
                            <option :value="province.code" x-text="province.name" :selected="province.code === selectedProvince"></option>
                        </template>
                    </select>

                    <select name="city" x-model="selectedCity" @change="fetchBarangays()" class="bg-[#FEF6F0] border-none rounded-xl px-6 py-4 font-bold text-black text-xs outline-none focus:ring-4 focus:ring-[#FF6B00]/5">
                        <option value="">Select City</option>
                        <template x-for="city in cities" :key="city.code">
                            <option :value="city.code" x-text="city.name" :selected="city.code === selectedCity"></option>
                        </template>
                    </select>

                    <select name="barangay" x-model="selectedBarangay" class="bg-[#FEF6F0] border-none rounded-xl px-6 py-4 font-bold text-black text-xs outline-none focus:ring-4 focus:ring-[#FF6B00]/5">
                        <option value="">Select Barangay</option>
                        <template x-for="barangay in barangays" :key="barangay.code">
                            <option :value="barangay.code" x-text="barangay.name" :selected="barangay.code === selectedBarangay"></option>
                        </template>
                    </select>

                    <x-text-input name="street_address" :value="old('street_address', $user->street_address)" placeholder="Street Address / House No." class="w-full" />
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4 pt-8 border-t border-black/5">
            <button type="submit" class="btn-primary px-12">Commit Registry Update</button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-[10px] font-black uppercase tracking-widest text-green-600">{{ __('Changes Merged.') }}</p>
            @endif
        </div>
    </form>

    <script>
        function addressPicker(defaults = {}) {
            return {
                regions: [], provinces: [], cities: [], barangays: [],
                selectedRegion: defaults.region || '',
                selectedProvince: defaults.province || '',
                selectedCity: defaults.city || '',
                selectedBarangay: defaults.barangay || '',

                async init() {
                    const res = await fetch('https://psgc.gitlab.io/api/regions/');
                    this.regions = await res.json();
                    if(this.selectedRegion) await this.fetchProvinces(true);
                },

                async fetchProvinces(isInit = false) {
                    if(!isInit) { this.provinces = []; this.cities = []; this.barangays = []; }
                    const res = await fetch(`https://psgc.gitlab.io/api/regions/${this.selectedRegion}/provinces/`);
                    this.provinces = await res.json();

                    if(this.provinces.length === 0) {
                        const resCities = await fetch(`https://psgc.gitlab.io/api/regions/${this.selectedRegion}/cities-municipalities/`);
                        this.cities = await resCities.json();
                    }

                    if(isInit && this.selectedProvince) await this.fetchCities(true);
                },

                async fetchCities(isInit = false) {
                    if(!isInit) { this.cities = []; this.barangays = []; }
                    const res = await fetch(`https://psgc.gitlab.io/api/provinces/${this.selectedProvince}/cities-municipalities/`);
                    this.cities = await res.json();

                    if(isInit && this.selectedCity) await this.fetchBarangays();
                },

                async fetchBarangays() {
                    const res = await fetch(`https://psgc.gitlab.io/api/cities-municipalities/${this.selectedCity}/barangays/`);
                    this.barangays = await res.json();
                }
            }
        }
    </script>
</section>
