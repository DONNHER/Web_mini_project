@extends('layouts.app')

@section('title', 'Admin Users Edit')

@section('header')
    <div class="flex items-center space-x-6">
        <a href="{{ route('admin.users.index') }}" class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-[#1A1A1A] hover:bg-[#FF6B00] hover:text-white transition-all duration-300 shadow-sm group">
            <svg class="h-5 w-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        </a>
        <div>
            <h1 class="text-4xl font-black text-[#1A1A1A] uppercase tracking-tighter">Users Edit</h1>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-12">
    <!-- Main Config -->
    <div class="lg:col-span-2 space-y-10">
        <div class="card p-12">
            <h2 class="text-[10px] font-black uppercase tracking-[0.3em] mb-10 text-[#1A1A1A]/30">Core Registry Data</h2>

            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-10">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div>
                        <x-input-label for="name" :value="__('Full Identity Name')" />
                        <x-text-input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="w-full mt-3" />
                    </div>
                    <div>
                        <x-input-label for="email" :value="__('System Identifier')" />
                        <x-text-input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="w-full mt-3" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div>
                        <x-input-label for="role_id" :value="__('Authority Role')" />
                        <select name="role_id" id="role_id" class="w-full bg-[#FEF6F0] border-none rounded-2xl px-8 py-4 font-bold focus:ring-4 focus:ring-[#FF6B00]/5 text-sm mt-3">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>{{ $role->display_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="status" :value="__('Integrity Status')" />
                        <select name="status" id="status" class="w-full bg-[#FEF6F0] border-none rounded-2xl px-8 py-4 font-bold focus:ring-4 focus:ring-[#FF6B00]/5 text-sm mt-3">
                            <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ $user->status == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end pt-10 border-t border-[#1A1A1A]/5">
                    <button type="submit" class="btn-primary px-12 py-4">
                        Commit Refactor
                    </button>
                </div>
            </form>
        </div>

        <!-- Login History (Requirement 14.4) -->
        <div class="card p-12 overflow-hidden">
            <h2 class="text-[10px] font-black text-[#1A1A1A] uppercase tracking-[0.3em] mb-10 opacity-30">Access Intelligence Stream</h2>
            <div class="space-y-6">
                @foreach($loginHistory as $audit)
                    <div class="flex items-center justify-between text-[10px] border-b border-[#1A1A1A]/5 pb-6">
                        <div class="flex items-center space-x-4">
                            <span class="h-2 w-2 rounded-full {{ $audit->event == 'login_failed' ? 'bg-red-500 shadow-lg shadow-red-500/40' : 'bg-green-500 shadow-lg shadow-green-500/40' }}"></span>
                            <div>
                                <span class="font-black uppercase tracking-widest {{ $audit->event == 'login_failed' ? 'text-red-600' : 'text-[#1A1A1A]' }}">{{ str_replace('_', ' ', $audit->event) }}</span>
                                <p class="text-[9px] opacity-40 font-black tracking-widest mt-1 uppercase">{{ $audit->ip_address }} | {{ Str::limit($audit->user_agent, 40) }}</p>
                            </div>
                        </div>
                        <span class="font-black opacity-20 uppercase tracking-widest">{{ $audit->created_at->diffForHumans() }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Side Actions -->
    <div class="space-y-10">
        <!-- Analytics -->
        <div class="card p-8">
            <h3 class="font-black uppercase text-[10px] tracking-[0.2em] mb-8 text-[#1A1A1A]/40">Identity Analytics</h3>
            <div class="space-y-8">
                <div>
                    <p class="text-[8px] font-black uppercase tracking-[0.3em] text-[#FF6B00] mb-2">Last Active Pulse</p>
                    <p class="text-sm font-black text-[#1A1A1A] uppercase tracking-tighter">{{ $lastActive ? $lastActive->diffForHumans() : 'OFFLINE' }}</p>
                </div>
                <div>
                    <p class="text-[8px] font-black uppercase tracking-[0.3em] text-[#FF6B00] mb-2">Primary Module Usage</p>
                    <p class="text-sm font-black text-[#1A1A1A] uppercase tracking-tighter">{{ $mostUsedFeature ? class_basename($mostUsedFeature->auditable_type) : 'IDLE' }}</p>
                </div>
            </div>
        </div>

        <!-- Force Logout -->
        <div class="card p-8 bg-[#1A1A1A] border-none">
            <h3 class="font-black uppercase text-[10px] tracking-[0.2em] text-white mb-4">Session Purge</h3>
            <p class="text-[10px] font-bold text-white/40 mb-8 leading-relaxed">Immediately terminate all active session tokens associated with this identity matrix.</p>
            <form action="{{ route('admin.users.force-logout', $user) }}" method="POST">
                @csrf
                <button type="submit" class="w-full btn-primary bg-white/5 border border-white/10 text-white hover:bg-red-600 hover:border-red-600 transition-all duration-300">
                    Force Terminal Exit
                </button>
            </form>
        </div>

        <!-- Critical Directives -->
        <div class="card p-8 border-red-500/20 bg-red-500/5 shadow-none">
            <h3 class="font-black uppercase text-[10px] text-red-600 tracking-[0.2em] mb-4">Destructive Directive</h3>
            <p class="text-[10px] font-bold text-red-600/60 mb-8 leading-relaxed">Permanently purge this identity and all associated meta-data from the core registry.</p>
            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('CRITICAL: Purge identity?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full btn-primary bg-red-600 text-white hover:opacity-80 transition-opacity">
                    Purge Registry Node
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
