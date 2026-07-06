@extends('layouts.app')

@section('title', 'Admin Users Create')

@section('header')
    <div class="flex items-center space-x-6">
        <a href="{{ route('admin.users.index') }}" class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-[#1A1A1A] hover:bg-[#FF6B00] hover:text-white transition-all duration-300 shadow-sm group">
            <svg class="h-5 w-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        </a>
        <div>
            <span class="text-[#FF6B00] font-black uppercase tracking-[0.4em] text-[10px] mb-1 block">Registry Entry</span>
            <h1 class="text-4xl font-black text-[#1A1A1A] uppercase tracking-tighter">Initialize <span class="text-[#FF6B00]">Node</span></h1>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card p-12">
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-10">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div>
                    <x-input-label for="name" :value="__('Full Identity Name')" :required="true" />
                    <x-text-input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full mt-3" required />
                </div>
                <div>
                    <x-input-label for="email" :value="__('System Identifier (Email)')" :required="true" />
                    <x-text-input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full mt-3" required />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div>
                    <x-input-label for="password" :value="__('Security Token')" :required="true" />
                    <x-text-input type="password" name="password" id="password" class="w-full mt-3" required />
                </div>
                <div>
                    <x-input-label for="password_confirmation" :value="__('Verify Token')" :required="true" />
                    <x-text-input type="password" name="password_confirmation" id="password_confirmation" class="w-full mt-3" required />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div>
                    <x-input-label for="role_id" :value="__('Authority Level')" :required="true" />
                    <select name="role_id" id="role_id" class="w-full bg-[#FEF6F0] border-none rounded-2xl py-4 font-bold focus:ring-4 focus:ring-[#FF6B00]/5 text-sm mt-3" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="status" :value="__('Matrix Status')" :required="true" />
                    <select name="status" id="status" class="w-full bg-[#FEF6F0] border-none rounded-2xl py-4 font-bold focus:ring-4 focus:ring-[#FF6B00]/5 text-sm mt-3" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end pt-10 border-t border-[#1A1A1A]/5">
                <button type="submit" class="btn-primary px-12 py-4 shadow-xl shadow-[#FF6B00]/20">
                    Deploy Identity
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
