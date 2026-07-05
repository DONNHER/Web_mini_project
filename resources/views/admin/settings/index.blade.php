@extends('layouts.app')

@section('title', 'System Control - Admin')

@section('header')
    <h1 class="text-3xl font-black text-black uppercase tracking-tighter">System Parameters</h1>
@endsection

@section('content')
<div class="max-w-6xl mx-auto" x-data="{ tab: 'branding' }">
    <div class="flex flex-col md:flex-row gap-12">
        <!-- Sidebar Navigation -->
        <div class="md:w-1/4 space-y-2">
            <button @click="tab = 'branding'" :class="tab === 'branding' ? 'bg-black text-brand' : 'bg-black/5 text-black/40'" class="w-full text-left p-4 rounded-2xl font-black uppercase tracking-widest text-[10px] transition">Branding & Core</button>
            <button @click="tab = 'security'" :class="tab === 'security' ? 'bg-black text-brand' : 'bg-black/5 text-black/40'" class="w-full text-left p-4 rounded-2xl font-black uppercase tracking-widest text-[10px] transition">Security Policy</button>
            <button @click="tab = 'backup'" :class="tab === 'backup' ? 'bg-black text-brand' : 'bg-black/5 text-black/40'" class="w-full text-left p-4 rounded-2xl font-black uppercase tracking-widest text-[10px] transition">Backup Engine</button>
            <button @click="tab = 'email'" :class="tab === 'email' ? 'bg-black text-brand' : 'bg-black/5 text-black/40'" class="w-full text-left p-4 rounded-2xl font-black uppercase tracking-widest text-[10px] transition">Email Gateway</button>
            <button @click="tab = 'api'" :class="tab === 'api' ? 'bg-black text-brand' : 'bg-black/5 text-black/40'" class="w-full text-left p-4 rounded-2xl font-black uppercase tracking-widest text-[10px] transition">API Interface</button>
            <button @click="tab = 'maintenance'" :class="tab === 'maintenance' ? 'bg-black text-brand' : 'bg-black/5 text-black/40'" class="w-full text-left p-4 rounded-2xl font-black uppercase tracking-widest text-[10px] transition text-red-600">Maintenance Mode</button>
        </div>

        <!-- Main Content -->
        <div class="md:w-3/4">
            <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-8">
                @csrf
                @method('PATCH')

                <!-- Branding -->
                <div x-show="tab === 'branding'" class="bg-black text-brand rounded-3xl p-10 border border-black shadow-xl space-y-8">
                    <h2 class="text-xl font-black uppercase tracking-widest">Branding Configuration</h2>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest opacity-40 mb-2">Platform Identity</label>
                            <input type="text" name="branding[site_name]" value="{{ $branding['site_name'] }}" class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest opacity-40 mb-2">Primary Hex Color</label>
                            <input type="color" name="branding[theme_color]" value="{{ $branding['theme_color'] }}" class="w-full h-12 bg-brand/10 border-brand/20 rounded-xl p-1">
                        </div>
                    </div>
                </div>

                <!-- Security -->
                <div x-show="tab === 'security'" class="bg-black text-brand rounded-3xl p-10 border border-black shadow-xl space-y-8">
                    <h2 class="text-xl font-black uppercase tracking-widest">Security & Session</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest opacity-40 mb-2">Session Lifetime (Min)</label>
                            <input type="number" name="security[session_timeout]" value="{{ $security['session_timeout'] }}" class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest opacity-40 mb-2">Min Password Entropy</label>
                            <input type="number" name="security[min_password_length]" value="{{ $security['min_password_length'] }}" class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3">
                        </div>
                    </div>
                </div>

                <!-- Backup -->
                <div x-show="tab === 'backup'" class="bg-black text-brand rounded-3xl p-10 border border-black shadow-xl space-y-8">
                    <h2 class="text-xl font-black uppercase tracking-widest">Backup Engine</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest opacity-40 mb-2">Database Frequency</label>
                            <select name="backup[database]" class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3">
                                <option value="daily" {{ $backup['database'] == 'daily' ? 'selected' : '' }}>Daily</option>
                                <option value="weekly" {{ $backup['database'] == 'weekly' ? 'selected' : '' }}>Weekly</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest opacity-40 mb-2">Retention Cycle (Days)</label>
                            <input type="number" name="backup[retention_days]" value="{{ $backup['retention_days'] }}" class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3">
                        </div>
                    </div>
                </div>

                <!-- Email -->
                <div x-show="tab === 'email'" class="bg-black text-brand rounded-3xl p-10 border border-black shadow-xl space-y-8">
                    <h2 class="text-xl font-black uppercase tracking-widest">Email Gateway (SMTP)</h2>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest opacity-40 mb-2">SMTP Relay Host</label>
                            <input type="text" name="email[smtp_host]" value="{{ $email['smtp_host'] }}" class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest opacity-40 mb-2">System Sender Address</label>
                            <input type="email" name="email[from_address]" value="{{ $email['from_address'] }}" class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3">
                        </div>
                    </div>
                </div>

                <!-- API -->
                <div x-show="tab === 'api'" class="bg-black text-brand rounded-3xl p-10 border border-black shadow-xl space-y-8">
                    <h2 class="text-xl font-black uppercase tracking-widest">API Interface Limits</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest opacity-40 mb-2">Standard Burst (req/min)</label>
                            <input type="number" name="api[rate_limit_standard]" value="{{ $api['rate_limit_standard'] }}" class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest opacity-40 mb-2">Premium Burst (req/min)</label>
                            <input type="number" name="api[rate_limit_premium]" value="{{ $api['rate_limit_premium'] }}" class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3">
                        </div>
                    </div>
                </div>

                <!-- Maintenance -->
                <div x-show="tab === 'maintenance'" class="bg-black text-brand rounded-3xl p-10 border border-black shadow-xl space-y-8">
                    <h2 class="text-xl font-black uppercase tracking-widest text-red-600">Critical Maintenance</h2>
                    <div class="space-y-6">
                        <div class="flex items-center">
                            <input type="hidden" name="maintenance[enabled]" value="0">
                            <input type="checkbox" name="maintenance[enabled]" value="1" {{ $maintenance['enabled'] ? 'checked' : '' }} class="rounded border-brand/20 bg-brand/10 text-brand focus:ring-brand h-6 w-6">
                            <label class="ml-4 font-black uppercase text-xs tracking-widest">Activate Global Lockdown</label>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest opacity-40 mb-2">Public Broadcast Message</label>
                            <textarea name="maintenance[message]" rows="3" class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3">{{ $maintenance['message'] }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-8 border-t border-black/5">
                    <button type="submit" class="bg-black text-brand px-12 py-4 rounded-2xl font-black uppercase tracking-widest text-xs hover:opacity-80 transition shadow-xl">
                        Commit Global Refactor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
