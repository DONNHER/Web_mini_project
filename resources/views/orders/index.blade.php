@extends('layouts.app')

@section('title', 'My Orders - PageTurner')

@section('header')
    <h1 class="text-3xl font-bold text-white tracking-tight">My Orders</h1>
@endsection

@section('content')
    @if($orders->count() > 0)
        <div class="bg-gray-800 rounded-lg shadow-xl overflow-hidden border border-gray-700">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-900">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Order #</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Total</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @foreach($orders as $order)
                    <tr class="hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 text-sm font-bold text-white">
                            #{{ $order->id }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400 font-medium">
                            {{ $order->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm font-black text-blue-400">
                            ₱{{ number_format($order->total_amount, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 inline-flex text-[10px] font-black uppercase tracking-tighter rounded-full border
                                @if($order->status == 'completed') bg-green-900/40 text-green-400 border-green-500/30
                                @elseif($order->status == 'pending') bg-yellow-900/40 text-yellow-400 border-yellow-500/30
                                @elseif($order->status == 'processing') bg-blue-900/40 text-blue-400 border-blue-400/30
                                @elseif($order->status == 'cancelled') bg-red-900/40 text-red-400 border-red-500/30
                                @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm flex space-x-4">
                            <a href="{{ route('orders.show', $order) }}"
                               class="text-blue-400 hover:text-blue-300 font-bold uppercase text-xs tracking-wider">
                                View
                            </a>
                            <a href="{{ route('orders.invoice', $order) }}"
                               class="text-green-400 hover:text-green-300 font-bold uppercase text-xs tracking-wider">
                                Invoice (PDF)
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-8 pagination-dark">
            {{ $orders->links() }}
        </div>
    @else
        <div class="bg-gray-800 border-l-4 border-blue-500 p-8 text-center rounded-lg shadow-xl">
            <svg class="h-16 w-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <p class="text-xl text-gray-300 mb-6">You haven't placed any orders yet.</p>
            <a href="{{ route('books.index') }}" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-md font-bold uppercase tracking-widest hover:bg-blue-700 transition">
                Start Shopping
            </a>
        </div>
    @endif
@endsection
