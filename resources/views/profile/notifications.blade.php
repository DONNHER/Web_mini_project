@extends('layouts.app')

@section('title', 'Notification Preferences - LendingSystem')

@section('header')
    <h2 class="font-bold text-xl text-white leading-tight">
        {{ __('Notification Preferences') }}
    </h2>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="p-8 bg-gray-800 shadow-xl border border-gray-700 rounded-xl">
        <form method="post" action="{{ route('profile.notifications.update') }}" class="space-y-6">
            @csrf
            @method('PATCH')

            <div>
                <h3 class="text-lg font-medium text-white">Delivery Channels</h3>
                <p class="mt-1 text-sm text-gray-400">Choose how you want to receive system alerts.</p>

                <div class="mt-6 space-y-4">
                    <div class="flex items-center">
                        <input type="hidden" name="email" value="0">
                        <input id="email" name="email" type="checkbox" value="1" {{ $preferences['email'] ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-900 border-gray-700 rounded focus:ring-blue-500">
                        <label for="email" class="ml-3 text-sm font-medium text-gray-300">Email Notifications</label>
                    </div>

                    <div class="flex items-center">
                        <input type="hidden" name="sms" value="0">
                        <input id="sms" name="sms" type="checkbox" value="1" {{ $preferences['sms'] ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-900 border-gray-700 rounded focus:ring-blue-500">
                        <label for="sms" class="ml-3 text-sm font-medium text-gray-300">SMS Alerts (Critical Only)</label>
                    </div>

                    <div class="flex items-center">
                        <input type="hidden" name="in_app" value="0">
                        <input id="in_app" name="in_app" type="checkbox" value="1" {{ $preferences['in_app'] ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-900 border-gray-700 rounded focus:ring-blue-500">
                        <label for="in_app" class="ml-3 text-sm font-medium text-gray-300">In-App Notifications (Bell Icon)</label>
                    </div>
                </div>
            </div>

            <hr class="border-gray-700">

            <div>
                <h3 class="text-lg font-medium text-white">Alert Types</h3>
                <p class="mt-1 text-sm text-gray-400">Which events should trigger a notification?</p>

                <div class="mt-6 space-y-4">
                    <div class="flex items-center">
                        <input type="hidden" name="reminders" value="0">
                        <input id="reminders" name="reminders" type="checkbox" value="1" {{ $preferences['reminders'] ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-900 border-gray-700 rounded focus:ring-blue-500">
                        <label for="reminders" class="ml-3 text-sm font-medium text-gray-300">Payment Reminders & Deadlines</label>
                    </div>

                    <div class="flex items-center">
                        <input type="hidden" name="security" value="0">
                        <input id="security" name="security" type="checkbox" value="1" {{ $preferences['security'] ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-900 border-gray-700 rounded focus:ring-blue-500">
                        <label for="security" class="ml-3 text-sm font-medium text-gray-300">Security Alerts (Logins, Risk Flags)</label>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <x-primary-button>{{ __('Save Preferences') }}</x-primary-button>
            </div>
        </form>
    </div>
</div>
@endsection
