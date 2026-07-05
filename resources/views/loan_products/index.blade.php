@extends('layouts.app')

@section('title', 'Loan Products - LendingSystem')

@section('header')
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-black text-black uppercase tracking-tighter">Available Loan Products</h1>
        @auth
            @if(auth()->user()->isAdmin())
                <a href="{{ request()->fullUrlWithQuery(['trashed' => request('trashed') ? null : 1]) }}"
                   class="text-xs font-black uppercase tracking-widest no-underline border-b-2 border-black pb-1 hover:opacity-60 transition">
                    {{ request('trashed') ? 'View Active' : 'View Deleted Archive' }}
                </a>
            @endif
        @endauth
    </div>
@endsection

@section('content')
    <x-breadcrumbs :links="['Global Catalog' => route('loan_products.index')]" />

    <!-- Search and Filter -->
    <div class="bg-black/5 p-8 rounded-2xl shadow-sm mb-12 border border-black/5">
        <form action="{{ route('loan_products.index') }}" method="GET" class="flex flex-wrap gap-4">
            <input type="hidden" name="trashed" value="{{ request('trashed') }}">
            <div class="flex-1 min-w-[200px]">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search by product name..."
                       class="w-full bg-white/20 border-black/10 rounded-xl px-4 py-3 text-black placeholder-black/30 font-bold focus:ring-black">
            </div>

            <div class="w-48">
                <select name="category" class="w-full bg-white/20 border-black/10 rounded-xl px-4 py-3 text-black font-bold focus:ring-black">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="bg-black text-brand px-10 py-3 rounded-xl font-black uppercase tracking-widest text-xs hover:opacity-80 transition shadow-lg">
                Filter
            </button>
        </form>
    </div>

    <!-- Loan Products Grid -->
    @if($loanProducts->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            @foreach($loanProducts as $product)
                <x-loan-product-card :product="$product" />
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-16">
            {{ $loanProducts->withQueryString()->links() }}
        </div>
    @else
        <div class="bg-black/5 rounded-3xl p-20 text-center border border-dashed border-black/10">
            <svg class="h-20 w-20 text-black/10 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <h3 class="text-xl font-black uppercase tracking-tight text-black/40">No Assets Identified</h3>
            <p class="text-xs font-bold text-black/20 mt-2 uppercase tracking-widest">Adjust your filtration parameters and try again.</p>
            <a href="{{ route('loan_products.index') }}" class="mt-8 inline-block bg-black text-brand px-8 py-3 rounded-xl font-black uppercase tracking-widest text-[10px] no-underline hover:opacity-80 transition">Reset Registry</a>
        </div>
    @endif
@endsection
