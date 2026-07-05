@extends('layouts.app')

@section('title', 'LendingSystem - Financial Solutions')

@section('content')
    <!-- Hero Section -->
    <div class="bg-black/5 text-black rounded-3xl p-12 mb-12 border border-black/10 shadow-sm relative overflow-hidden">
        <div class="relative z-10">
            <h1 class="text-6xl font-black mb-4 uppercase tracking-tighter leading-none">Welcome to <br><span class="text-white">LendingSystem</span></h1>
            <p class="text-xl font-bold mb-8 max-w-2xl opacity-70">Simple, transparent, and fast financial solutions for your personal and business needs.</p>
            <a href="{{ route('loan_products.index') }}"
               class="bg-black text-brand px-10 py-4 rounded-xl font-black hover:opacity-90 transition shadow-xl inline-block uppercase tracking-widest text-sm no-underline">
                Explore Loan Products
            </a>
        </div>
        <div class="absolute -right-20 -bottom-20 text-[20rem] font-black text-black/5 select-none pointer-events-none uppercase">LOAN</div>
    </div>

    <!-- Categories Section -->
    <section class="mb-16">
        <h2 class="text-xs font-black mb-8 text-black uppercase tracking-[0.3em] opacity-40">Loan Categories</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($categories as $category)
                <div class="bg-black/5 p-8 rounded-2xl border border-black/5 hover:border-black/20 transition text-center group cursor-default">
                    <h3 class="font-black text-black uppercase tracking-tight group-hover:scale-105 transition">{{ $category->name }}</h3>
                    <p class="text-xs font-bold text-black/40 mt-2 uppercase">{{ $category->loan_products_count }} products</p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Featured Products Section -->
    <section>
        <div class="flex justify-between items-end mb-8">
            <h2 class="text-xs font-black text-black uppercase tracking-[0.3em] opacity-40">Featured Loan Products</h2>
            <a href="{{ route('loan_products.index') }}" class="text-black font-black text-xs uppercase tracking-widest no-underline border-b-2 border-black pb-1 hover:opacity-60 transition">View All →</a>
        </div>

        @if($featuredProducts->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-6">
                @foreach($featuredProducts as $product)
                    <x-loan-product-card :product="$product" />
                @endforeach
            </div>
        @else
            <div class="bg-black/5 border-l-4 border-black p-6 text-black rounded-xl italic">
                <p>No loan products available at the moment. Check back soon!</p>
            </div>
        @endif
    </section>
@endsection
