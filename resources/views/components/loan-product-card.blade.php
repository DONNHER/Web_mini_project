@props(['product'])

<div class="bg-black/5 rounded-2xl border border-black/5 overflow-hidden hover:border-black/20 transition flex flex-col group {{ $product->trashed() ? 'grayscale opacity-50' : '' }}">
    <div class="p-6 flex-grow">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[10px] font-black uppercase tracking-widest text-black/40">{{ $product->category?->name }}</span>
            @if($product->trashed())
                <span class="text-[8px] font-black uppercase tracking-widest bg-red-600 text-white px-2 py-0.5 rounded-full">Deleted</span>
            @endif
        </div>
        <h3 class="text-xl font-black text-black mb-3 uppercase tracking-tight group-hover:text-white transition">{{ $product->name }}</h3>
        <p class="text-black/60 text-xs mb-6 line-clamp-3 font-medium">{{ $product->description }}</p>

        <div class="space-y-3">
            <div class="flex justify-between items-end border-b border-black/5 pb-2">
                <span class="text-[10px] font-black uppercase tracking-tighter text-black/40">Interest Rate</span>
                <span class="text-lg font-black text-black leading-none">{{ $product->interest_rate }}%</span>
            </div>
            <div class="flex justify-between items-end border-b border-black/5 pb-2">
                <span class="text-[10px] font-black uppercase tracking-tighter text-black/40">Repayment Term</span>
                <span class="text-lg font-black text-black leading-none">{{ $product->duration_months }} Mo</span>
            </div>
        </div>
    </div>
    <div class="p-6">
        @if($product->trashed())
            <form action="{{ route('loan-products.restore', $product->id) }}" method="POST">
                @csrf
                <button type="submit" class="block w-full text-center bg-black text-brand py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:opacity-80 transition no-underline">
                    Restore Asset
                </button>
            </form>
        @else
            <a href="{{ route('loan_products.show', $product) }}" class="block w-full text-center bg-black text-brand py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:opacity-80 transition no-underline">
                View Details
            </a>
        @endif
    </div>
</div>
