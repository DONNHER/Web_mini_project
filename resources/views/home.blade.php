@extends('layouts.app')

@section('title', 'PIL - Point of Sale and Lending System')

@section('content')
    <!-- Hero Section -->
    <div class="bg-[#1A1A1A] text-white rounded-[3rem] p-12 lg:p-20 mb-16 shadow-2xl relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-12 opacity-10 group-hover:scale-110 transition-transform duration-1000">
            <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
        </div>

        <div class="relative z-10">
            <span class="bg-[#FF6B00] text-white text-[10px] font-black uppercase tracking-[0.4em] px-4 py-1.5 rounded-full mb-8 inline-block">PIL Core System</span>
            <h1 class="text-6xl lg:text-8xl font-black mb-8 uppercase tracking-tighter leading-[0.9]">Master <br>Your <span class="text-[#FF6B00]">Capital</span></h1>
            <p class="text-xl font-bold mb-12 max-w-xl opacity-60">Professional grade financial infrastructure for micro-lending and point-of-sale operations.</p>
            <div class="flex gap-4">
                <a href="{{ route('loan_products.index') }}" class="btn-primary no-underline px-10">Launch Catalog</a>
                <a href="{{ route('chatbot.index') }}" class="bg-white/10 text-white hover:bg-white/20 px-10 py-4 rounded-2xl font-black text-xs uppercase tracking-widest no-underline transition-all">AI Assistant</a>
            </div>
        </div>
    </div>

    <!-- Categories Section -->
    <section class="mb-24">
        <div class="flex items-center justify-between mb-12">
            <h2 class="text-xs font-black text-[#1A1A1A] uppercase tracking-[0.4em] opacity-40">Classification Matrix</h2>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($categories as $category)
                <a href="{{ route('loan_products.index', ['category' => $category->id]) }}" class="card p-10 hover:bg-[#FF6B00] hover:text-white transition-all duration-300 no-underline group">
                    <h3 class="text-xl font-black uppercase tracking-tight mb-2 group-hover:scale-105 transition-transform">{{ $category->name }}</h3>
                    <p class="text-[10px] font-black opacity-40 uppercase group-hover:text-white group-hover:opacity-100">{{ $category->loan_products_count }} protocols</p>
                </a>
            @endforeach
        </div>
    </section>

    <!-- Featured Products Section -->
    <section>
        <div class="flex justify-between items-end mb-12">
            <div>
                <h2 class="text-xs font-black text-[#1A1A1A] uppercase tracking-[0.4em] opacity-40 mb-2">Priority Assets</h2>
                <p class="text-2xl font-black text-[#1A1A1A] uppercase tracking-tighter">High Yield Inventory</p>
            </div>
            <a href="{{ route('loan_products.index') }}" class="text-[#FF6B00] font-black text-xs uppercase tracking-widest no-underline border-b-4 border-[#FF6B00] pb-2 hover:opacity-60 transition">Explore All Assets →</a>
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
