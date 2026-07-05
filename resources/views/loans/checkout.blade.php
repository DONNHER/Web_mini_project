@extends('layouts.app')

@section('title', 'Confirm Loan - PageTurner')

@section('header')
    <h1 class="text-3xl font-bold text-white tracking-tight">Confirm Loan</h1>
@endsection

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="md:col-span-2">
            <div class="bg-gray-800 rounded-lg shadow-xl p-8 border border-gray-700">
                <h2 class="text-xl font-black text-blue-400 uppercase tracking-widest mb-6 border-b border-gray-700 pb-2">Borrowing Summary</h2>

                <div class="space-y-6">
                    @foreach($borrowItems as $item)
                        <div class="flex justify-between items-center border-b border-gray-700/50 pb-4">
                            <div class="flex items-center">
                                <div class="h-12 w-10 bg-gray-900 rounded flex-shrink-0 mr-4 border border-gray-700">
                                    @if($item['book']->cover_image)
                                        <img src="{{ asset('storage/' . $item['book']->cover_image) }}" class="h-full w-full object-cover rounded">
                                    @endif
                                </div>
                                <div>
                                    <p class="font-bold text-white">{{ $item['book']->title }}</p>
                                    <p class="text-xs text-gray-500 font-bold uppercase tracking-tighter">Qty: <span class="text-blue-400">{{ $item['quantity'] }}</span></p>
                                </div>
                            </div>
                            <p class="font-black text-white">₱{{ number_format($item['subtotal'], 2) }}</p>
                        </div>
                    @endforeach

                    <div class="flex justify-between items-center pt-4">
                        <p class="text-lg font-bold text-gray-400 uppercase tracking-widest">Total Replacement Value</p>
                        <p class="text-3xl font-black text-blue-500">₱{{ number_format($total, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="md:col-span-1">
            <div class="bg-gray-800 rounded-lg shadow-xl p-8 border border-gray-700">
                <h2 class="text-xl font-black text-blue-400 uppercase tracking-widest mb-6 border-b border-gray-700 pb-2">Lending Details</h2>

                <form action="{{ route('loans.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="shipping_address" class="block text-gray-400 font-bold text-xs uppercase tracking-widest mb-2">
                            Delivery Address *
                        </label>
                        <textarea name="shipping_address"
                                  id="shipping_address"
                                  rows="3"
                                  class="w-full bg-gray-900 border-gray-700 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-500"
                                  placeholder="Enter your full delivery address..."
                                  required>{{ old('shipping_address') }}</textarea>
                    </div>

                    <div>
                        <label for="comaker_id" class="block text-gray-400 font-bold text-xs uppercase tracking-widest mb-2">
                            Co-maker (Optional)
                        </label>
                        <select name="comaker_id"
                                id="comaker_id"
                                class="w-full bg-gray-900 border-gray-700 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-white font-bold">
                            <option value="">Select a co-maker (optional)</option>
                            @foreach($comakers as $comaker)
                                <option value="{{ $comaker->id }}" {{ old('comaker_id') == $comaker->id ? 'selected' : '' }}>
                                    {{ $comaker->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-gray-500 mt-1 uppercase tracking-tighter">Only users marked as "Eligible Comakers" appear here.</p>
                        @error('comaker_id')
                            <p class="text-red-400 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                            class="w-full bg-blue-600 text-white font-black py-4 rounded-lg hover:bg-blue-700 transition uppercase tracking-widest shadow-lg shadow-blue-500/20">
                        Confirm Borrowing
                    </button>

                    <a href="{{ route('loans.cart') }}"
                       class="w-full block text-center mt-4 text-gray-500 hover:text-gray-300 font-bold uppercase text-xs tracking-tighter transition">
                        ← Back to Borrow List
                    </a>
                </form>
            </div>
        </div>
    </div>
@endsection
