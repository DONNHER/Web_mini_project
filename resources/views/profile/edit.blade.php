@extends('layouts.app')

@section('title', 'Profile')

@section('header')
    <div>
        <span class="text-[#FF6B00] font-black uppercase tracking-[0.4em] text-[10px] mb-2 block">Personal Registry</span>
        <h1 class="text-5xl font-black text-[#1A1A1A] uppercase tracking-tighter leading-none">Security <span class="text-[#FF6B00]">Profile</span></h1>
    </div>
@endsection

@section('content')
    <div class="py-12 space-y-10">
        <div class="max-w-7xl mx-auto space-y-10">
            <!-- Profile Information -->
            <div class="card p-10">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Update Password -->
            <div class="card p-10">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Browser Sessions -->
            <div class="card p-10">
                <div class="max-w-xl">
                    @include('profile.partials.logout-other-browser-sessions-form')
                </div>
            </div>

            <!-- Two Factor Authentication -->
            <div class="card p-10">
                <div class="max-w-xl">
                    <h2 class="text-2xl font-black uppercase tracking-tighter text-[#1A1A1A]">
                        {{ __('Security & Notifications') }}
                    </h2>
                    <p class="mt-4 text-[10px] font-black uppercase tracking-[0.2em] text-[#1A1A1A]/40 leading-relaxed">
                        {{ __('Manage your two-factor authentication and alert preferences for maximum account integrity.') }}
                    </p>
                    <div class="mt-8 flex space-x-4">
                        <a href="{{ route('profile.two-factor') }}" class="btn-primary no-underline">
                            {{ __('Manage 2FA') }}
                        </a>
                        <a href="{{ route('profile.notifications') }}" class="btn-secondary no-underline">
                            {{ __('Alert Matrix') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Delete User -->
            <div class="card p-10 border-red-500/20">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
@endsection
