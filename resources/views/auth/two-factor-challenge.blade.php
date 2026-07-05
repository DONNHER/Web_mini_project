@extends('layouts.app')

@section('title', 'Two-Factor Authentication - LendingSystem')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-black">
    <div class="max-w-md w-full bg-gray-800 rounded-2xl shadow-2xl p-8 border border-gray-700">
        <div class="text-center">
            <!-- Shield Icon -->
            <svg class="mx-auto h-16 w-16 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>

            <h2 class="mt-6 text-2xl font-black text-white uppercase tracking-tight">Identity Verification</h2>

            <div class="mt-4 text-gray-400 text-sm">
                <p>Please enter the 6-digit OTP sent to your email or the code from your authenticator app.</p>
            </div>

            @if (session('error'))
                <div class="mt-4 bg-red-900/30 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg text-xs font-bold">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('two-factor.verify') }}" class="mt-8">
                @csrf

                <div class="mb-6">
                    <label for="code" class="block text-left text-gray-500 text-[10px] font-black uppercase tracking-widest mb-2">
                        Verification Code
                    </label>
                    <input type="text"
                           name="code"
                           id="code"
                           inputmode="numeric"
                           pattern="[0-9]*"
                           class="w-full bg-gray-900 border-gray-700 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 text-white font-black text-center text-2xl tracking-[1em]"
                           placeholder="000000"
                           required
                           autofocus>
                </div>

                <button type="submit"
                        class="w-full bg-blue-600 text-white font-black py-4 rounded-xl hover:bg-blue-700 transition uppercase tracking-widest shadow-lg shadow-blue-500/20">
                    Verify Identity
                </button>
            </form>

            <div class="mt-8 pt-8 border-t border-gray-700 flex flex-col space-y-4">
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-tighter">
                    Having trouble? Check your spam folder for the OTP.
                </p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-red-500 hover:text-red-400 text-xs font-black uppercase tracking-widest">
                        Cancel and Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
