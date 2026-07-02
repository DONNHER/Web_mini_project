@extends('layouts.app')

@section('title', 'My Dashboard - PageTurner')

@section('header')
    <h1 class="text-3xl font-bold text-white tracking-tight">Welcome back, <span class="text-blue-500">{{ $user->name }}</span>!</h1>
@endsection

@section('content')
    <!-- Account Status Alerts -->
    <div class="mb-8 space-y-4">
        @if(!$user->hasVerifiedEmail())
            <div class="bg-yellow-900/20 border-l-4 border-yellow-500 text-yellow-200 p-4 rounded-lg shadow-md flex items-center">
                <svg class="h-6 w-6 mr-3 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>
                    <strong class="font-bold">Email not verified!</strong>
                    Please verify your email address to access all features.
                    <a href="{{ route('verification.notice') }}" class="underline ml-2 hover:text-white transition">Verify now</a>
                </span>
            </div>
        @endif

        @if(!$user->hasTwoFactorEnabled())
            <div class="bg-blue-900/20 border-l-4 border-blue-500 text-blue-200 p-4 rounded-lg shadow-md flex items-center">
                <svg class="h-6 w-6 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <span>
                    <strong class="font-bold">Enhance your security!</strong>
                    Enable two-factor authentication to protect your account.
                    <a href="{{ route('profile.two-factor') }}" class="underline ml-2 hover:text-white transition">Enable 2FA</a>
                </span>
            </div>
        @endif
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Orders Card -->
        <div class="bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Total Orders</p>
                    <p class="text-3xl font-black text-white mt-1">{{ $totalOrders }}</p>
                </div>
                <div class="bg-blue-900/30 p-3 rounded-lg border border-blue-500/20">
                    <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Orders Card -->
        <div class="bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Pending</p>
                    <p class="text-3xl font-black text-yellow-500 mt-1">{{ $pendingOrders }}</p>
                </div>
                <div class="bg-yellow-900/30 p-3 rounded-lg border border-yellow-500/20">
                    <svg class="h-8 w-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Completed Orders Card -->
        <div class="bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Completed</p>
                    <p class="text-3xl font-black text-green-500 mt-1">{{ $completedOrders }}</p>
                </div>
                <div class="bg-green-900/30 p-3 rounded-lg border border-green-500/20">
                    <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Reviews Card -->
        <div class="bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">My Reviews</p>
                    <p class="text-3xl font-black text-white mt-1">{{ $recentReviews->count() }}</p>
                </div>
                <div class="bg-blue-900/30 p-3 rounded-lg border border-blue-500/20">
                    <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Orders -->
        <div class="lg:col-span-2 bg-gray-800 rounded-xl shadow-xl p-8 border border-gray-700">
            <h2 class="text-xl font-black text-white mb-6 uppercase tracking-tight border-b border-gray-700 pb-2">Recent Orders</h2>

            @if($recentOrders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead>
                            <tr class="text-gray-500 uppercase text-[10px] font-black tracking-widest">
                                <th class="text-left py-3">Order #</th>
                                <th class="text-left py-3">Date</th>
                                <th class="text-left py-3">Total</th>
                                <th class="text-left py-3">Status</th>
                                <th class="text-left py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @foreach($recentOrders as $order)
                            <tr class="hover:bg-gray-700/30 transition-colors">
                                <td class="py-4 text-sm font-bold text-white">#{{ $order->id }}</td>
                                <td class="py-4 text-sm text-gray-400 font-medium">{{ $order->created_at->format('M d, Y') }}</td>
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
                                <td class="py-4 text-sm">
                                    <a href="{{ route('orders.show', $order) }}" class="text-blue-400 hover:text-blue-300 font-bold uppercase text-xs tracking-widest transition">
                                        View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <a href="{{ route('orders.index') }}" class="mt-6 inline-block text-blue-400 hover:text-blue-300 text-xs font-bold uppercase tracking-widest transition">
                    View All Orders →
                </a>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-500 italic mb-6">No orders yet.</p>
                    <a href="{{ route('books.index') }}" class="bg-blue-600 text-white px-8 py-3 rounded-md font-black uppercase tracking-widest hover:bg-blue-700 transition shadow-lg shadow-blue-500/20">
                        Start Shopping
                    </a>
                </div>
            @endif
        </div>

        <!-- Recently Purchased Books -->
        <div class="bg-gray-800 rounded-xl shadow-xl p-8 border border-gray-700">
            <h2 class="text-xl font-black text-white mb-6 uppercase tracking-tight border-b border-gray-700 pb-2">Recent Purchases</h2>

            @if($recentBooks->count() > 0)
                <div class="space-y-6">
                    @foreach($recentBooks as $book)
                        <div class="flex items-center space-x-4 border-b border-gray-700/50 last:border-0 pb-4 last:pb-0 group">
                            <div class="flex-shrink-0 h-14 w-12 bg-gray-900 rounded border border-gray-700 overflow-hidden">
                                @if($book->cover_image)
                                    <img src="{{ asset('storage/' . $book->cover_image) }}"
                                         alt="{{ $book->title }}"
                                         class="h-full w-full object-cover group-hover:scale-110 transition duration-300">
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-white truncate text-sm group-hover:text-blue-400 transition">{{ $book->title }}</h3>
                                <p class="text-xs text-gray-500 italic">by {{ $book->author }}</p>
                            </div>
                            <a href="{{ route('books.show', $book) }}" class="text-blue-500 hover:text-blue-400 transition-transform hover:scale-110">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8 italic text-sm">No purchases yet.</p>
            @endif
        </div>

        <!-- Recent Reviews -->
        <div class="lg:col-span-2 bg-gray-800 rounded-xl shadow-xl p-8 border border-gray-700">
            <h2 class="text-xl font-black text-white mb-6 uppercase tracking-tight border-b border-gray-700 pb-2">My Recent Reviews</h2>

            @if($recentReviews->count() > 0)
                <div class="space-y-6">
                    @foreach($recentReviews as $review)
                        <div class="border-b border-gray-700/50 last:border-0 pb-6 last:pb-0">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-bold text-white mb-2">{{ $review->book->title }}</p>
                                    <div class="flex items-center mb-3">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="h-4 w-4 {{ $i <= $review->rating ? 'text-yellow-500' : 'text-gray-700' }}"
                                                 fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @endfor
                                    </div>
                                    @if($review->comment)
                                        <p class="text-sm text-gray-400 bg-gray-900/50 p-4 rounded-lg border-l-2 border-blue-500 italic">"{{ Str::limit($review->comment, 100) }}"</p>
                                    @endif
                                </div>
                                <span class="text-[10px] text-gray-500 font-bold uppercase tracking-tight">{{ $review->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8 italic text-sm">No reviews yet.</p>
            @endif
        </div>

        <!-- Data Portability Section -->
        <div class="bg-gray-800 rounded-xl shadow-xl p-8 border border-gray-700">
            <h2 class="text-xl font-black text-white mb-6 uppercase tracking-tight border-b border-gray-700 pb-2">Data Portability</h2>
            <p class="text-gray-400 text-sm mb-4">Export your personal data and history in compliance with GDPR.</p>

            <div class="space-y-3">
                <a href="{{ route('user.export.personal') }}"
                   class="flex items-center justify-between p-3 bg-gray-900/50 rounded-lg border border-gray-700 hover:border-blue-500 transition-all group">
                    <span class="text-gray-300 text-xs font-bold uppercase tracking-widest">Personal Data (JSON)</span>
                    <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                </a>

                <a href="{{ route('user.export.reading') }}"
                   class="flex items-center justify-between p-3 bg-gray-900/50 rounded-lg border border-gray-700 hover:border-blue-500 transition-all group">
                    <span class="text-gray-300 text-xs font-bold uppercase tracking-widest">Reading History (JSON)</span>
                    <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                </a>

                <div class="pt-2">
                    <p class="text-gray-500 text-[10px] font-black uppercase tracking-widest mb-2">Order History Export</p>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('user.export.orders.excel') }}"
                           class="flex items-center justify-center p-2 bg-green-900/20 rounded border border-green-500/30 hover:bg-green-900/40 transition">
                            <span class="text-green-400 text-[10px] font-black uppercase">Excel</span>
                        </a>
                        <a href="{{ route('user.export.orders.pdf') }}"
                           class="flex items-center justify-center p-2 bg-red-900/20 rounded border border-red-500/30 hover:bg-red-900/40 transition">
                            <span class="text-red-400 text-[10px] font-black uppercase">PDF</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="lg:col-span-3 bg-gray-800 rounded-xl shadow-xl p-8 border border-gray-700">
            <h2 class="text-xl font-black text-white mb-6 uppercase tracking-tight border-b border-gray-700 pb-2">Quick Actions</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('books.index') }}"
                   class="flex items-center p-4 bg-gray-900/50 rounded-xl border border-gray-700 hover:border-blue-500 transition-all group">
                    <div class="bg-blue-900/30 p-2 rounded-lg mr-4 group-hover:bg-blue-600 transition">
                        <svg class="h-6 w-6 text-blue-500 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <span class="text-white font-bold text-sm uppercase tracking-widest">Browse Books</span>
                </a>

                <a href="{{ route('orders.index') }}"
                   class="flex items-center p-4 bg-gray-900/50 rounded-xl border border-gray-700 hover:border-blue-500 transition-all group">
                    <div class="bg-blue-900/30 p-2 rounded-lg mr-4 group-hover:bg-blue-600 transition">
                        <svg class="h-6 w-6 text-blue-500 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <span class="text-white font-bold text-sm uppercase tracking-widest">Order History</span>
                </a>

                <a href="{{ route('profile.edit') }}"
                   class="flex items-center p-4 bg-gray-900/50 rounded-xl border border-gray-700 hover:border-blue-500 transition-all group">
                    <div class="bg-blue-900/30 p-2 rounded-lg mr-4 group-hover:bg-blue-600 transition">
                        <svg class="h-6 w-6 text-blue-500 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <span class="text-white font-bold text-sm uppercase tracking-widest">Profile Settings</span>
                </a>

                <a href="{{ route('profile.two-factor') }}"
                   class="flex items-center p-4 bg-gray-900/50 rounded-xl border border-gray-700 hover:border-blue-500 transition-all group">
                    <div class="bg-blue-900/30 p-2 rounded-lg mr-4 group-hover:bg-blue-600 transition">
                        <svg class="h-6 w-6 text-blue-500 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <span class="text-white font-bold text-sm uppercase tracking-widest">Security Settings</span>
                </a>
            </div>
        </div>
    </div>
@endsection
