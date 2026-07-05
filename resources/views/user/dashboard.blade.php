@extends('layouts.app')

@section('title', 'My Dashboard - LendingSystem')

@section('header')
    <h1 class="text-3xl font-black text-black uppercase tracking-tighter leading-none">Account <br><span class="text-white">Overview</span></h1>
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

        // Loading state
        submitBtn.disabled = true;
        submitBtn.innerText = 'Analyzing...';
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
                categoryTitle.innerText = `Suggested Category: ${data.reason.split(' ')[0]}...`; // Simplified logic for demo
                // Since we don't have the category name in the response easily without extra DB hit in service,
                // let's adjust the service to return it or just show the reason.

                reasonText.innerText = `"${data.reason}"`;

                productsList.innerHTML = '';
                if (data.suggested_products && data.suggested_products.length > 0) {
                    data.suggested_products.forEach(product => {
                        const productCard = `
                            <div class="bg-white/10 p-4 rounded-xl border border-white/5 flex justify-between items-center">
                                <div>
                                    <p class="text-white font-bold text-xs">${product.name}</p>
                                    <p class="text-white/40 text-[10px] uppercase">${product.interest_rate}% Interest</p>
                                </div>
                                <a href="/loan-products/${product.id}" class="bg-white text-black px-4 py-2 rounded-lg font-black text-[10px] uppercase tracking-widest no-underline hover:bg-blue-600 hover:text-white transition">Apply</a>
                            </div>
                        `;
                        productsList.insertAdjacentHTML('beforeend', productCard);
                    });
                } else {
                    productsList.innerHTML = '<p class="text-white/40 text-xs italic">No specific products found for this category.</p>';
                }

                resultDiv.classList.remove('hidden');
            }
        } catch (error) {
            console.error('AI Error:', error);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerText = 'Analyze';
        }
    });
</script>
@endpush

