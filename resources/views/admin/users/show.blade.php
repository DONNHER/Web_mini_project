@extends('layouts.app')

@section('title', 'User Details')

@section('header')
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.users.index') }}" class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-black hover:bg-[#FF6B00] hover:text-white transition-all shadow-sm group">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        </a>
        <h1 class="text-2xl font-black text-black uppercase tracking-tighter">Identity Details</h1>
    </div>
@endsection

@section('content')
<div class="space-y-6 text-black">
    {{-- User Profile Card --}}
    <div class="card p-6 bg-white border border-black/5 shadow-sm">
        <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
            <div class="h-24 w-24 rounded-2xl bg-[#FFEDD5] flex items-center justify-center border-2 border-[#FF6B00]/10 shrink-0">
                @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" class="h-full w-full object-cover rounded-2xl">
                @else
                    <span class="text-3xl font-black text-[#FF6B00]">{{ substr($user->name, 0, 1) }}</span>
                @endif
            </div>

            <div class="flex-1 space-y-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-xl font-black text-black uppercase tracking-tighter">{{ $user->name }}</h2>
                        <p class="text-black/40 font-bold uppercase tracking-widest text-[8px] mt-0.5">{{ $user->email }}</p>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="document.getElementById('capital-modal').classList.remove('hidden')" class="btn-primary px-4 py-2 text-[8px]">
                            Update Capital
                        </button>
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn-secondary px-4 py-2 text-[8px] no-underline">
                            Edit Node
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-6 border-t border-black/5">
                    <div>
                        <p class="text-[7px] font-black uppercase text-black/30 tracking-[0.2em] mb-1">Current Capital</p>
                        <p class="text-lg font-black text-[#FF6B00]">₱{{ number_format($user->shareholder_capital) }}</p>
                    </div>
                    <div>
                        <p class="text-[7px] font-black uppercase text-black/30 tracking-[0.2em] mb-1">10x Capacity</p>
                        <p class="text-lg font-black text-black">₱{{ number_format($user->shareholder_capital * 10) }}</p>
                    </div>
                    <div>
                        <p class="text-[7px] font-black uppercase text-black/30 tracking-[0.2em] mb-1">Authority</p>
                        <span class="px-2 py-0.5 rounded-full text-[7px] font-black uppercase tracking-widest border border-black/10">
                            {{ $user->role->name ?? 'User' }}
                        </span>
                    </div>
                    <div>
                        <p class="text-[7px] font-black uppercase text-black/30 tracking-[0.2em] mb-1">Integrity</p>
                        <span class="flex items-center text-[8px] font-black uppercase">
                            <span class="h-1 w-1 rounded-full mr-1 {{ $user->status == 'active' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                            {{ $user->status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Loans Section --}}
    <div class="card overflow-hidden bg-white border border-black/5 shadow-sm">
        <div class="px-6 py-3 border-b border-black/5 bg-[#FEF6F0]/30 flex justify-between items-center">
            <h3 class="text-[9px] font-black text-black uppercase tracking-[0.4em]">Obligation Registry</h3>
            <span class="text-[8px] font-black uppercase tracking-widest text-black/40">Registered: {{ $user->loans->count() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-black text-white uppercase text-[7px] font-black tracking-widest">
                    <tr>
                        <th class="px-6 py-3">Node</th>
                        <th class="px-4 py-3">Product</th>
                        <th class="px-4 py-3 text-right">Principal</th>
                        <th class="px-4 py-3">State</th>
                        <th class="px-6 py-3 text-right">Directives</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @forelse($user->loans as $loan)
                    <tr class="group hover:bg-[#FEF6F0] transition-colors">
                        <td class="px-6 py-3 text-[9px] font-black text-[#FF6B00]">#{{ str_pad($loan->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-4 py-3 text-[10px] font-black text-black uppercase">{{ $loan->loanProduct?->name }}</td>
                        <td class="px-4 py-3 text-right text-[10px] font-black text-black">₱{{ number_format($loan->principal_amount) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 text-[7px] font-black uppercase tracking-widest rounded border
                                @if($loan->status == 'completed') border-green-500 text-green-600 bg-green-500/5
                                @elseif($loan->status == 'released') border-blue-500 text-blue-600 bg-blue-500/5
                                @elseif($loan->status == 'pending') border-[#FF6B00] text-[#FF6B00] bg-[#FF6B00]/5
                                @else border-red-500 text-red-600 bg-red-500/5
                                @endif">
                                {{ $loan->status }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('loans.show', $loan) }}" class="text-black font-black text-[8px] uppercase tracking-widest border-b border-black hover:text-[#FF6B00] hover:border-[#FF6B00]">Inspect</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-black/20 text-[8px] font-black uppercase tracking-widest">No active obligations identified.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Capital Adjustment Modal --}}
<div id="capital-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl p-8 max-w-sm w-full shadow-2xl animate-in zoom-in duration-200">
            <h3 class="text-lg font-black text-black uppercase tracking-tighter mb-1">Adjust Capital</h3>
            <p class="text-black/40 text-[9px] font-bold mb-6 uppercase tracking-widest leading-relaxed">Update shareholder equity for this node. 10x capacity will recalibrate.</p>

            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="name" value="{{ $user->name }}">
                <input type="hidden" name="email" value="{{ $user->email }}">
                <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                <input type="hidden" name="status" value="{{ $user->status }}">

                <div class="space-y-2">
                    <label class="block text-[7px] font-black uppercase text-black/40 tracking-widest">Capital Amount (PHP)</label>
                    <input type="number" name="shareholder_capital" value="{{ $user->shareholder_capital }}" step="0.01" required
                           class="w-full bg-[#FEF6F0] border-none rounded-xl px-4 py-3 text-black font-black focus:ring-2 focus:ring-[#FF6B00]/10 text-base">
                </div>

                <div class="flex gap-3 mt-8">
                    <button type="button" onclick="document.getElementById('capital-modal').classList.add('hidden')"
                            class="flex-1 px-4 py-3 text-[8px] font-black uppercase tracking-widest text-black/40 hover:text-black transition">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 btn-primary py-3">
                        Confirm Logic
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
