@props(['product'])

<div class="card p-8 group hover:border-[#FF6B00]/30 transition-all flex flex-col {{ $product->trashed() ? 'grayscale opacity-50' : '' }} relative overflow-hidden">
    @if($product->interest_rate < 10)
        <div class="absolute top-0 right-0">
            <span class="bg-[#FF6B00] text-white text-[8px] font-black uppercase tracking-widest px-4 py-2 rounded-bl-2xl">Premium Tier</span>
        </div>
    @endif

    <div class="flex-grow">
        <div class="flex items-center justify-between mb-6">
            <div class="bg-[#FFEDD5] p-3 rounded-2xl text-[#FF6B00]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
            </div>
            <span class="text-[9px] font-black uppercase tracking-[0.2em] text-[#1A1A1A]/40">{{ $product->category?->name }}</span>
        </div>

        <h3 class="text-2xl font-black text-[#1A1A1A] mb-2 uppercase tracking-tighter group-hover:text-[#FF6B00] transition-colors leading-none">{{ $product->name }}</h3>
        <p class="text-[#1A1A1A]/50 text-xs mb-8 line-clamp-2 font-bold leading-relaxed">{{ $product->description }}</p>

        <div class="grid grid-cols-2 gap-4">
            <div class="bg-[#FEF6F0] p-4 rounded-2xl">
                <p class="text-[8px] font-black uppercase tracking-widest text-[#1A1A1A]/30 mb-1">Interest</p>
                <p class="text-lg font-black text-[#1A1A1A]">{{ $product->interest_rate }}%</p>
            </div>
            <div class="bg-[#FEF6F0] p-4 rounded-2xl">
                <p class="text-[8px] font-black uppercase tracking-widest text-[#1A1A1A]/30 mb-1">Duration</p>
                <p class="text-lg font-black text-[#1A1A1A]">{{ $product->duration_months }}M</p>
            </div>
        </div>
    </div>

    <div class="mt-8">
        @if($product->trashed())
            <form action="{{ route('loan-products.restore', $product->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn-primary w-full text-[10px]">Restore Asset</button>
            </form>
        @else
            <a href="{{ route('loan_products.show', $product) }}" class="btn-primary w-full text-[10px] no-underline flex items-center justify-center">
                <span>View Details</span>
                <svg class="w-3 h-3 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
            </a>
        @endif
    </div>
</div>
