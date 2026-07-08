@extends('layouts.app')

@section('title', 'Loan Management')

@section('header')
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-black uppercase tracking-tighter leading-none">Loan Registry</h1>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.loans.create') }}" class="btn-primary px-6 no-underline">
                Initialize Loan
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6" x-data="{ showColumns: { borrower: true, principal: true, status: true, date: true } }">

    <!-- Filters -->
    <div class="card p-6">
        <form action="{{ route('admin.loans.index') }}" method="GET" id="filter-form" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-[8px] font-black uppercase tracking-[0.2em] text-black/40 mb-2">Omni Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Scan identities or Loan IDs..."
                           class="w-full bg-[#FEF6F0] border-none rounded-lg px-6 py-2 text-black font-bold focus:ring-2 focus:ring-[#FF6B00]/5 placeholder-black/20 text-xs">
                </div>

                <div>
                    <label class="block text-[8px] font-black uppercase tracking-[0.2em] text-black/40 mb-2">State Integrity</label>
                    <select name="status" class="w-full bg-[#FEF6F0] border-none rounded-lg px-6 py-2 text-black font-bold focus:ring-2 focus:ring-[#FF6B00]/5 text-[10px]">
                        <option value="">Matrix: All</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="disbursed" {{ request('status') == 'disbursed' ? 'selected' : '' }}>Disbursed</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="past due" {{ request('status') == 'past due' ? 'selected' : '' }}>Past Due</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[8px] font-black uppercase tracking-[0.2em] text-black/40 mb-2">Density</label>
                    <select name="per_page" class="w-full bg-[#FEF6F0] border-none rounded-lg px-6 py-2 text-black font-bold focus:ring-2 focus:ring-[#FF6B00]/5 text-[10px]">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 / Cycle</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 / Cycle</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / Cycle</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full btn-primary py-2.5">Apply Logic</button>
                </div>
            </div>

            <div class="flex justify-between items-center pt-4 border-t border-black/5">
                <div class="flex items-center space-x-4">
                    <span class="text-[8px] font-black uppercase tracking-[0.2em] text-black/40">Visible Nodes:</span>
                    <div class="flex space-x-1">
                        <template x-for="(visible, col) in showColumns">
                            <button type="button" @click="showColumns[col] = !showColumns[col]"
                                    :class="visible ? 'bg-black text-white' : 'bg-[#FEF6F0] text-black/40'"
                                    class="px-3 py-1.5 rounded-full text-[7px] font-black uppercase tracking-widest transition-all duration-300"
                                    x-text="col"></button>
                        </template>
                    </div>
                </div>
                <a href="{{ route('admin.loans.index') }}" class="text-[8px] font-black uppercase tracking-widest text-black/40 hover:text-[#FF6B00] no-underline">Reset Defaults</a>
            </div>
        </form>
    </div>

    <!-- Registry Table -->
    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b border-black/5 flex justify-between items-center bg-[#FEF6F0]/30">
            <h2 class="text-[8px] font-black uppercase tracking-widest text-black/40">Registry Stream</h2>
            <div class="text-[8px] font-black uppercase tracking-widest text-black/40">
                Total records: {{ $loans->total() }}
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-black text-white uppercase text-[8px] font-black tracking-widest">
                    <tr>
                        <th class="px-6 py-4">Node ID</th>
                        <th x-show="showColumns.borrower" class="px-4 py-4">Borrower</th>
                        <th x-show="showColumns.principal" class="px-4 py-4">Principal</th>
                        <th x-show="showColumns.status" class="px-4 py-4">State</th>
                        <th x-show="showColumns.date" class="px-4 py-4">Registry Date</th>
                        <th class="px-6 py-4 text-right">Directives</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @foreach($loans as $loan)
                    <tr class="group hover:bg-[#FEF6F0] transition-colors">
                        <td class="px-6 py-4 text-xs font-black text-[#FF6B00]">#{{ str_pad($loan->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td x-show="showColumns.borrower" class="px-4 py-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-black text-black uppercase">{{ $loan->user->name }}</span>
                                <span class="text-[8px] font-bold text-black/40">{{ $loan->user->email }}</span>
                            </div>
                        </td>
                        <td x-show="showColumns.principal" class="px-4 py-4 text-xs font-black text-black">₱{{ number_format($loan->principal_amount) }}</td>
                        <td x-show="showColumns.status" class="px-4 py-4">
                            <span class="px-2 py-0.5 text-[7px] font-black uppercase tracking-widest rounded border
                                @if($loan->status == 'completed' || $loan->status == 'disbursed' || $loan->status == 'active') border-green-500 text-green-600 bg-green-500/5
                                @elseif($loan->status == 'pending') border-[#FF6B00] text-[#FF6B00] bg-[#FF6B00]/5
                                @elseif($loan->status == 'past due' || $loan->status == 'overdue') border-red-500 text-red-600 bg-red-500/5
                                @else border-black/20 text-black/40
                                @endif">
                                {{ $loan->status }}
                            </span>
                        </td>
                        <td x-show="showColumns.date" class="px-4 py-4 text-[10px] font-bold text-black/40">{{ $loan->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right space-x-4">
                            <a href="{{ route('loans.show', $loan) }}" class="text-black font-black text-[8px] uppercase tracking-widest border-b border-black hover:text-[#FF6B00] hover:border-[#FF6B00]">Inspect</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Custom Pagination -->
    <div class="mt-8 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center space-x-2">
            {{ $loans->links('vendor.pagination.simple-registry') }}
        </div>

        <div class="flex items-center space-x-2">
            <span class="text-[8px] font-black uppercase tracking-widest text-black/40">Jump Logic:</span>
            <a href="{{ $loans->url($loans->currentPage() + 1) }}" class="px-3 py-1.5 bg-[#FEF6F0] rounded-lg text-[7px] font-black uppercase text-black hover:bg-black hover:text-white transition no-underline">Next Node</a>
            <a href="{{ $loans->url(min($loans->lastPage(), $loans->currentPage() + 10)) }}" class="px-3 py-1.5 bg-[#FEF6F0] rounded-lg text-[7px] font-black uppercase text-black hover:bg-black hover:text-white transition no-underline">Next 10x</a>
            <a href="{{ $loans->url(min($loans->lastPage(), $loans->currentPage() + 50)) }}" class="px-3 py-1.5 bg-[#FEF6F0] rounded-lg text-[7px] font-black uppercase text-black hover:bg-black hover:text-white transition no-underline">Next 50x</a>
        </div>
    </div>
</div>
@endsection
