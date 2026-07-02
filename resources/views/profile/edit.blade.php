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

            <!-- Two Factor Authentication -->
            <div class="p-4 sm:p-8 bg-gray-800 shadow-xl border border-gray-700 sm:rounded-lg">
                <div class="max-w-xl text-white">
                    <h2 class="text-lg font-medium text-white">
                        {{ __('Two-Factor Authentication') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-400">
                        {{ __('Add additional security to your account using two-factor authentication.') }}
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('profile.two-factor') }}" class="inline-flex items-center px-4 py-2 bg-blue-400 border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:bg-blue-500 active:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-[0_0_15px_rgba(96,165,250,0.3)]">
                            {{ __('Manage 2FA') }}
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
