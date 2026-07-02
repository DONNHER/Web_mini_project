@extends('layouts.app')

@section('title', 'Admin Dashboard - PageTurner')

@section('header')
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-white tracking-tight">Admin Dashboard</h1>
        <div class="flex space-x-3">
            <form action="{{ route('admin.dashboard.backup') }}" method="POST">
                @csrf
                <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-2 rounded-lg hover:from-blue-500 hover:to-indigo-500 transition-all duration-300 font-bold text-xs uppercase tracking-widest shadow-lg shadow-blue-500/25 flex items-center">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Run Manual Backup
                </button>
            </form>
        </div>
    </div>
@endsection

@section('content')
    @if(session('success'))
        <div class="mb-8 bg-green-900/30 border border-green-500/30 text-green-400 p-4 rounded-lg flex items-center justify-between">
            <div class="flex items-center">
                <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm font-bold uppercase tracking-wider">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-8 bg-red-900/30 border border-red-500/30 text-red-400 p-4 rounded-lg flex items-center">
            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm font-bold uppercase tracking-wider">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Users Card -->
        <div class="bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white text-xs font-bold uppercase tracking-widest">Total Users</p>
                    <p class="text-3xl font-black text-white mt-1">{{ $totalUsers }}</p>
                </div>
                <div class="bg-blue-900/30 p-3 rounded-lg border border-blue-400/20">
                    <svg class="h-8 w-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Books Card -->
        <div class="bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white text-xs font-bold uppercase tracking-widest">Total Books</p>
                    <p class="text-3xl font-black text-white mt-1">{{ $totalBooks }}</p>
                </div>
                <div class="bg-blue-900/30 p-3 rounded-lg border border-blue-400/20">
                    <svg class="h-8 w-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Categories Card -->
        <div class="bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white text-xs font-bold uppercase tracking-widest">Categories</p>
                    <p class="text-3xl font-black text-white mt-1">{{ $totalCategories }}</p>
                </div>
                <div class="bg-blue-900/30 p-3 rounded-lg border border-blue-400/20">
                    <svg class="h-8 w-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-5-5A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white text-xs font-bold uppercase tracking-widest">Total Orders</p>
                    <p class="text-3xl font-black text-white mt-1">{{ $totalOrders }}</p>
                </div>
                <div class="bg-blue-900/30 p-3 rounded-lg border border-blue-400/20">
                    <svg class="h-8 w-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Orders -->
        <div class="bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-700">
            <h2 class="text-xl font-black text-white mb-6 uppercase tracking-tight border-b border-gray-700 pb-2">Recent Orders</h2>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead>
                        <tr class="text-white uppercase text-[10px] font-black tracking-widest">
                            <th class="text-left py-3">Order #</th>
                            <th class="text-left py-3">Customer</th>
                            <th class="text-left py-3">Total</th>
                            <th class="text-left py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($recentOrders as $order)
                        <tr class="hover:bg-gray-700/30 transition-colors">
                            <td class="py-4 text-sm font-bold text-white">#{{ $order->id }}</td>
                            <td class="py-4 text-sm text-white">{{ $order->user->name }}</td>
                            <td class="py-4 text-sm font-black text-blue-400">₱{{ number_format($order->total_amount, 2) }}</td>
                            <td class="py-4 text-sm">
                                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-tighter rounded-full border
                                    @if($order->status == 'completed') bg-green-900/40 text-green-400 border-green-500/30
                                    @elseif($order->status == 'pending') bg-yellow-900/40 text-yellow-400 border-yellow-500/30
                                    @elseif($order->status == 'processing') bg-blue-900/40 text-blue-400 border-blue-400/30
                                    @elseif($order->status == 'cancelled') bg-red-900/40 text-red-400 border-red-500/30
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <a href="{{ route('admin.orders.index') }}" class="mt-6 inline-block text-blue-400 hover:text-blue-300 text-xs font-bold uppercase tracking-widest transition">
                View All Orders →
            </a>
        </div>

        <!-- Order Status Summary -->
        <div class="bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-700">
            <h2 class="text-xl font-black text-white mb-6 uppercase tracking-tight border-b border-gray-700 pb-2">Order Status Summary</h2>

            <div class="space-y-6">
                @foreach($orderStatusSummary as $status)
                <div>
                    <div class="flex justify-between text-xs font-bold mb-2 uppercase tracking-widest">
                        <span class="text-white">{{ ucfirst($status->status) }}</span>
                        <span class="text-white">{{ $status->total }}</span>
                    </div>
                    <div class="w-full bg-gray-900 rounded-full h-2 border border-gray-700">
                        <div class="h-2 rounded-full shadow-[0_0_8px_rgba(96,165,250,0.3)]
                            @if($status->status == 'completed') bg-green-500
                            @elseif($status->status == 'pending') bg-yellow-500
                            @elseif($status->status == 'processing') bg-blue-400
                            @elseif($status->status == 'cancelled') bg-red-500
                            @endif"
                            style="width: {{ ($status->total / ($totalOrders ?: 1)) * 100 }}%">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Reviews -->
        <div class="bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-700">
            <h2 class="text-xl font-black text-white mb-6 uppercase tracking-tight border-b border-gray-700 pb-2">Recent Reviews</h2>

            <div class="space-y-4">
                @foreach($recentReviews as $review)
                <div class="border-b border-gray-700 last:border-0 pb-4 last:pb-0">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-bold text-white">{{ $review->book->title }}</p>
                            <p class="text-xs text-white font-bold italic">by {{ $review->user->name }}</p>
                            <div class="flex items-center mt-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="h-3 w-3 {{ $i <= $review->rating ? 'text-yellow-500' : 'text-gray-700' }}"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endfor
                            </div>
                        </div>
                        <span class="text-[10px] text-gray-500 font-bold">{{ $review->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Top Selling Books -->
        <div class="bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-700">
            <h2 class="text-xl font-black text-white mb-6 uppercase tracking-tight border-b border-gray-700 pb-2">Top Selling Books</h2>

            <div class="space-y-4">
                @foreach($topBooks as $book)
                <div class="flex items-center justify-between border-b border-gray-700 last:border-0 pb-4 last:pb-0">
                    <div>
                        <p class="font-bold text-white">{{ $book->title }}</p>
                        <p class="text-xs text-white italic">by {{ $book->author }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-black text-blue-400 uppercase tracking-tighter">{{ $book->order_items_count }} sold</p>
                        <p class="text-[10px] text-gray-500 font-bold">₱{{ number_format($book->price, 2) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
