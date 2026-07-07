@extends('layouts.app')

@section('title', 'User Details')

@section('header')
    <div class="flex items-center space-x-6">
        <a href="{{ route('admin.users.index') }}" class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-[#1A1A1A] hover:bg-[#FF6B00] hover:text-white transition-all duration-300 shadow-sm group">
            <svg class="h-5 w-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        </a>
        <div>
            <h1 class="text-4xl font-black text-[#1A1A1A] uppercase tracking-tighter">User Details</h1>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-12">
    {{-- User Profile Card --}}
    <div class="card p-10 bg-white shadow-xl rounded-[2rem] border-none">
        <div class="flex flex-col md:flex-row items-center md:items-start gap-10">
            <div class="h-32 w-32 rounded-[2rem] bg-[#FFEDD5] flex items-center justify-center border-4 border-[#FF6B00]/10 shrink-0">
                @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" class="h-full w-full object-cover rounded-[1.8rem]">
                @else
                    <span class="text-5xl font-black text-[#FF6B00]">{{ substr($user->name, 0, 1) }}</span>
                @endif
            </div>

            <div class="flex-1 space-y-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-3xl font-black text-[#1A1A1A] uppercase tracking-tighter">{{ $user->name }}</h2>
                        <p class="text-[#1A1A1A]/40 font-bold uppercase tracking-widest text-[10px] mt-1">{{ $user->email }}</p>
                    </div>
                    <div class="flex gap-4">
                        <button onclick="document.getElementById('capital-modal').classList.remove('hidden')" class="btn-primary px-6 py-3 text-[10px]">
                            Adjust Share Capital
                        </button>
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn-secondary px-6 py-3 text-[10px] no-underline">
                            Edit Identity
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 pt-8 border-t border-[#1A1A1A]/5">
                    <div>
                        <p class="text-[8px] font-black uppercase text-[#1A1A1A]/30 tracking-[0.2em] mb-1">Current Capital</p>
                        <p class="text-2xl font-black text-[#FF6B00]">₱{{ number_format($user->shareholder_capital) }}</p>
                    </div>
                    <div>
                        <p class="text-[8px] font-black uppercase text-[#1A1A1A]/30 tracking-[0.2em] mb-1">Max Capacity</p>
                        <p class="text-xl font-black text-[#1A1A1A]">₱{{ number_format($user->shareholder_capital * 10) }}</p>
                    </div>
                    <div>
                        <p class="text-[8px] font-black uppercase text-[#1A1A1A]/30 tracking-[0.2em] mb-1">Authority</p>
                        <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest border border-black/10">
                            {{ $user->role->name ?? 'User' }}
                        </span>
                    </div>
                    <div>
                        <p class="text-[8px] font-black uppercase text-[#1A1A1A]/30 tracking-[0.2em] mb-1">Integrity</p>
                        <span class="flex items-center text-[10px] font-black uppercase">
                            <span class="h-1.5 w-1.5 rounded-full mr-2 {{ $user->status == 'active' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                            {{ $user->status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Obligations / Loans Section --}}
    <div class="card overflow-hidden bg-white shadow-lg border-none rounded-[2rem]">
        <div class="px-10 py-6 border-b border-black/5 bg-[#FEF6F0]/50 flex justify-between items-center">
            <h3 class="text-xs font-black text-[#1A1A1A] uppercase tracking-[0.4em]">Asset Registry (Loans)</h3>
            <span class="text-[10px] font-black uppercase tracking-widest text-black/40">Entries: {{ $user->loans->count() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-[#1A1A1A] text-white uppercase text-[9px] font-black tracking-[0.2em]">
                    <tr>
                        <th class="px-10 py-5">Node ID</th>
                        <th class="px-6 py-5">Product Template</th>
                        <th class="px-6 py-5">Principal</th>
                        <th class="px-6 py-5">State</th>
                        <th class="px-6 py-5">Registry Date</th>
                        <th class="px-10 py-5 text-right">Directives</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @forelse($user->loans as $loan)
                    <tr class="group hover:bg-[#FEF6F0] transition-colors">
                        <td class="px-10 py-5 text-xs font-black text-[#FF6B00]">#{{ str_pad($loan->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-6 py-5 text-sm font-extrabold text-[#1A1A1A]">{{ $loan->loanProduct?->name }}</td>
                        <td class="px-6 py-5 text-sm font-black text-[#1A1A1A]">₱{{ number_format($loan->principal_amount) }}</td>
                        <td class="px-6 py-5">
                            <span class="px-3 py-1 text-[8px] font-black uppercase tracking-widest rounded-lg border
                                @if($loan->status == 'completed') border-green-500 text-green-600 bg-green-500/5
                                @elseif($loan->status == 'released') border-blue-500 text-blue-600 bg-blue-500/5
                                @elseif($loan->status == 'pending') border-[#FF6B00] text-[#FF6B00] bg-[#FF6B00]/5
                                @else border-red-500 text-red-600 bg-red-500/5
                                @endif">
                                {{ $loan->status }}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-xs font-bold text-black/40">{{ $loan->created_at->format('M d, Y') }}</td>
                        <td class="px-10 py-5 text-right">
                            <a href="{{ route('loans.show', $loan) }}" class="text-black font-black text-[9px] uppercase tracking-widest no-underline border-b-2 border-black hover:text-[#FF6B00] hover:border-[#FF6B00] transition">Inspect</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-10 py-20 text-center">
                            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-[#1A1A1A]/20 italic">No obligations registered to this identity matrix.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Capital Adjustment Modal --}}
<div id="capital-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-[#1A1A1A]/80 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-[2.5rem] p-10 max-w-md w-full shadow-2xl animate-in zoom-in duration-300">
            <h3 class="text-2xl font-black text-[#1A1A1A] uppercase tracking-tighter mb-2">Adjust Share Capital</h3>
            <p class="text-[#1A1A1A]/60 text-xs font-bold mb-8">Set the current shareholder capital for this identity node. 10x capacity will be recalculated.</p>

            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="name" value="{{ $user->name }}">
                <input type="hidden" name="email" value="{{ $user->email }}">
                <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                <input type="hidden" name="status" value="{{ $user->status }}">

                <div class="space-y-4">
                    <label class="block text-[10px] font-black uppercase text-[#1A1A1A]/40 tracking-widest">Shareholder Capital (PHP)</label>
                    <input type="number" name="shareholder_capital" value="{{ $user->shareholder_capital }}" step="0.01" required
                           class="w-full bg-[#FEF6F0] border-none rounded-2xl px-6 py-4 text-[#1A1A1A] font-black focus:ring-4 focus:ring-[#FF6B00]/10 text-lg">
                </div>

                <div class="flex gap-4 mt-10">
                    <button type="button" onclick="document.getElementById('capital-modal').classList.add('hidden')"
                            class="flex-1 px-6 py-4 text-[10px] font-black uppercase tracking-widest text-[#1A1A1A]/40 hover:text-black transition">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 btn-primary py-4">
                        Confirm Logic
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
