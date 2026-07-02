@extends('layouts.app')

@section('title', 'Two-Factor Authentication - PageTurner')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-gray-100">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
        <div class="text-center">
            <!-- Shield Icon -->
            <svg class="mx-auto h-16 w-16 text-brown-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            
            <h2 class="mt-6 text-3xl font-bold text-gray-900">Two-Factor Authentication</h2>
            
            <div class="mt-4 text-gray-600">
                <p>Please enter the verification code from your authenticator app or use a recovery code.</p>
            </div>

            @if (session('error'))
                <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('two-factor.verify') }}" class="mt-8">
                @csrf
                
                <div class="mb-4">
                    <label for="code" class="block text-left text-gray-700 font-medium mb-2">
                        Authentication Code
                    </label>
                    <input type="text" 
                           name="code" 
                           id="code"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-brown-500 focus:border-brown-500"
                           placeholder="000000"
                           required>
                </div>

                <button type="submit" 
                        class="w-full bg-brown-600 text-white px-4 py-2 rounded-md hover:bg-brown-700 transition">
                    Verify
                </button>
            </form>

            <div class="mt-6 text-sm text-gray-500">
                <p>Lost your device? Use a recovery code or contact support.</p>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                    Cancel and Logout
                </button>
            </form>
        </div>
    </div>
</div>
@endsection