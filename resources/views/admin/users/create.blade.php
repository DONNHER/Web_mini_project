@extends('layouts.app')

@section('title', 'Register Identity - Admin')

@section('header')
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.users.index') }}" class="text-black hover:opacity-60 transition">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        </a>
        <h1 class="text-3xl font-black text-black uppercase tracking-tighter">Initialize Registry</h1>
    </div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-black text-brand rounded-3xl p-10 border border-black shadow-xl">
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-8">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <x-input-label for="name" :value="__('Full Name')" :required="true" />
                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3 focus:ring-white mt-2" required>
                </div>
                <div>
                    <x-input-label for="email" :value="__('Identifier (Email)')" :required="true" />
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3 focus:ring-white mt-2" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <x-input-label for="password" :value="__('Security Token')" :required="true" />
                    <input type="password" name="password" id="password" class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3 focus:ring-white mt-2" required>
                </div>
                <div>
                    <x-input-label for="password_confirmation" :value="__('Confirm Token')" :required="true" />
                    <input type="password" name="password_confirmation" id="password_confirmation" class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3 focus:ring-white mt-2" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <x-input-label for="role_id" :value="__('Authority Role')" :required="true" />
                    <select name="role_id" id="role_id" class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3 focus:ring-white mt-2" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="status" :value="__('Initial Status')" :required="true" />
                    <select name="status" id="status" class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3 focus:ring-white mt-2" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end pt-8 border-t border-brand/10">
                <button type="submit" class="bg-white text-black px-12 py-4 rounded-2xl font-black uppercase tracking-widest text-xs hover:opacity-80 transition shadow-xl">
                    Deploy Identity
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
