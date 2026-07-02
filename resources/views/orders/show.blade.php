@extends('layouts.app')

@section('title', 'Order Details - PageTurner')

@section('header')
    <h1 class="text-3xl font-bold text-white tracking-tight">Order #{{ $order->id }}</h1>
@endsection

@section('content')
    <div class="bg-gray-800 rounded-lg shadow-xl p-8 mb-8 border border-gray-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-3">
                <h2 class="text-xl font-bold text-blue-400 uppercase tracking-tight border-b border-gray-700 pb-2">Order Information</h2>
                <p class="text-white font-medium">Date: <span class="text-white">{{ $order->created_at->format('F j, Y, g:i a') }}</span></p>
                <div class="flex items-center space-x-2">
                    <span class="text-white font-medium">Status:</span>
                    <span class="px-3 py-1 inline-flex text-[10px] font-black uppercase tracking-tighter rounded-full border
                        @if($order->status == 'completed') bg-green-900/40 text-green-400 border-green-500/30
                        @elseif($order->status == 'pending') bg-yellow-900/40 text-yellow-400 border-yellow-500/30
                        @elseif($order->status == 'processing') bg-blue-900/40 text-blue-400 border-blue-400/30
                        @elseif($order->status == 'cancelled') bg-red-900/40 text-red-400 border-red-500/30
                        @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                <p class="text-white font-medium">Payment Method:
                    <span class="text-white bg-gray-900 px-2 py-1 rounded border border-gray-700 text-sm">
                        @switch($order->payment_method)
                            @case('credit_card') Credit Card @break
                            @case('paypal') PayPal @break
                            @case('bank_transfer') Bank Transfer @break
                            @case('gcash') GCash @break
                            @case('card') Card @break
                            @case('cod') Cash on Delivery @break
                            @default {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
                        @endswitch
                    </span>
                </p>
                <p class="text-white font-medium">Shipping Address: <span class="text-white block mt-1 bg-gray-900/50 p-3 rounded italic">"{{ $order->shipping_address }}"</span></p>
                <p class="text-white font-medium">Total Amount: <span class="text-2xl font-black text-blue-400">₱{{ number_format($order->total_amount, 2) }}</span></p>
            </div>

            <div class="space-y-3">
                <h2 class="text-xl font-bold text-blue-400 uppercase tracking-tight border-b border-gray-700 pb-2">Customer Information</h2>
                <div class="bg-gray-900/30 p-4 rounded-lg border border-gray-700">
                    <p class="text-white font-bold mb-1">{{ $order->user->name }}</p>
                    <p class="text-blue-400 text-sm">{{ $order->user->email }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-gray-800 rounded-lg shadow-xl overflow-hidden border border-gray-700">
        <h2 class="text-lg font-black text-white p-6 bg-gray-900 border-b border-gray-700 uppercase tracking-widest">Order Items</h2>

        <table class="min-w-full divide-y divide-gray-700">
            <thead class="bg-gray-900/50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-widest">Book</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-widest">Price</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-widest">Quantity</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-widest">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @foreach($order->orderItems as $item)
                <tr class="hover:bg-gray-700/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div>
                                <div class="text-sm font-bold text-white">
                                    {{ $item->book->title }}
                                </div>
                                <div class="text-xs text-gray-400 italic">
                                    by {{ $item->book->author }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-white font-medium">
                        ₱{{ number_format($item->unit_price, 2) }}
                    </td>
                    <td class="px-6 py-4 text-sm text-white font-black">
                        {{ $item->quantity }}
                    </td>
                    <td class="px-6 py-4 text-sm font-black text-blue-400">
                        ₱{{ number_format($item->subtotal, 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-900">
                <tr>
                    <td colspan="3" class="px-6 py-6 text-right font-bold text-white uppercase tracking-widest text-xs">
                        Final Total:
                    </td>
                    <td class="px-6 py-6 text-2xl font-black text-blue-400">
                        ₱{{ number_format($order->total_amount, 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="mt-8">
        <a href="{{ url()->previous() == route('admin.orders.index') ? route('admin.orders.index') : route('orders.index') }}" class="inline-flex items-center text-blue-400 hover:text-blue-300 font-bold uppercase tracking-widest text-xs transition">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Orders
        </a>
    </div>
@endsection
