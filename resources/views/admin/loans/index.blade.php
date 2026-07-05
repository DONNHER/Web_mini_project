@extends('layouts.app')

@section('title', 'Loan Administration - LendingSystem')

@section('header')
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-black text-white uppercase tracking-tighter">Global <span class="text-white/40">Portfolio</span></h1>
        <div class="flex space-x-2">
            <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition shadow-xl">
                System Audit
            </a>
        </div>
    </div>
@endsection

@section('content')
    <!-- Statistics Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="bg-black/5 rounded-3xl p-8 border border-black/5 shadow-sm">
            <p class="text-black/40 text-[10px] font-black uppercase tracking-[0.2em] mb-4">Portfolio Size</p>
            <p class="text-4xl font-black text-black">{{ $loans->total() }}</p>
        </div>
        <div class="bg-black/5 rounded-3xl p-8 border border-black/5 shadow-sm">
            <p class="text-black/40 text-[10px] font-black uppercase tracking-[0.2em] mb-4">AI Categorized</p>
            <p class="text-4xl font-black text-blue-600">{{ $loans->whereNotNull('ai_tag')->count() }}</p>
        </div>
    </div>

    <!-- Active Applications -->
    <div class="bg-black/5 rounded-3xl p-10 border border-black/5 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="text-black/30 uppercase text-[10px] font-black tracking-widest border-b border-black/10">
                        <th class="text-left py-4">ID</th>
                        <th class="text-left py-4">Borrower</th>
                        <th class="text-left py-4">AI Intent Tag</th>
                        <th class="text-left py-4">Principal</th>
                        <th class="text-left py-4">Status Control</th>
                        <th class="text-right py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @foreach($loans as $loan)
                    <tr class="group">
                        <td class="py-6 text-sm font-black text-black">#{{ $loan->id }}</td>
                        <td class="py-6">
                            <p class="text-sm font-black text-black">{{ $loan->user->name }}</p>
                            <p class="text-[10px] font-bold text-black/40">{{ $loan->user->email }}</p>
                        </td>
                        <td class="py-6">
                            @if($loan->ai_tag)
                                <span class="bg-blue-600/10 text-blue-600 border border-blue-600/20 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter">
                                    {{ $loan->ai_tag }}
                                </a>
                            @else
                                <span class="text-black/20 text-[10px] font-black uppercase tracking-widest">Manual</span>
                            @endif
                        </td>
                        <td class="py-6 text-sm font-black text-black">₱{{ number_format($loan->principal_amount) }}</td>
                        <td class="py-6">
                            <form action="{{ route('admin.loans.status', $loan) }}" method="POST" class="flex items-center space-x-2">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="bg-white/50 border-black/5 rounded-lg text-[10px] font-black uppercase tracking-widest py-1 focus:ring-black">
                                    <option value="pending" {{ $loan->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ $loan->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="released" {{ $loan->status == 'released' ? 'selected' : '' }}>Released</option>
                                    <option value="rejected" {{ $loan->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="completed" {{ $loan->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                                <button type="submit" class="bg-black text-brand px-4 py-1 rounded-lg font-black text-[8px] uppercase tracking-widest">OK</button>
                            </form>
                        </td>
                        <td class="py-6 text-right">
                            <a href="{{ route('loans.show', $loan) }}" class="text-black hover:opacity-60 transition font-black text-[10px] uppercase tracking-widest no-underline border-b-2 border-black">
                                Inspect
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-8">
        {{ $loans->links() }}
    </div>
@endsection
