@extends('layouts.app')

@section('title', 'Verify Email - PageTurner')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-gray-100">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
        <div class="text-center">
            <!-- Email Icon -->
            <svg class="mx-auto h-16 w-16 text-brown-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            
            <h2 class="mt-6 text-3xl font-bold text-gray-900">Verify Your Email</h2>
            
            <div class="mt-4 text-gray-600">
                <p>Thanks for signing up! To get started, click the link sent to your email.</p>
            </div>

            @if (session('message'))
                <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('message') }}
                </div>
            @endif

            <div class="mt-8 space-y-4">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" 
                            class="w-full bg-brown-600 text-white px-4 py-2 rounded-md hover:bg-brown-700 transition">
                        Resend Verification Email
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                            class="w-full bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition">
                        Log Out
                    </button>
                </form>
            </div>

            <p class="mt-4 text-sm text-gray-500">
                If you didn't receive the email, check your spam folder or resend it.
            </p>
        </div>
    </div>
</div>
@endsection