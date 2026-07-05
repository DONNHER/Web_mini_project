@extends('layouts.app')

@section('title', 'My Loans - LendingSystem')

@section('header')
    <h1 class="text-3xl font-bold text-white tracking-tight">My Loans</h1>
@endsection

@section('content')
    @if($loans->count() > 0)
        <div class="bg-gray-800 rounded-lg shadow-xl overflow-hidden border border-gray-700">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-900">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Loan #</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Product</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Principal</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Total Due</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @foreach($loans as $loan)
                    <tr class="hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 text-sm font-bold text-white">
                            #{{ $loan->id }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-300">
                            {{ $loan->loanProduct?->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-400">
                            PHP {{ number_format($loan->principal_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 text-sm font-black text-blue-400">
                            PHP {{ number_format($loan->total_amount, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 inline-flex text-[10px] font-black uppercase tracking-tighter rounded-full border
                                @if($loan->status == 'completed') bg-green-900/40 text-green-400 border-green-500/30
                                @elseif($loan->status == 'released') bg-blue-900/40 text-blue-400 border-blue-400/30
                                @elseif($loan->status == 'pending') bg-yellow-900/40 text-yellow-400 border-yellow-500/30
                                @elseif($loan->status == 'flagged') bg-red-900/40 text-red-400 border-red-500/30
                                @elseif($loan->status == 'rejected') bg-red-900/40 text-red-400 border-red-500/30
                                @endif">
                                {{ ucfirst($loan->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex flex-col space-y-1">
                                <a href="{{ route('loans.show', $loan) }}"
                                   class="text-blue-400 hover:text-blue-300 font-bold uppercase text-[10px] tracking-wider">
                                    View Details
                                </a>
                                <a href="{{ route('loans.invoice', $loan) }}"
                                   class="text-gray-400 hover:text-white font-bold uppercase text-[8px] tracking-widest">
                                    Invoice (PDF)
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-8 pagination-dark">
            {{ $loans->links() }}
        </div>
    @else
        <div class="bg-gray-800 border-l-4 border-blue-500 p-8 text-center rounded-lg shadow-xl">
            <svg class="h-16 w-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <p class="text-xl text-gray-300 mb-6">You haven't applied for any loans yet.</p>
            <a href="{{ route('loan_products.index') }}" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-md font-bold uppercase tracking-widest hover:bg-blue-700 transition">
                Browse Loan Products
            </a>
        </div>
    @endif
@endsection
