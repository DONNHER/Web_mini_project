@extends('layouts.app')

@section('title', 'Profile Settings - PageTurner')

@section('header')
    <h2 class="font-bold text-xl text-white leading-tight">
        {{ __('Profile Settings') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Profile Information -->
            <div class="p-4 sm:p-8 bg-gray-800 shadow-xl border border-gray-700 sm:rounded-lg">
                <div class="max-w-xl text-white">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Update Password -->
            <div class="p-4 sm:p-8 bg-gray-800 shadow-xl border border-gray-700 sm:rounded-lg">
                <div class="max-w-xl text-white">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Browser Sessions -->
            <div class="p-4 sm:p-8 bg-gray-800 shadow-xl border border-gray-700 sm:rounded-lg">
                <div class="max-w-xl text-white">
                    @include('profile.partials.logout-other-browser-sessions-form')
                </div>
            </div>

            <!-- Two Factor Authentication -->
            <div class="p-4 sm:p-8 bg-gray-800 shadow-xl border border-gray-700 sm:rounded-lg">
                <div class="max-w-xl text-white">
                    <h2 class="text-lg font-medium text-white">
                        {{ __('Security & Notifications') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-400">
                        {{ __('Manage your two-factor authentication and alert preferences.') }}
                    </p>
                    <div class="mt-6 flex space-x-4">
                        <a href="{{ route('profile.two-factor') }}" class="inline-flex items-center px-4 py-2 bg-blue-400 border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:bg-blue-500 active:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-[0_0_15px_rgba(96,165,250,0.3)]">
                            {{ __('Manage 2FA') }}
                        </a>
                        <a href="{{ route('profile.notifications') }}" class="inline-flex items-center px-4 py-2 bg-gray-700 border border-gray-600 rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-gray-600 transition ease-in-out duration-150">
                            {{ __('Notification Settings') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Delete User -->
            <div class="p-4 sm:p-8 bg-gray-800 shadow-xl border border-gray-700 sm:rounded-lg">
                <div class="max-w-xl text-white">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
@endsection