@section('content')
    <!-- Account Status Alerts -->
    <div class="mb-12 space-y-4">
        @if(!$user->hasTwoFactorEnabled())
            <div class="bg-white/10 border border-black/10 text-black p-6 rounded-3xl shadow-sm flex items-center">
                <svg class="h-8 w-8 mr-4 text-black opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <div class="flex-1">
                    <p class="font-black uppercase text-xs tracking-widest mb-1">Security Recommendation</p>
                    <p class="text-sm font-bold opacity-60">Protect your assets by enabling 2FA.</p>
                </div>
                <a href="{{ route('profile.two-factor') }}" class="bg-black text-brand px-6 py-2 rounded-xl font-black text-xs uppercase tracking-widest no-underline transition hover:opacity-80">Secure Now</a>
            </div>
        @endif
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <div class="bg-black/5 rounded-3xl p-8 border border-black/5 shadow-sm">
            <p class="text-black/40 text-[10px] font-black uppercase tracking-[0.2em] mb-4">Total Appliations</p>
            <p class="text-4xl font-black text-black">{{ $totalLoans }}</p>
        </div>
        <div class="bg-black/5 rounded-3xl p-8 border border-black/5 shadow-sm">
            <p class="text-black/40 text-[10px] font-black uppercase tracking-[0.2em] mb-4">Active Assets</p>
            <p class="text-4xl font-black text-black">{{ $activeLoans }}</p>
        </div>
        <div class="bg-black/5 rounded-3xl p-8 border border-black/5 shadow-sm">
            <p class="text-black/40 text-[10px] font-black uppercase tracking-[0.2em] mb-4">Completed</p>
            <p class="text-4xl font-black text-black">{{ $completedLoans }}</p>
        </div>
        <div class="bg-black text-brand rounded-3xl p-8 border border-black shadow-xl">
            <p class="opacity-40 text-[10px] font-black uppercase tracking-[0.2em] mb-4">Outstanding Balance</p>
            <p class="text-3xl font-black">₱{{ number_format($totalBalance) }}</p>
        </div>
    </div>

    <!-- AI Smart Suggestion -->
    <div class="mb-12">
        <div class="bg-gradient-to-br from-blue-900 to-black rounded-3xl p-8 border border-white/10 shadow-2xl">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex-1">
                    <h2 class="text-xl font-black text-white uppercase tracking-tighter mb-2">Smart Loan Suggester</h2>
                    <p class="text-blue-200/60 text-sm font-bold">Describe your financial goal, and our AI will categorize it and suggest the best asset for you.</p>
                </div>
                <div class="flex-1">
                    <form id="ai-categorize-form" class="relative">
                        <input type="text"
                               id="ai-description"
                               placeholder="e.g., I want to buy a new car for my family"
                               class="w-full bg-white/5 border-white/10 rounded-2xl text-white py-4 px-6 focus:ring-blue-500 focus:border-blue-500 placeholder-white/20">
                        <button type="submit"
                                class="absolute right-2 top-2 bottom-2 bg-blue-600 hover:bg-blue-700 text-white px-6 rounded-xl font-black text-xs uppercase tracking-widest transition">
                            Analyze
                        </button>
                    </form>
                </div>
            </div>

            <div id="ai-result" class="mt-8 hidden animate-in fade-in slide-in-from-top-4">
                <div class="p-6 bg-white/5 rounded-2xl border border-white/10">
                    <div class="flex items-start gap-4">
                        <div class="bg-blue-600/20 p-3 rounded-xl">
                            <span class="text-2xl">🤖</span>
                        </div>
                        <div class="flex-1">
                            <h3 id="suggested-category" class="text-white font-black uppercase tracking-widest text-xs mb-2">Suggested Category: ...</h3>
                            <p id="ai-reason" class="text-blue-100/70 text-sm italic mb-6">"..."</p>

                            <div id="suggested-products-list" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Suggested products will appear here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Recent Loans -->
        <div class="lg:col-span-2 bg-black/5 rounded-3xl p-10 border border-black/5 shadow-sm">
            <h2 class="text-xs font-black text-black mb-8 uppercase tracking-[0.3em] opacity-40">Recent Applications</h2>

            @if($recentLoans->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="text-black/30 uppercase text-[10px] font-black tracking-widest border-b border-black/10">
                                <th class="text-left py-4">Loan ID</th>
                                <th class="text-left py-4">Asset</th>
                                <th class="text-left py-4">Principal</th>
                                <th class="text-left py-4">Status</th>
                                <th class="text-right py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5">
                            @foreach($recentLoans as $loan)
                            <tr class="group">
                                <td class="py-6 text-sm font-black text-black">#{{ $loan->id }}</td>
                                <td class="py-6 text-sm font-bold text-black/60">{{ $loan->loanProduct?->name }}</td>
                                <td class="py-6 text-sm font-black text-black">₱{{ number_format($loan->principal_amount) }}</td>
                                <td class="py-6">
                                    <span class="px-3 py-1 text-[10px] font-black uppercase tracking-tighter rounded-full border border-black/20 text-black">
                                        {{ $loan->status }}
                                    </span>
                                </td>
                                <td class="py-6 text-right">
                                    <a href="{{ route('loans.show', $loan) }}" class="text-black hover:opacity-60 transition font-black text-[10px] uppercase tracking-widest no-underline border-b-2 border-black">
                                        Details
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-black/40 font-bold italic mb-8">System standby. No applications found.</p>
                    <a href="{{ route('loan_products.index') }}" class="bg-black text-brand px-12 py-4 rounded-xl font-black uppercase tracking-widest text-xs no-underline hover:opacity-80 transition shadow-xl">
                        Browse Assets
                    </a>
                </div>
            @endif
        </div>

        <!-- Data Portability Section -->
        <div class="bg-black/5 rounded-3xl p-10 border border-black/5 shadow-sm">
            <h2 class="text-xs font-black text-black mb-8 uppercase tracking-[0.3em] opacity-40">System Tools</h2>

            <div class="space-y-4">
                <a href="{{ route('user.export.personal') }}"
                   class="flex items-center justify-between p-6 bg-white/20 rounded-2xl border border-black/5 hover:border-black/20 transition no-underline group">
                    <span class="text-black font-black text-[10px] uppercase tracking-widest">Financial Export (JSON)</span>
                    <svg class="h-5 w-5 text-black opacity-20 group-hover:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                </a>

                <div class="pt-10 mt-10 border-t border-black/10">
                    <h3 class="text-black font-black text-[10px] uppercase tracking-[0.2em] opacity-40 mb-6">Quick Links</h3>
                    <div class="grid grid-cols-1 gap-3">
                        <a href="{{ route('loan_products.index') }}" class="bg-black/5 border border-black/5 p-4 rounded-2xl text-center text-[10px] font-black text-black uppercase tracking-widest hover:bg-black hover:text-brand transition no-underline">Global Catalog</a>
                        <a href="{{ route('profile.edit') }}" class="bg-black/5 border border-black/5 p-4 rounded-2xl text-center text-[10px] font-black text-black uppercase tracking-widest hover:bg-black hover:text-brand transition no-underline">Settings</a>
                    </div>
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

        // Loading state
        submitBtn.disabled = true;
        submitBtn.innerText = 'Analyzing...';
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
                categoryTitle.innerText = `Suggested Category: ${data.reason.split(' ')[0]}...`; // Simplified logic for demo
                // Since we don't have the category name in the response easily without extra DB hit in service,
                // let's adjust the service to return it or just show the reason.

                reasonText.innerText = `"${data.reason}"`;

                productsList.innerHTML = '';
                if (data.suggested_products && data.suggested_products.length > 0) {
                    data.suggested_products.forEach(product => {
                        const productCard = `
                            <div class="bg-white/10 p-4 rounded-xl border border-white/5 flex justify-between items-center">
                                <div>
                                    <p class="text-white font-bold text-xs">${product.name}</p>
                                    <p class="text-white/40 text-[10px] uppercase">${product.interest_rate}% Interest</p>
                                </div>
                                <a href="/loan-products/${product.id}" class="bg-white text-black px-4 py-2 rounded-lg font-black text-[10px] uppercase tracking-widest no-underline hover:bg-blue-600 hover:text-white transition">Apply</a>
                            </div>
                        `;
                        productsList.insertAdjacentHTML('beforeend', productCard);
                    });
                } else {
                    productsList.innerHTML = '<p class="text-white/40 text-xs italic">No specific products found for this category.</p>';
                }

                resultDiv.classList.remove('hidden');
            }
        } catch (error) {
            console.error('AI Error:', error);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerText = 'Analyze';
        }
    });
</script>
@endpush
