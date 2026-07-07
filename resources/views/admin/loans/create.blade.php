@extends('layouts.app')

@section('title', 'Initialize Loan')

@section('header')
    <div class="flex items-center space-x-4">
        <a href="{{ route('loan_products.index') }}" class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-black hover:bg-[#FF6B00] hover:text-white transition-all shadow-sm group">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        </a>
        <h1 class="text-2xl font-black text-black uppercase tracking-tighter leading-none">Initialize Loan</h1>
    </div>
@endsection

@section('content')
<div class="space-y-6" x-data="{
    borrower: null,
    comaker1: null,
    comaker2: null,
    amount: 0,
    searchBorrower: '',
    searchComaker1: '',
    searchComaker2: '',
    users: {{ $users->toJson() }},

    get maxCapacity() {
        return this.borrower ? Number(this.borrower.shareholder_capital) * 10 : 0;
    },

    filteredUsers(query, excludeId = null) {
        if (!query) return [];
        return this.users.filter(u =>
            (u.name.toLowerCase().includes(query.toLowerCase()) || u.email.toLowerCase().includes(query.toLowerCase()) || u.id.toString() == query) &&
            u.id != excludeId
        ).slice(0, 5);
    }
}">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Form --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="card p-6 bg-white border border-black/5 shadow-sm">
                <form action="{{ route('admin.loans.store') }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- Borrower Search --}}
                    <div class="relative">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-black/40 mb-2">Search Borrower</label>
                        <input type="text" x-model="searchBorrower"
                               placeholder="Type name, ID or email..."
                               class="w-full bg-[#FEF6F0] border-none rounded-xl px-6 py-3 text-black font-bold focus:ring-2 focus:ring-[#FF6B00]/10 text-xs">

                        <div x-show="searchBorrower && !borrower" class="absolute z-50 w-full mt-1 bg-white border border-black/5 rounded-xl shadow-xl overflow-hidden">
                            <template x-for="user in filteredUsers(searchBorrower)" :key="user.id">
                                <button type="button" @click="borrower = user; searchBorrower = user.name; amount = 0"
                                        class="w-full text-left px-6 py-3 hover:bg-[#FEF6F0] text-xs font-bold text-black border-b border-black/5 last:border-none transition">
                                    <span x-text="user.name"></span> <span class="text-black/40 ml-2" x-text="'#' + user.id"></span>
                                </button>
                            </template>
                        </div>

                        <template x-if="borrower">
                            <div class="mt-2 p-4 bg-[#FEF6F0] rounded-xl flex justify-between items-center border border-[#FF6B00]/10">
                                <div>
                                    <p class="text-[10px] font-black uppercase text-black" x-text="borrower.name"></p>
                                    <p class="text-[8px] font-bold text-black/40 uppercase tracking-widest">Shareholder Capital: <span class="text-[#FF6B00]" x-text="'₱' + Number(borrower.shareholder_capital).toLocaleString()"></span></p>
                                </div>
                                <button type="button" @click="borrower = null; searchBorrower = ''; amount = 0" class="text-red-500 hover:text-red-700 transition">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                        </template>
                        <input type="hidden" name="user_id" :value="borrower ? borrower.id : ''">
                    </div>

                    {{-- Amount Slider --}}
                    <div class="space-y-4" x-show="borrower">
                        <div class="flex justify-between items-end">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-black/40">Capital Deployment</label>
                            <p class="text-2xl font-black text-black">₱<span x-text="Number(amount).toLocaleString()"></span></p>
                        </div>
                        <input type="range" name="principal_amount" min="0" :max="maxCapacity" x-model="amount"
                               class="w-full h-2 bg-[#FEF6F0] rounded-lg appearance-none cursor-pointer accent-[#FF6B00]">
                        <div class="flex justify-between text-[8px] font-black uppercase tracking-widest text-black/20">
                            <span>Min: ₱0</span>
                            <span class="text-[#FF6B00]" x-text="'10x Capacity: ₱' + Number(maxCapacity).toLocaleString()"></span>
                        </div>
                    </div>

                    {{-- Purpose --}}
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-black/40 mb-2">Protocol Purpose</label>
                        <textarea name="purpose" rows="2" placeholder="deployment objective..."
                                  class="w-full bg-[#FEF6F0] border-none rounded-xl px-6 py-3 text-black font-bold focus:ring-2 focus:ring-[#FF6B00]/10 text-xs"></textarea>
                    </div>

                    {{-- Co-makers --}}
                    <div class="grid grid-cols-2 gap-6 pt-4 border-t border-black/5">
                        <div class="relative">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-black/40 mb-2">Primary Co-maker</label>
                            <input type="text" x-model="searchComaker1" placeholder="Search..."
                                   class="w-full bg-[#FEF6F0] border-none rounded-xl px-6 py-3 text-black font-bold focus:ring-2 focus:ring-[#FF6B00]/10 text-xs">
                            <div x-show="searchComaker1 && !comaker1" class="absolute z-50 w-full mt-1 bg-white border border-black/5 rounded-xl shadow-xl">
                                <template x-for="user in filteredUsers(searchComaker1, borrower ? borrower.id : null)" :key="user.id">
                                    <button type="button" @click="comaker1 = user; searchComaker1 = user.name"
                                            class="w-full text-left px-4 py-2 hover:bg-[#FEF6F0] text-[10px] font-bold text-black border-b border-black/5 last:border-none transition">
                                        <span x-text="user.name"></span>
                                    </button>
                                </template>
                            </div>
                            <template x-if="comaker1">
                                <div class="mt-2 flex justify-between items-center text-[10px] font-black text-[#FF6B00]">
                                    <span x-text="comaker1.name"></span>
                                    <button type="button" @click="comaker1 = null; searchComaker1 = ''" class="text-black/20 hover:text-black">×</button>
                                </div>
                            </template>
                            <input type="hidden" name="comaker_1_id" :value="comaker1 ? comaker1.id : ''">
                        </div>

                        <div class="relative">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-black/40 mb-2">Secondary Co-maker</label>
                            <input type="text" x-model="searchComaker2" placeholder="Search..."
                                   class="w-full bg-[#FEF6F0] border-none rounded-xl px-6 py-3 text-black font-bold focus:ring-2 focus:ring-[#FF6B00]/10 text-xs">
                            <div x-show="searchComaker2 && !comaker2" class="absolute z-50 w-full mt-1 bg-white border border-black/5 rounded-xl shadow-xl">
                                <template x-for="user in filteredUsers(searchComaker2, borrower ? borrower.id : null)" :key="user.id">
                                    <button type="button" @click="comaker2 = user; searchComaker2 = user.name"
                                            class="w-full text-left px-4 py-2 hover:bg-[#FEF6F0] text-[10px] font-bold text-black border-b border-black/5 last:border-none transition">
                                        <span x-text="user.name"></span>
                                    </button>
                                </template>
                            </div>
                            <template x-if="comaker2">
                                <div class="mt-2 flex justify-between items-center text-[10px] font-black text-[#FF6B00]">
                                    <span x-text="comaker2.name"></span>
                                    <button type="button" @click="comaker2 = null; searchComaker2 = ''" class="text-black/20 hover:text-black">×</button>
                                </div>
                            </template>
                            <input type="hidden" name="comaker_2_id" :value="comaker2 ? comaker2.id : ''">
                        </div>
                    </div>

                    <button type="submit" class="w-full btn-primary py-4">Initialize Registry Entry</button>
                </form>
            </div>
        </div>

        {{-- Right: Shareholder List --}}
        <div class="space-y-4">
            <h2 class="text-xs font-black uppercase tracking-[0.3em] text-black">Shareholders Registry</h2>
            <div class="card overflow-hidden bg-white border-none shadow-sm">
                <div class="max-h-[500px] overflow-y-auto">
                    <table class="w-full text-left">
                        <thead class="bg-black text-white text-[8px] font-black uppercase tracking-widest sticky top-0">
                            <tr>
                                <th class="px-4 py-3">Identity</th>
                                <th class="px-4 py-3 text-right">Capital</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5">
                            @foreach($users->sortByDesc('shareholder_capital') as $user)
                                <tr class="hover:bg-[#FEF6F0] transition-colors cursor-pointer" @click="borrower = {{ $user->toJson() }}; searchBorrower = '{{ $user->name }}'; amount = 0">
                                    <td class="px-4 py-3">
                                        <p class="text-[10px] font-black text-black leading-tight">{{ $user->name }}</p>
                                        <p class="text-[8px] text-black/40 font-bold uppercase tracking-tighter">{{ $user->email }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <p class="text-[10px] font-black text-[#FF6B00]">₱{{ number_format($user->shareholder_capital) }}</p>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
