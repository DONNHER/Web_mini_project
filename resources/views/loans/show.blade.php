@extends('layouts.app')

@section('title', 'Loan Details - LendingSystem')

@section('header')
    <h1 class="text-3xl font-black text-white uppercase tracking-tighter">Application <span class="text-white/40">#{{ $loan->id }}</span></h1>
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <div class="lg:col-span-2 space-y-12">
            <!-- Main Loan Info -->
            <div class="bg-black text-brand rounded-3xl p-10 border border-black shadow-2xl relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-xs font-black uppercase tracking-[0.3em] mb-12 opacity-40">Financial Configuration</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest opacity-40 mb-2">Total Repayment Amount</p>
                            <p class="text-6xl font-black text-white tracking-tighter">₱{{ number_format($loan->total_amount, 2) }}</p>
                        </div>
                        <div class="space-y-6">
                            <div class="flex justify-between border-b border-white/10 pb-4">
                                <span class="text-[10px] font-black uppercase tracking-widest opacity-40">Principal</span>
                                <span class="text-white font-black">₱{{ number_format($loan->principal_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between border-b border-white/10 pb-4">
                                <span class="text-[10px] font-black uppercase tracking-widest opacity-40">Interest Rate</span>
                                <span class="text-white font-black">{{ $loan->interest_rate }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[10px] font-black uppercase tracking-widest opacity-40">Term Duration</span>
                                <span class="text-white font-black">{{ $loan->term_months }} Months</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="absolute -right-20 -bottom-20 opacity-5">
                    <svg class="w-80 h-80" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                </div>
            </div>

            <!-- Purpose and AI Tagging -->
            <div class="bg-black/5 rounded-3xl p-10 border border-black/5 shadow-sm">
                <div class="flex justify-between items-start mb-8">
                    <h2 class="text-xs font-black text-black uppercase tracking-[0.3em] opacity-40">Application Narrative</h2>
                    @if($loan->ai_tag)
                        <span class="bg-blue-600 text-white px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">
                            AI Tagged: {{ $loan->ai_tag }}
                        </span>
                    @endif
                </div>

                <div class="bg-white/20 p-8 rounded-2xl border border-black/5 italic text-black font-medium leading-relaxed">
                    "{{ $loan->purpose ?? 'No purpose provided.' }}"
                </div>
            </div>

            <!-- Repayment History -->
            <div class="bg-black/5 rounded-3xl p-10 border border-black/5 shadow-sm">
                <h2 class="text-xs font-black text-black mb-8 uppercase tracking-[0.3em] opacity-40">Repayment Schedule</h2>
                @if($loan->repayments->count() > 0)
                    <!-- Table for repayments -->
                @else
                    <div class="text-center py-8">
                        <p class="text-black/30 font-bold text-sm italic">No repayment records found for this asset.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-12">
            <!-- Status Card -->
            <div class="bg-black/5 rounded-3xl p-10 border border-black/5 shadow-sm">
                <h2 class="text-xs font-black text-black uppercase tracking-[0.3em] mb-8 opacity-40">Processing Status</h2>
                <div class="flex items-center space-x-4">
                    <div class="w-4 h-4 rounded-full animate-pulse
                        @if($loan->status == 'pending') bg-yellow-500
                        @elseif($loan->status == 'approved') bg-blue-500
                        @elseif($loan->status == 'completed') bg-green-500
                        @else bg-red-500 @endif">
                    </div>
                    <span class="text-2xl font-black uppercase tracking-tighter text-black">{{ $loan->status }}</span>
                </div>
                <p class="text-[10px] font-black text-black/40 uppercase mt-4 tracking-widest">Last Updated: {{ $loan->updated_at->format('M d, Y') }}</p>

                <div class="mt-8">
                    <a href="{{ route('loans.invoice', $loan) }}" class="w-full inline-block text-center bg-black text-brand font-black py-4 rounded-xl hover:opacity-90 transition uppercase tracking-widest text-[10px] shadow-lg">
                        Download Invoice (PDF)
                    </a>
                </div>
            </div>

            <!-- Borrower Info -->
            <div class="bg-black/5 rounded-3xl p-10 border border-black/5 shadow-sm">
                <h2 class="text-xs font-black text-black uppercase tracking-[0.3em] mb-8 opacity-40">Entity Details</h2>
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest opacity-40">Primary Borrower</p>
                        <p class="text-sm font-black text-black">{{ $loan->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest opacity-40">Verification Email</p>
                        <p class="text-sm font-bold text-black/60">{{ $loan->user->email }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-12">
        <a href="{{ route('loans.index') }}" class="text-black hover:opacity-60 transition font-black text-xs uppercase tracking-[0.2em] no-underline border-b-4 border-black pb-1">
            ← Global Transaction History
        </a>
    </div>
@endsection
