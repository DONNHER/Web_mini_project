@extends('layouts.app')

@section('title', 'Checkout - PageTurner')

@section('header')
    <h1 class="text-3xl font-bold text-white tracking-tight">Checkout</h1>
@endsection

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="md:col-span-2">
            <div class="bg-gray-800 rounded-lg shadow-xl p-8 border border-gray-700">
                <h2 class="text-xl font-black text-blue-400 uppercase tracking-widest mb-6 border-b border-gray-700 pb-2">Order Summary</h2>

                <div class="space-y-6">
                    @foreach($cartItems as $item)
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
                        <p class="text-lg font-bold text-gray-400 uppercase tracking-widest">Total Amount</p>
                        <p class="text-3xl font-black text-blue-500">₱{{ number_format($total, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="md:col-span-1">
            <div class="bg-gray-800 rounded-lg shadow-xl p-8 border border-gray-700">
                <h2 class="text-xl font-black text-blue-400 uppercase tracking-widest mb-6 border-b border-gray-700 pb-2">Shipping & Payment</h2>

                <form action="{{ route('orders.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="shipping_address" class="block text-gray-400 font-bold text-xs uppercase tracking-widest mb-2">
                            Shipping Address *
                        </label>
                        <textarea name="shipping_address"
                                  id="shipping_address"
                                  rows="4"
                                  class="w-full bg-gray-700 border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-500"
                                  placeholder="Enter your full delivery address..."
                                  required>{{ old('shipping_address') }}</textarea>
                    </div>

                    <div>
                        <label for="payment_method" class="block text-gray-400 font-bold text-xs uppercase tracking-widest mb-2">
                            Payment Method *
                        </label>
                        <select name="payment_method"
                                id="payment_method"
                                class="w-full bg-gray-700 border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-white font-bold"
                                required>
                            <option value="">Select payment method</option>
                            <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                            <option value="paypal" {{ old('payment_method') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                            <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="gcash" {{ old('payment_method') == 'gcash' ? 'selected' : '' }}>GCash</option>
                            <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                            <option value="cod" {{ old('payment_method') == 'cod' ? 'selected' : '' }}>Cash on Delivery</option>
                        </select>
                        @error('payment_method')
                            <p class="text-red-400 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                            class="w-full bg-blue-600 text-white font-black py-4 rounded-lg hover:bg-blue-700 transition uppercase tracking-widest shadow-lg shadow-blue-500/20">
                        Complete Order
                    </button>

                    <a href="{{ route('orders.cart') }}"
                       class="w-full block text-center mt-4 text-gray-500 hover:text-gray-300 font-bold uppercase text-xs tracking-tighter transition">
                        ← Back to Cart
                    </a>
                </form>
            </div>
        </div>
    </div>
@endsection
