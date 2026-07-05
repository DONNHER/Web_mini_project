<x-guest-layout>
    <h1 class="text-3xl font-black text-black uppercase tracking-tighter mb-8 text-center">System <br><span class="text-white">Registry</span></h1>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name')" :required="true" />
            <x-text-input id="name" class="block mt-1 w-full bg-white/20 border-black/10 text-black font-bold" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Access Identifier (Email)')" :required="true" />
            <x-text-input id="email" class="block mt-1 w-full bg-white/20 border-black/10 text-black font-bold" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Security Token (Password)')" :required="true" />

            <x-text-input id="password" class="block mt-1 w-full bg-white/20 border-black/10 text-black font-bold"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Token')" :required="true" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full bg-white/20 border-black/10 text-black font-bold"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-col space-y-6 pt-4">
            <button type="submit" class="w-full bg-black text-brand py-4 rounded-2xl font-black uppercase tracking-widest text-xs hover:opacity-90 transition shadow-xl flex items-center justify-center space-x-2">
                <span>{{ __('Initialize Account') }}</span>
                <svg class="loading-spinner hidden animate-spin h-4 w-4 text-brand" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>

            <div class="text-center border-t border-black/5 pt-6">
                <p class="text-[10px] font-black uppercase tracking-widest text-black/40">
                    {{ __('Already have an access key?') }}
                    <a href="{{ route('login') }}" class="text-black font-black hover:opacity-60 transition ml-2 no-underline border-b-2 border-black">
                        {{ __('Sign In') }}
                    </a>
                </p>
            </div>
        </div>
    </form>
</x-guest-layout>
