<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-white">
            {{ __('Browser Sessions') }}
        </h2>

        <p class="mt-1 text-sm text-gray-400">
            {{ __('Manage and log out your active sessions on other browsers and devices.') }}
        </p>
    </header>

    <div class="mt-5 space-y-6">
        <p class="text-sm text-gray-400">
            {{ __('If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below; however, this list may not be exhaustive. If you feel your account has been compromised, you should also update your password.') }}
        </p>

        <div class="flex items-center mt-5">
            <x-primary-button
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'confirm-logout-other-sessions')"
            >
                {{ __('Log Out Other Browser Sessions') }}
            </x-primary-button>
        </div>

        <x-modal name="confirm-logout-other-sessions" :show="$errors->userDeletion->isNotEmpty()" focusable>
            <form method="post" action="{{ route('profile.logout-other-sessions') }}" class="p-6 bg-gray-800">
                @csrf

                <h2 class="text-lg font-medium text-white">
                    {{ __('Are you sure you want to log out of other browser sessions?') }}
                </h2>

                <p class="mt-1 text-sm text-gray-400">
                    {{ __('Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices.') }}
                </p>

                <div class="mt-6">
                    <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                    <x-text-input
                        id="password"
                        name="password"
                        type="password"
                        class="mt-1 block w-3/4 bg-gray-900 border-gray-700 text-white"
                        placeholder="{{ __('Password') }}"
                    />

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-primary-button class="ms-3">
                        {{ __('Log Out Other Browser Sessions') }}
                    </x-primary-button>
                </div>
            </form>
        </x-modal>
    </div>
</section>
