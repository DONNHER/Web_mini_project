@extends('layouts.app')

@section('title', $category->name . ' - PIL')

@section('header')
    <div class="flex flex-col">
        <span class="text-[#FF6B00] font-black uppercase tracking-[0.4em] text-[10px] mb-2">Category Detail</span>
        <h1 class="text-5xl font-black text-[#1A1A1A] uppercase tracking-tighter">{{ $category->name }}</h1>
        <p class="text-[#1A1A1A]/50 mt-4 text-lg font-bold max-w-3xl leading-snug">{{ $category->description }}</p>
    </div>
@endsection

@section('content')
    <div class="mb-12 flex items-center space-x-4">
        <span class="bg-[#FF6B00] text-white px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest shadow-lg shadow-orange-500/20">
            {{ $category->loanProducts->count() }} assets
        </span>
        <span class="text-[#1A1A1A]/30 text-[10px] font-black uppercase tracking-widest">available in registry</span>
    </div>

    @if($loanProducts->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            @foreach($loanProducts as $product)
                <x-loan-product-card :product="$product" />
            @endforeach
        </div>

        <div class="mt-20">
            {{ $loanProducts->links() }}
        </div>
    @else
        <div class="card p-20 text-center border-dashed border-2">
            <div class="w-20 h-20 bg-[#FEF6F0] rounded-3xl flex items-center justify-center mx-auto mb-6 text-[#1A1A1A]/10">
                <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H4a2 2 0 00-2 2v7m18 0v5a2 2 0 01-2 2H4a2 2 0 01-2-2v-5m18 0h-2m-9 0H4" />
                </svg>
            </div>
            <h3 class="text-xl font-black uppercase tracking-tight text-[#1A1A1A]/40 italic">Registry Empty</h3>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#1A1A1A]/20 mt-2">No assets mapped to this classification.</p>
        </div>
    @endif
@endsection
