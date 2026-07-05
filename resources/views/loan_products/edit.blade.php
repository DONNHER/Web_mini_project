@extends('layouts.app')

@section('title', 'Refactor Asset - LendingSystem')

@section('header')
    <h1 class="text-3xl font-black text-black uppercase tracking-tighter">Refactor Asset Data</h1>
@endsection

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-black text-brand rounded-3xl p-10 border border-black shadow-xl">
            <form action="{{ route('admin.loan-products.update', $loanProduct) }}" method="POST" class="space-y-8">
                @csrf
                @method('PATCH')

                <div>
                    <label for="name" class="block text-[10px] font-black uppercase tracking-widest mb-3 opacity-40">Asset Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $loanProduct->name) }}"
                           class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3 focus:ring-white"
                           required>
                    @error('name') <p class="text-white text-[10px] mt-2 font-black uppercase">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="category_id" class="block text-[10px] font-black uppercase tracking-widest mb-3 opacity-40">Classification *</label>
                    <select name="category_id" id="category_id"
                            class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3 focus:ring-white"
                            required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $loanProduct->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-8">
                    <div>
                        <label for="interest_rate" class="block text-[10px] font-black uppercase tracking-widest mb-3 opacity-40">Interest Rate (%) *</label>
                        <input type="number" step="0.01" name="interest_rate" id="interest_rate" value="{{ old('interest_rate', $loanProduct->interest_rate) }}"
                               class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3 focus:ring-white"
                               required>
                    </div>

                    <div>
                        <label for="duration_months" class="block text-[10px] font-black uppercase tracking-widest mb-3 opacity-40">Term (Months) *</label>
                        <input type="number" name="duration_months" id="duration_months" value="{{ old('duration_months', $loanProduct->duration_months) }}"
                               class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3 focus:ring-white"
                               required>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-[10px] font-black uppercase tracking-widest mb-3 opacity-40">Asset Summary</label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full bg-brand/10 border-brand/20 rounded-xl text-white font-bold p-3 focus:ring-white">{{ old('description', $loanProduct->description) }}</textarea>
                </div>

                <div class="flex justify-end pt-8 border-t border-brand/10">
                    <button type="submit" class="bg-white text-black px-12 py-4 rounded-2xl font-black uppercase tracking-widest text-xs hover:opacity-80 transition shadow-xl">
                        Commit Refactor
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
