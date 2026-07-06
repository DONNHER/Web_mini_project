@extends('layouts.app')

@section('title', 'Console - PIL')

@section('header')
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <span class="text-[#FF6B00] font-black uppercase tracking-[0.4em] text-[10px] mb-2 block">System Console</span>
            <h1 class="text-4xl font-black text-[#1A1A1A] uppercase tracking-tighter leading-none">Account <span class="text-[#FF6B00]">Intelligence</span></h1>
        </div>
        <div class="flex items-center space-x-2">
            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
            <span class="text-[10px] font-black uppercase tracking-widest text-[#1A1A1A]/40">Node Active: PIL-{{ auth()->id() }}</span>
        </div>
    </div>
@endsection

@section('content')
    <!-- Account Status Alerts -->
    <div class="mb-12 space-y-4">
        @if(!$user->hasTwoFactorEnabled())
            <div class="bg-[#FF6B00] text-white p-8 rounded-[2rem] shadow-xl shadow-orange-500/20 flex flex-col md:flex-row items-center gap-6">
                <div class="bg-white/20 p-4 rounded-2xl">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div class="flex-1 text-center md:text-left">
                    <p class="font-black uppercase text-xs tracking-widest mb-1 opacity-80">Security Protocol Required</p>
                    <p class="text-lg font-bold">Multi-Factor Authentication is currently disabled for your account.</p>
                </div>
                <a href="{{ route('profile.two-factor') }}" class="bg-white text-[#FF6B00] px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest no-underline transition hover:scale-105 transform">Enable MFA</a>
            </div>
        @endif
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <div class="card p-8 group hover:border-[#FF6B00]/30 transition-colors">
            <p class="text-[#1A1A1A]/40 text-[10px] font-black uppercase tracking-widest mb-4">Registry Entries</p>
            <p class="text-4xl font-black text-[#1A1A1A] group-hover:text-[#FF6B00] transition-colors">{{ $totalLoans }}</p>
        </div>
        <div class="card p-8 group hover:border-[#FF6B00]/30 transition-colors">
            <p class="text-[#1A1A1A]/40 text-[10px] font-black uppercase tracking-widest mb-4">Active Assets</p>
            <p class="text-4xl font-black text-[#1A1A1A] group-hover:text-[#FF6B00] transition-colors">{{ $activeLoans }}</p>
        </div>
        <div class="card p-8 group hover:border-[#FF6B00]/30 transition-colors">
            <p class="text-[#1A1A1A]/40 text-[10px] font-black uppercase tracking-widest mb-4">Terminated</p>
            <p class="text-4xl font-black text-[#1A1A1A] group-hover:text-[#FF6B00] transition-colors">{{ $completedLoans }}</p>
        </div>
        <div class="bg-[#1A1A1A] rounded-[2rem] p-8 border border-[#1A1A1A] shadow-2xl shadow-black/20">
            <p class="text-white/40 text-[10px] font-black uppercase tracking-widest mb-4">Capital Liability</p>
            <p class="text-3xl font-black text-white">₱{{ number_format($totalBalance) }}</p>
        </div>
    </div>

    <!-- AI Smart Suggestion -->
    <div class="mb-12">
        <div class="bg-white rounded-[3rem] p-10 border border-[#FF6B00]/10 shadow-2xl shadow-orange-500/5 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-8 opacity-5">
                <svg class="w-40 h-40" fill="#FF6B00" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
            </div>

            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-12 relative z-10">
                <div class="max-w-md">
                    <div class="flex items-center space-x-2 mb-4">
                        <span class="bg-[#FF6B00]/10 text-[#FF6B00] text-[8px] font-black uppercase tracking-widest px-3 py-1 rounded-full">AI Core v2.4</span>
                    </div>
                    <h2 class="text-3xl font-black text-[#1A1A1A] uppercase tracking-tighter mb-4">Capital <br><span class="text-[#FF6B00]">Pathfinder</span></h2>
                    <p class="text-[#1A1A1A]/60 text-sm font-bold leading-relaxed">Input your objective. Our neural engine will identify the optimal asset configuration for your needs.</p>
                </div>

                <div class="flex-1 w-full">
                    <form id="ai-categorize-form" class="relative group">
                        <input type="text"
                               id="ai-description"
                               placeholder="e.g. Scaling my retail inventory for the holiday season"
                               class="w-full bg-[#FEF6F0] border-none rounded-[1.5rem] text-[#1A1A1A] py-6 px-8 focus:ring-4 focus:ring-[#FF6B00]/10 font-bold placeholder-[#1A1A1A]/20 transition-all">
                        <button type="submit"
                                class="absolute right-3 top-3 bottom-3 bg-[#FF6B00] hover:bg-[#EA580C] text-white px-8 rounded-2xl font-black text-xs uppercase tracking-widest transition shadow-lg shadow-orange-500/20">
                            Execute
                        </button>
                    </form>
                </div>
            </div>

            <div id="ai-result" class="mt-12 hidden animate-in fade-in zoom-in duration-500">
                <div class="p-8 bg-[#FEF6F0] rounded-[2rem] border border-[#FF6B00]/20">
                    <div class="flex flex-col md:flex-row gap-8">
                        <div class="bg-[#FF6B00] w-16 h-16 rounded-2xl flex items-center justify-center shrink-0 shadow-lg shadow-orange-500/20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div class="flex-1">
                            <h3 id="suggested-category" class="text-[#1A1A1A] font-black uppercase tracking-widest text-xs mb-2">Inference Result</h3>
                            <p id="ai-reason" class="text-[#1A1A1A]/70 text-lg font-bold mb-8 leading-tight italic">"..."</p>

                            <div id="suggested-products-list" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- AI Suggestions -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Recent Loans -->
        <div class="lg:col-span-2 card p-10">
            <div class="flex items-center justify-between mb-12">
                <h2 class="text-xs font-black text-[#1A1A1A] uppercase tracking-[0.4em] opacity-40 text-center">Active Registry</h2>
                <a href="{{ route('loans.index') }}" class="text-[#FF6B00] font-black text-[10px] uppercase tracking-widest hover:underline">Full Audit →</a>
            </div>

            @if($recentLoans->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="text-[#1A1A1A]/30 uppercase text-[9px] font-black tracking-[0.2em] border-b border-[#FEF6F0]">
                                <th class="text-left py-4">ID</th>
                                <th class="text-left py-4">Asset Node</th>
                                <th class="text-left py-4">Principal</th>
                                <th class="text-left py-4">State</th>
                                <th class="text-right py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#FEF6F0]">
                            @foreach($recentLoans as $loan)
                            <tr class="group hover:bg-[#FEF6F0]/50 transition-colors">
                                <td class="py-6 text-[10px] font-black text-[#1A1A1A]/40">#{{ str_pad($loan->id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td class="py-6 text-sm font-extrabold text-[#1A1A1A]">{{ $loan->loanProduct?->name }}</td>
                                <td class="py-6 text-sm font-black text-[#FF6B00]">₱{{ number_format($loan->principal_amount) }}</td>
                                <td class="py-6">
                                    <span class="px-3 py-1 text-[9px] font-black uppercase tracking-widest rounded-lg bg-white border border-[#FFEDD5] text-[#1A1A1A]">
                                        {{ $loan->status }}
                                    </span>
                                </td>
                                <td class="py-6 text-right">
                                    <a href="{{ route('loans.show', $loan) }}" class="btn-secondary px-4 py-2 text-[8px] tracking-[0.2em] no-underline">Details</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-20">
                    <div class="w-20 h-20 bg-[#FEF6F0] rounded-3xl flex items-center justify-center mx-auto mb-6 text-[#1A1A1A]/10">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </div>
                    <p class="text-[#1A1A1A]/40 font-black uppercase tracking-[0.2em] text-[10px] mb-8 italic">Registry empty</p>
                    <a href="{{ route('loan_products.index') }}" class="btn-primary">Initialize Registry</a>
                </div>
            @endif
        </div>

        <!-- Data Portability Section -->
        <div class="space-y-6">
            <div class="card p-10">
                <h2 class="text-xs font-black text-[#1A1A1A] uppercase tracking-[0.4em] opacity-40 mb-10">System Utilities</h2>
                <div class="space-y-4">
                    <a href="{{ route('user.export.personal') }}"
                       class="flex items-center justify-between p-6 bg-[#FEF6F0] rounded-2xl border border-[#FFEDD5] hover:border-[#FF6B00]/30 transition-all no-underline group">
                        <div class="flex items-center">
                            <div class="bg-white p-2 rounded-lg shadow-sm mr-4 text-[#FF6B00]">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                            </div>
                            <span class="text-[#1A1A1A] font-black text-[10px] uppercase tracking-widest">Protocol Export (JSON)</span>
                        </div>
                        <svg class="h-4 w-4 text-[#FF6B00] opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </a>
                </div>
            </div>

            <div class="bg-[#FF6B00] rounded-[2rem] p-10 text-white shadow-xl shadow-orange-500/20">
                <h3 class="font-black text-xs uppercase tracking-[0.3em] mb-4 opacity-60">Node Status</h3>
                <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest border-b border-white/10 pb-4 mb-4">
                    <span>Latency</span>
                    <span class="text-white/40">12ms</span>
                </div>
                <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest">
                    <span>Encyption</span>
                    <span class="bg-white/20 px-2 py-0.5 rounded text-[8px]">AES-256</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('ai-categorize-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const description = document.getElementById('ai-description').value.trim();
        if (!description) return;

        const resultDiv = document.getElementById('ai-result');
        const categoryTitle = document.getElementById('suggested-category');
        const reasonText = document.getElementById('ai-reason');
        const productsList = document.getElementById('suggested-products-list');
        const submitBtn = e.target.querySelector('button');

        submitBtn.disabled = true;
        submitBtn.innerText = 'Neural Sync...';
        resultDiv.classList.add('hidden');

        try {
            const response = await fetch('{{ route("ai.categorize") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ description })
            });

            const data = await response.json();

            if (data.category_id) {
                categoryTitle.innerText = `Inference: ${data.reason.split(' ')[0]} Classification`;
                reasonText.innerText = `"${data.reason}"`;

                productsList.innerHTML = '';
                if (data.suggested_products && data.suggested_products.length > 0) {
                    data.suggested_products.forEach(product => {
                        const productCard = `
                            <div class="bg-white p-6 rounded-2xl border border-[#FFEDD5] flex justify-between items-center shadow-sm">
                                <div>
                                    <p class="text-[#1A1A1A] font-black text-sm uppercase tracking-tighter">${product.name}</p>
                                    <p class="text-[#FF6B00] font-black text-[10px] uppercase mt-1">${product.interest_rate}% APR</p>
                                </div>
                                <a href="/loan-products/${product.id}" class="bg-[#1A1A1A] text-white px-6 py-2 rounded-xl font-black text-[8px] uppercase tracking-widest no-underline hover:bg-[#FF6B00] transition-colors shadow-lg">Access</a>
                            </div>
                        `;
                        productsList.insertAdjacentHTML('beforeend', productCard);
                    });
                } else {
                    productsList.innerHTML = '<p class="text-[#1A1A1A]/40 text-[10px] font-black uppercase tracking-widest italic p-4">No optimal assets identified.</p>';
                }

                resultDiv.classList.remove('hidden');
            }
        } catch (error) {
            console.error('AI Error:', error);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerText = 'Execute';
        }
    });
</script>
@endpush
