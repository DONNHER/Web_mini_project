@extends('layouts.app')

@section('title', 'Borrow List - PageTurner')

@section('header')
    <h1 class="text-3xl font-bold text-white">Borrow List</h1>
@endsection

@section('content')
    @if(count($borrowItems) > 0)
        <div class="bg-gray-800 rounded-lg shadow-xl overflow-hidden border border-gray-700">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-900">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Book</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Value</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Quantity</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Subtotal</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @foreach($borrowItems as $item)
                    <tr class="hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12 bg-gray-900 rounded border border-gray-700">
                                    @if($item['book']->cover_image)
                                        <img src="{{ asset('storage/' . $item['book']->cover_image) }}"
                                             alt="{{ $item['book']->title }}"
                                             class="h-12 w-12 object-cover rounded">
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-white">
                                        {{ $item['book']->title }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        by {{ $item['book']->author }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-300">
                            ₱{{ number_format($item['book']->price, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            <form action="{{ route('loans.cart.update') }}" method="POST" class="flex items-center space-x-2">
                                @csrf
                                <input type="hidden" name="book_id" value="{{ $item['book']->id }}">
                                <input type="number"
                                       name="quantity"
                                       value="{{ $item['quantity'] }}"
                                       min="0"
                                       max="{{ $item['book']->stock_quantity }}"
                                       class="w-20 bg-gray-700 border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-white text-center font-bold">
                                <button type="submit" class="px-3 py-1 bg-blue-600/20 border border-blue-400/30 text-blue-400 rounded-md hover:bg-blue-600 hover:text-white transition-all duration-300 text-[10px] font-bold uppercase tracking-widest">
                                    Update
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-blue-400">
                            ₱{{ number_format($item['subtotal'], 2) }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('loans.cart.remove', $item['book']->id) }}"
                               class="text-red-400 hover:text-red-300 text-xs font-bold uppercase"
                               onclick="return confirm('Remove this item from borrow list?')">
                                Remove
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-900/50">
                    <tr>
                        <td colspan="3" class="px-6 py-6 text-right font-bold text-gray-400 uppercase tracking-widest text-xs">
                            Total Replacement Value:
                        </td>
                        <td class="px-6 py-6 text-2xl font-black text-blue-500">
                            ₱{{ number_format($total, 2) }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-8 flex justify-between">
            <a href="{{ route('books.index') }}"
               class="bg-gray-800 text-gray-300 border border-gray-700 px-8 py-3 rounded-md hover:bg-gray-700 transition font-bold uppercase tracking-widest text-sm">
                Browse Books
            </a>
            <a href="{{ route('loans.checkout') }}"
               class="bg-blue-600 text-white px-10 py-3 rounded-md hover:bg-blue-700 transition font-black uppercase tracking-widest shadow-lg shadow-blue-500/20">
                Confirm Loan
            </a>
        </div>
    @else
        <div class="bg-gray-800 border-l-4 border-blue-500 p-8 text-center rounded-lg shadow-xl">
            <svg class="h-16 w-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <p class="text-xl text-gray-300 mb-6">Your borrow list is empty.</p>
            <a href="{{ route('books.index') }}" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-md font-bold uppercase tracking-widest hover:bg-blue-700 transition">
                Browse Books
            </a>
        </div>
    @endif
@endsection
