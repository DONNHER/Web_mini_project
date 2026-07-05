@extends('layouts.app')

@section('title', 'Register Asset - LendingSystem')

@section('header')
    <h1 class="text-3xl font-black text-black uppercase tracking-tighter">Initialize New Asset</h1>
@endsection

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-black text-brand rounded-3xl p-10 border border-black shadow-xl">
            <form action="{{ route('admin.loan-products.store') }}" method="POST" class="space-y-8 dirty-check">
                @csrf

                <div>
                    <x-input-label for="name" :value="__('Asset Name')" :required="true" />
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                           class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3 focus:ring-white mt-2"
                           required>
                    <x-input-error :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="category_id" :value="__('Classification')" :required="true" />
                    <select name="category_id" id="category_id"
                            class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3 focus:ring-white mt-2"
                            required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('category_id')" />
                </div>

                <div class="grid grid-cols-2 gap-8">
                    <div>
                        <x-input-label for="interest_rate" :value="__('Interest Rate (%)')" :required="true" />
                        <input type="number" step="0.01" name="interest_rate" id="interest_rate" value="{{ old('interest_rate') }}"
                               class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3 focus:ring-white mt-2"
                               required>
                        <x-input-error :messages="$errors->get('interest_rate')" />
                    </div>

                    <div>
                        <x-input-label for="duration_months" :value="__('Term (Months)')" :required="true" />
                        <input type="number" name="duration_months" id="duration_months" value="{{ old('duration_months') }}"
                               class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3 focus:ring-white mt-2"
                               required>
                        <x-input-error :messages="$errors->get('duration_months')" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-8">
                    <div>
                        <x-input-label for="min_amount" :value="__('Min Principal')" :required="true" />
                        <input type="number" name="min_amount" id="min_amount" value="{{ old('min_amount') }}"
                               class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3 focus:ring-white mt-2"
                               required>
                        <x-input-error :messages="$errors->get('min_amount')" />
                    </div>

                    <div>
                        <x-input-label for="max_amount" :value="__('Max Principal')" :required="true" />
                        <input type="number" name="max_amount" id="max_amount" value="{{ old('max_amount') }}"
                               class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3 focus:ring-white mt-2"
                               required>
                        <x-input-error :messages="$errors->get('max_amount')" />
                    </div>
                </div>

                <div>
                    <x-input-label for="description" :value="__('Asset Summary')" />
                    <textarea name="description" id="description" rows="4"
                              class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3 focus:ring-white mt-2">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" />
                </div>

                <div class="flex justify-end pt-8 border-t border-brand/10">
                    <button type="submit" class="bg-white text-black px-12 py-4 rounded-2xl font-black uppercase tracking-widest text-xs hover:opacity-80 transition shadow-xl flex items-center justify-center space-x-2">
                        <span>Deploy Asset</span>
                        <svg class="loading-spinner hidden animate-spin h-4 w-4 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
