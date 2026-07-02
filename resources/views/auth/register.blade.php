<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" class="text-gray-300 font-bold uppercase text-xs tracking-widest" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" class="text-gray-300 font-bold uppercase text-xs tracking-widest" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="text-gray-300 font-bold uppercase text-xs tracking-widest" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-gray-300 font-bold uppercase text-xs tracking-widest" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-col space-y-4 mt-4">
            <div class="flex items-center justify-end">
                <x-primary-button>
                    {{ __('Register') }}
                </x-primary-button>
            </div>

            <div class="text-center border-t border-gray-700 pt-4">
                <p class="text-sm text-gray-400">
                    {{ __('Already have an account?') }}
                    <a href="{{ route('login') }}" class="underline text-blue-400 hover:text-blue-300 font-bold">
                        {{ __('Login here') }}
                    </a>
                </p>
            </div>
        </div>
    </form>
</x-guest-layout>
