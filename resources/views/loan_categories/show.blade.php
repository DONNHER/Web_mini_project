@extends('layouts.app')

@section('title', $category->name . ' - LendingSystem')

@section('header')
    <div class="flex flex-col">
        <h1 class="text-4xl font-extrabold text-white tracking-tight">{{ $category->name }}</h1>
        <p class="text-gray-400 mt-3 text-lg leading-relaxed max-w-3xl">{{ $category->description }}</p>
    </div>
@endsection

@section('content')
    <div class="mb-8 flex items-center space-x-2">
        <span class="bg-blue-600/20 text-blue-400 px-3 py-1 rounded-full text-sm font-bold border border-blue-500/30">
            {{ $category->loanProducts->count() }} products
        </span>
        <span class="text-gray-500 text-sm">available in this category</span>
    </div>

    @if($loanProducts->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($loanProducts as $product)
                <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700 hover:border-blue-500 transition shadow-xl">
                    <h3 class="text-white font-black uppercase tracking-tight text-lg mb-2">{{ $product->name }}</h3>
                    <p class="text-gray-400 text-sm line-clamp-2 mb-4">{{ $product->description }}</p>

                    <div class="flex justify-between items-center pt-4 border-t border-gray-700">
                        <div>
                            <p class="text-blue-400 font-black text-xl">{{ $product->interest_rate }}%</p>
                            <p class="text-[8px] text-gray-500 uppercase font-bold">Annual Rate</p>
                        </div>
                        <a href="{{ route('loan_products.show', $product) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest no-underline hover:bg-blue-700 transition">View Details</a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-10 pagination-dark">
            {{ $loanProducts->links() }}
        </div>
    @else
        <div class="bg-gray-800 border-l-4 border-blue-500 p-6 text-gray-300 rounded shadow-lg">
            <div class="flex items-center">
                <svg class="h-6 w-6 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="font-medium">No products found in this category at the moment. Please check back later.</p>
            </div>
        </div>
    @endif
@endsection
