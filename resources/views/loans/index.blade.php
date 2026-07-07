@extends('layouts.app')

@section('title', 'My Loans')

@section('header')
    <h1 class="text-4xl font-black text-[#1A1A1A] uppercase tracking-tighter leading-none">My Loans</h1>
@endsection

@section('content')
    @if($loans->count() > 0)
        <div class="card overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-[#1A1A1A] text-white uppercase text-[10px] font-black tracking-[0.2em]">
                    <tr>
                        <th class="px-10 py-6">Loan #</th>
                        <th class="px-6 py-6">Product</th>
                        <th class="px-6 py-6">Principal</th>
                        <th class="px-6 py-6">Total Due</th>
                        <th class="px-6 py-6">Status</th>
                        <th class="px-10 py-6 text-right">Directives</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1A1A1A]/5">
                    @foreach($loans as $loan)
                    <tr class="group hover:bg-[#FEF6F0] transition-colors">
                        <td class="px-10 py-6 text-xs font-black text-[#FF6B00]">
                            #{{ $loan->id }}
                        </td>
                        <td class="px-6 py-6 text-sm font-extrabold text-[#1A1A1A]">
                            {{ $loan->loanProduct?->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-6 text-xs font-bold text-[#1A1A1A]/60">
                            PHP {{ number_format($loan->principal_amount, 2) }}
                        </td>
                        <td class="px-6 py-6 text-sm font-black text-[#FF6B00]">
                            PHP {{ number_format($loan->total_amount, 2) }}
                        </td>
                        <td class="px-6 py-6">
                            <span class="px-3 py-1 text-[8px] font-black uppercase tracking-widest rounded-lg border
                                @if($loan->status == 'completed') border-green-500 text-green-600 bg-green-500/5
                                @elseif($loan->status == 'released') border-blue-500 text-blue-600 bg-blue-500/5
                                @elseif($loan->status == 'pending') border-[#FF6B00] text-[#FF6B00] bg-[#FF6B00]/5
                                @else border-red-500 text-red-600 bg-red-500/5
                                @endif">
                                {{ $loan->status }}
                            </span>
                        </td>
                        <td class="px-10 py-6 text-right space-x-4">
                            <a href="{{ route('loans.show', $loan) }}"
                               class="text-[#1A1A1A] font-black text-[10px] uppercase tracking-widest no-underline border-b-2 border-[#1A1A1A] hover:text-[#FF6B00] hover:border-[#FF6B00] transition">
                                Inspect
                            </a>
                            <a href="{{ route('loans.invoice', $loan) }}"
                               class="text-[#1A1A1A]/40 font-black text-[8px] uppercase tracking-widest no-underline hover:text-[#1A1A1A] transition">
                                Invoice (PDF)
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-8">
            {{ $loans->links() }}
        </div>
    @else
        <div class="card p-32 text-center border-dashed border-2">
            <div class="w-24 h-24 bg-[#FEF6F0] rounded-[2rem] flex items-center justify-center mx-auto mb-8 text-[#1A1A1A]/5">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            </div>
            <h3 class="text-2xl font-black uppercase tracking-tight text-[#1A1A1A]/30 italic">No Active Obligations</h3>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-[#1A1A1A]/20 mt-4 max-w-sm mx-auto leading-relaxed">You haven't applied for any assets yet. Explore the catalog to initialize a node.</p>
            <a href="{{ route('loan_products.index') }}" class="btn-primary mt-12 no-underline">Browse Loan Management</a>
        </div>
    @endif
@endsection
