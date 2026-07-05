<x-guest-layout>
    <h1 class="text-3xl font-black text-black uppercase tracking-tighter mb-8 text-center">System <br><span class="text-white">Authentication</span></h1>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Access Identifier (Email)')" :required="true" />
            <x-text-input id="email" class="block mt-1 w-full bg-white/20 border-black/10 text-black font-bold" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Security Token (Password)')" :required="true" />

            <x-text-input id="password" class="block mt-1 w-full bg-white/20 border-black/10 text-black font-bold"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-black/20 bg-white/20 text-black focus:ring-black" name="remember">
                <span class="ms-2 text-[10px] font-black uppercase text-black/40">{{ __('Maintain Session') }}</span>
            </label>
        </div>

        <div class="flex flex-col space-y-6 pt-4">
            <button type="submit" class="w-full bg-black text-brand py-4 rounded-2xl font-black uppercase tracking-widest text-xs hover:opacity-90 transition shadow-xl flex items-center justify-center space-x-2">
                <span>{{ __('Log in') }}</span>
                <svg class="loading-spinner hidden animate-spin h-4 w-4 text-brand" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>

            @if (Route::has('password.request'))
                <div class="text-center">
                    <a class="text-[10px] font-black uppercase tracking-widest text-black/40 hover:text-black no-underline border-b border-black/10 pb-1" href="{{ route('password.request') }}">
                        {{ __('Recover Security Token') }}
                    </a>
                </div>
            @endif

            <div class="text-center border-t border-black/5 pt-6">
                <p class="text-[10px] font-black uppercase tracking-widest text-black/40">
                    {{ __("No access yet?") }}
                    <a href="{{ route('register') }}" class="text-black font-black hover:opacity-60 transition ml-2 no-underline border-b-2 border-black">
                        {{ __('Initialize Registry') }}
                    </a>
                </p>
            </div>
        </div>
    </form>
</x-guest-layout>
