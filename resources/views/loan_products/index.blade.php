@extends('layouts.app')

@section('title', 'Global Catalog - PIL')

@section('header')
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <span class="text-[#FF6B00] font-black uppercase tracking-[0.4em] text-[10px] mb-2 block">System Inventory</span>
            <h1 class="text-5xl font-black text-[#1A1A1A] uppercase tracking-tighter leading-none">Global <span class="text-[#FF6B00]">Catalog</span></h1>
        </div>
        @auth
            @if(auth()->user()->isAdmin())
                <a href="{{ request()->fullUrlWithQuery(['trashed' => request('trashed') ? null : 1]) }}"
                   class="btn-secondary px-6 py-3 text-[8px] tracking-[0.2em] no-underline">
                    {{ request('trashed') ? 'Active Ledger' : 'Archive Registry' }}
                </a>
            @endif
        @endauth
    </div>
@endsection

@section('content')
    <!-- Search and Filter -->
    <div class="card p-10 mb-16 shadow-xl shadow-orange-500/5">
        <form action="{{ route('loan_products.index') }}" method="GET" class="flex flex-col md:flex-row gap-6">
            <input type="hidden" name="trashed" value="{{ request('trashed') }}">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none text-[#1A1A1A]/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Scan asset names..."
                       class="w-full bg-[#FEF6F0] border-none rounded-2xl pl-14 py-4 font-bold focus:ring-4 focus:ring-[#FF6B00]/5 placeholder-[#1A1A1A]/20">
            </div>

            <div class="w-full md:w-64">
                <select name="category" class="w-full bg-[#FEF6F0] border-none rounded-2xl py-4 font-bold focus:ring-4 focus:ring-[#FF6B00]/5">
                    <option value="">Matrix: All</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn-primary px-12">
                Scan Matrix
            </button>
        </form>
    </div>

    <!-- Loan Products Grid -->
    @if($loanProducts->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-10">
            @foreach($loanProducts as $product)
                <x-loan-product-card :product="$product" />
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-20">
            {{ $loanProducts->withQueryString()->links() }}
        </div>
    @else
        <div class="card p-32 text-center border-dashed border-2">
            <div class="w-24 h-24 bg-[#FEF6F0] rounded-[2rem] flex items-center justify-center mx-auto mb-8 text-[#1A1A1A]/5">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <h3 class="text-2xl font-black uppercase tracking-tight text-[#1A1A1A]/30 italic">No Assets Identified</h3>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-[#1A1A1A]/20 mt-4 max-w-sm mx-auto leading-relaxed">System scan returned null. Try recalibrating your filtration parameters.</p>
            <a href="{{ route('loan_products.index') }}" class="btn-primary mt-12 no-underline">Reset Matrix</a>
        </div>
    @endif
@endsection
