@extends('layouts.app')

@section('title', 'Two-Factor Authentication - PageTurner')

@section('header')
    <h1 class="text-3xl font-bold text-gray-900">Security Settings</h1>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Two-Factor Authentication</h2>
        
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if(isset($twoFactor) && $twoFactor->enabled)
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                <div class="flex items-center">
                    <svg class="h-6 w-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-green-800 font-medium">Two-factor authentication is enabled</span>
                </div>
            </div>

            <form method="POST" action="{{ route('two-factor.disable') }}" class="mt-4">
                @csrf
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 font-medium mb-2">
                        Enter your password to disable 2FA
                    </label>
                    <input type="password" 
                           name="password" 
                           id="password"
                           class="w-full md:w-1/2 border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                           required>
                </div>
                <button type="submit" 
                        class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition">
                    Disable Two-Factor Authentication
                </button>
            </form>
        @else
            <p class="text-gray-600 mb-4">
                Two-factor authentication adds an extra layer of security to your account. Once enabled, you'll need to provide a verification code from your authenticator app when logging in.
            </p>

            <a href="{{ route('two-factor.enable') }}" 
               class="inline-block bg-brown-600 text-white px-4 py-2 rounded-md hover:bg-brown-700 transition">
                Enable Two-Factor Authentication
            </a>
        @endif
    </div>

    <!-- Security Tips -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-800 mb-2">Security Tips</h3>
        <ul class="list-disc list-inside text-blue-700 space-y-1">
            <li>Use an authenticator app like Google Authenticator or Authy</li>
            <li>Keep your recovery codes in a safe place</li>
            <li>Never share your verification codes with anyone</li>
            <li>Enable 2FA to protect your account from unauthorized access</li>
        </ul>
    </div>
</div>
@endsection