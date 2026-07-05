@extends('layouts.app')

@section('title', 'Configure Identity - Admin')

@section('header')
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.users.index') }}" class="text-black hover:opacity-60 transition">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        </a>
        <h1 class="text-3xl font-black text-black uppercase tracking-tighter">Configure Identity</h1>
    </div>
@endsection

@section('content')
<div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-12">
    <!-- Main Config -->
    <div class="lg:col-span-2 space-y-8">
        <div class="bg-black text-brand rounded-3xl p-10 border border-black shadow-xl">
            <h2 class="text-xs font-black uppercase tracking-[0.3em] mb-8 opacity-40">Core Registry Data</h2>

            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-8">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <x-input-label for="name" :value="__('Full Name')" />
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3 focus:ring-white mt-2">
                    </div>
                    <div>
                        <x-input-label for="email" :value="__('Identifier')" />
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3 focus:ring-white mt-2">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <x-input-label for="role_id" :value="__('Authority Role')" />
                        <select name="role_id" id="role_id" class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3 focus:ring-white mt-2">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>{{ $role->display_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="status" :value="__('Account Status')" />
                        <select name="status" id="status" class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3 focus:ring-white mt-2">
                            <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ $user->status == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end pt-8 border-t border-brand/10">
                    <button type="submit" class="bg-white text-black px-12 py-4 rounded-2xl font-black uppercase tracking-widest text-xs hover:opacity-80 transition shadow-xl">
                        Commit Refactor
                    </button>
                </div>
            </form>
        </div>

        <!-- Login History (Requirement 14.4) -->
        <div class="bg-black/5 rounded-3xl p-10 border border-black/5 shadow-sm">
            <h2 class="text-xs font-black text-black uppercase tracking-[0.3em] mb-8 opacity-40">Access Intelligence History</h2>
            <div class="space-y-4">
                @foreach($loginHistory as $audit)
                    <div class="flex items-center justify-between text-xs border-b border-black/5 pb-4">
                        <div>
                            <span class="font-black uppercase {{ $audit->event == 'login_failed' ? 'text-red-600' : 'text-black' }}">{{ str_replace('_', ' ', $audit->event) }}</span>
                            <p class="text-[10px] opacity-40 font-mono mt-1">{{ $audit->ip_address }} | {{ Str::limit($audit->user_agent, 50) }}</p>
                        </div>
                        <span class="font-bold opacity-40">{{ $audit->created_at->diffForHumans() }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Side Actions -->
    <div class="space-y-8">
        <!-- Analytics (Requirement 14.7) -->
        <div class="bg-black/5 rounded-3xl p-8 border border-black/5 shadow-sm">
            <h3 class="font-black uppercase text-sm tracking-tight mb-6 opacity-40">Identity Analytics</h3>
            <div class="space-y-6">
                <div>
                    <p class="text-[8px] font-black uppercase tracking-widest opacity-40 mb-1">Last Active Pulse</p>
                    <p class="text-xs font-bold">{{ $lastActive ? $lastActive->diffForHumans() : 'Never' }}</p>
                </div>
                <div>
                    <p class="text-[8px] font-black uppercase tracking-widest opacity-40 mb-1">Primary Asset Usage</p>
                    <p class="text-xs font-bold">{{ $mostUsedFeature ? class_basename($mostUsedFeature->auditable_type) : 'None' }}</p>
                </div>
            </div>
        </div>

        <!-- Force Logout (Requirement 14.5) -->
        <div class="bg-black text-brand rounded-3xl p-8 border border-black shadow-xl">
            <h3 class="font-black uppercase text-sm tracking-tight mb-4">Session Termination</h3>
            <p class="text-xs font-bold opacity-60 mb-6">Purge all active sessions across all devices for this identity.</p>
            <form action="{{ route('admin.users.force-logout', $user) }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-brand/10 text-white border border-brand/20 py-3 rounded-xl font-black uppercase tracking-widest text-[10px] hover:bg-red-600 hover:text-white transition">
                    Execute Force Logout
                </button>
            </form>
        </div>

        <!-- Critical Directives -->
        <div class="bg-red-600/5 rounded-3xl p-8 border border-red-600/10 shadow-sm">
            <h3 class="font-black uppercase text-sm text-red-600 tracking-tight mb-4">Critical Directive</h3>
            <p class="text-xs font-bold text-red-600 opacity-60 mb-6">Permanently remove this identity and all associated meta-data from the registry.</p>
            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('CRITICAL: Purge identity?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full bg-red-600 text-white py-3 rounded-xl font-black uppercase tracking-widest text-[10px] hover:opacity-80 transition">
                    Purge Identity
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
