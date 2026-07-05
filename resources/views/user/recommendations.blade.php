@extends('layouts.app')

@section('title', 'AI Credit Insights - LendingSystem')

@section('header')
    <h2 class="text-3xl font-black text-black uppercase tracking-tighter leading-none">
        AI Financial <br><span class="text-white">Advisor</span>
    </h2>
@endsection

@section('content')
<div class="space-y-12">
    <!-- Credit Health Card -->
    <div class="bg-black text-brand rounded-3xl shadow-2xl border border-black overflow-hidden p-12">
        <div class="md:flex items-center gap-12">
            <!-- Score Meter -->
            <div class="md:w-1/3 flex flex-col items-center justify-center">
                <div class="relative inline-flex items-center justify-center">
                    <svg class="w-48 h-48">
                        <circle class="text-brand/10" stroke-width="12" stroke="currentColor" fill="transparent" r="80" cx="96" cy="96"/>
                        <circle class="text-brand"
                                stroke-width="12"
                                stroke-dasharray="{{ ($insights['reliability_score'] / 100) * 502 }}"
                                stroke-linecap="round"
                                stroke="currentColor"
                                fill="transparent"
                                r="80" cx="96" cy="96"/>
                    </svg>
                    <span class="absolute text-5xl font-black text-white">{{ $insights['reliability_score'] }}</span>
                </div>
                <p class="mt-6 text-[10px] font-black uppercase tracking-[0.3em] opacity-40">Reliability Index</p>
                <span class="mt-4 px-6 py-2 rounded-xl text-xs font-black uppercase bg-brand text-black">
                    {{ $insights['status'] }}
                </span>
            </div>

            <!-- Insights -->
            <div class="md:w-2/3 space-y-8 mt-12 md:mt-0">
                <div>
                    <h3 class="text-brand/40 text-[10px] font-black uppercase tracking-[0.2em] mb-4">Neural Analysis Insight</h3>
                    <p class="text-white text-3xl font-black leading-tight italic tracking-tighter uppercase">"{{ $insights['ai_insight'] }}"</p>
                </div>

                <div class="p-8 bg-brand/5 rounded-2xl border border-brand/10">
                    <h4 class="text-brand/40 text-[10px] font-black uppercase tracking-[0.2em] mb-2">Strategy Recommendation</h4>
                    <p class="text-white font-black text-lg tracking-tight uppercase">{{ $insights['recommendation'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Conditional Section: Pre-Approved Offers -->
    @if(count($insights['suggested_products']) > 0)
        <div>
            <div class="flex items-center space-x-4 mb-8">
                <h3 class="text-xs font-black text-black uppercase tracking-[0.3em] opacity-40">Prime Market Access</h3>
                <div class="flex-1 h-px bg-black/10"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($insights['suggested_products'] as $product)
                    <div class="bg-black/5 rounded-3xl border-2 border-black/5 p-8 hover:border-black transition duration-300 relative group">
                        <div class="absolute -top-3 right-8 bg-black text-brand text-[8px] font-black px-3 py-1 rounded-full uppercase tracking-widest">Pre-Approved</div>

                        <h4 class="text-black font-black text-xl mb-4 uppercase tracking-tight">{{ $product['name'] }}</h4>
                        <div class="flex justify-between items-end mb-8 border-b border-black/5 pb-4">
                            <span class="text-black font-black text-4xl leading-none">{{ $product['interest_rate'] }}%</span>
                            <span class="text-black/40 text-[10px] font-black uppercase mb-1">{{ $product['duration_months'] }} Months</span>
                        </div>
                        <a href="{{ route('loan_products.show', $product['id']) }}" class="block w-full text-center bg-black text-brand py-4 rounded-xl font-black text-xs uppercase tracking-widest transition no-underline hover:opacity-80">
                            Lock Rate
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-black/5 p-12 rounded-3xl border border-dashed border-black/20 text-center">
            <svg class="h-16 w-16 text-black/10 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            <h3 class="text-black font-black uppercase tracking-[0.2em] text-sm opacity-40">Restricted Market Access</h3>
            <p class="text-black/60 font-bold text-sm mt-4 max-w-md mx-auto">Elevate your reliability index to 80+ by ensuring all future payments are processed on or before the asset due date.</p>
        </div>
    @endif
</div>
@endsection
