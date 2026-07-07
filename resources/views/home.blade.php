@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <!-- Hero Section -->

    <!-- Featured Products Section -->
    <section>
        <div class="flex justify-between items-end mb-12">
            <div>
                <h2 class="text-xs font-black text-[#1A1A1A] uppercase tracking-[0.4em] opacity-40 mb-2">Priority Products</h2>
                <p class="text-2xl font-black text-[#1A1A1A] uppercase tracking-tighter">High Yield Catalog</p>
            </div>
            <a href="{{ route('loan_products.index') }}" class="text-[#FF6B00] font-black text-xs uppercase tracking-widest no-underline border-b-4 border-[#FF6B00] pb-2 hover:opacity-60 transition">Explore Loan Management →</a>
        </div>

        @if($featuredProducts->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                @foreach($featuredProducts as $product)
                    <x-loan-product-card :product="$product" />
                @endforeach
            </div>
        @else
            <div class="card p-20 text-center border-dashed border-2">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#1A1A1A]/20 italic">No priority assets currently deployed.</p>
            </div>
        @endif
    </section>
@endsection
