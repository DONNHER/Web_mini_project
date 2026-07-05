@extends('layouts.app')

@section('title', $loanProduct->name . ' - LendingSystem')

@section('content')
    <x-breadcrumbs :links="[
        'Global Catalog' => route('loan_products.index'),
        $loanProduct->name => '#'
    ]" />

    <div class="max-w-6xl mx-auto space-y-12">
        <div class="bg-black/5 rounded-3xl overflow-hidden border border-black/10 shadow-sm">
            <div class="md:flex">
                <!-- Product Info Icon -->
                <div class="md:w-1/3 bg-black/10 p-12 flex items-center justify-center border-r border-black/10">
                    <div class="text-center">
                        <svg class="h-48 w-48 text-black/20 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-black font-black uppercase tracking-[0.3em] text-xs opacity-40">Lending Asset</p>
                    </div>
                </div>

                <!-- Product Details -->
                <div class="md:w-2/3 p-12">
                    <span class="text-black font-black uppercase tracking-widest text-[10px] opacity-40">{{ $loanProduct->category?->name }}</span>
                    <h1 class="text-5xl font-black text-black mt-2 uppercase tracking-tighter">{{ $loanProduct->name }}</h1>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-12">
                        <div class="bg-white/20 p-6 rounded-2xl border border-black/5">
                            <p class="text-black/40 text-[10px] font-black uppercase tracking-widest mb-1">Interest Rate</p>
                            <p class="text-3xl font-black text-black leading-none">{{ $loanProduct->interest_rate }}%</p>
                        </div>
                        <div class="bg-white/20 p-6 rounded-2xl border border-black/5">
                            <p class="text-black/40 text-[10px] font-black uppercase tracking-widest mb-1">Repayment Term</p>
                            <p class="text-3xl font-black text-black leading-none">{{ $loanProduct->duration_months }} Mo</p>
                        </div>
                    </div>

                    <div class="mt-12">
                        <h3 class="font-black text-black text-xs uppercase tracking-[0.2em] opacity-40 mb-4">Description</h3>
                        <p class="text-black/80 font-bold leading-relaxed">{{ $loanProduct->description }}</p>
                    </div>

                    <!-- Apply Now Section -->
                    @auth
                        <div class="mt-12 pt-8 border-t border-black/5">
                            <a href="{{ route('loans.apply', $loanProduct) }}"
                               class="block w-full bg-black text-brand px-8 py-5 rounded-2xl hover:opacity-90 transition text-center font-black shadow-xl uppercase tracking-widest text-sm no-underline">
                                Initialize Application
                            </a>
                        </div>
                    @endauth

                    <!-- Admin Actions -->
                    @auth
                        @if(auth()->user()->isAdmin())
                            <div class="mt-16 flex space-x-4 border-t border-black/5 pt-8">
                                <a href="{{ route('admin.loan-products.edit', $loanProduct) }}"
                                   class="bg-black/10 text-black px-6 py-3 rounded-xl hover:bg-black/20 transition text-xs font-black uppercase tracking-widest no-underline">
                                    Edit Asset
                                </a>

                                <x-danger-button
                                    x-data=""
                                    x-on:click.prevent="$dispatch('open-modal', 'confirm-product-deletion')"
                                >
                                    {{ __('Purge Asset') }}
                                </x-danger-button>

                                <x-modal name="confirm-product-deletion" focusable>
                                    <form method="post" action="{{ route('admin.loan-products.destroy', $loanProduct) }}" class="p-12 bg-brand border-4 border-black">
                                        @csrf
                                        @method('delete')

                                        <h2 class="text-3xl font-black text-black uppercase tracking-tighter">
                                            {{ __('Delete Asset?') }}
                                        </h2>

                                        <p class="mt-4 text-sm font-bold text-black/60">
                                            {{ __('This action is permanent and will remove this product from the catalog. Confirm with your password.') }}
                                        </p>

                                        <div class="mt-8">
                                            <x-text-input id="password" name="password" type="password" class="block w-full" placeholder="{{ __('Password') }}" required />
                                        </div>

                                        <div class="mt-8 flex justify-end space-x-4">
                                            <x-secondary-button x-on:click="$dispatch('close')">
                                                {{ __('Cancel') }}
                                            </x-secondary-button>
                                            <x-danger-button>
                                                {{ __('Delete Now') }}
                                            </x-danger-button>
                                        </div>
                                    </form>
                                </x-modal>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>

        @auth
            @if(auth()->user()->isAdmin())
                <section class="bg-black/5 rounded-3xl p-10 border border-black/5">
                    <h2 class="text-xs font-black mb-8 text-black uppercase tracking-[0.3em] opacity-40">Asset Audit Trail (READ Detail)</h2>
                    <div class="space-y-4">
                        @foreach($loanProduct->audits()->with('user')->latest()->take(5)->get() as $audit)
                            <div class="flex items-center justify-between text-xs border-b border-black/5 pb-4">
                                <div>
                                    <span class="font-black uppercase">{{ $audit->user->name ?? 'System' }}</span>
                                    <span class="ml-2 font-bold opacity-60">{{ strtoupper($audit->event) }}</span>
                                    <div class="mt-2 font-mono text-[10px] opacity-40">
                                        @if($audit->event === 'updated')
                                            MODIFIED: {{ implode(', ', array_keys($audit->new_values)) }}
                                        @endif
                                    </div>
                                </div>
                                <span class="font-bold opacity-40">{{ $audit->created_at->diffForHumans() }}</span>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        @endauth
    </div>
@endsection
