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
    borrowerId: '',
    comaker1Id: '',
    comaker2Id: '',
    selectedUser: null,
    amount: 0,
    users: {{ $users->toJson() }},

    get maxCapacity() {
        if (!this.selectedUser) return 0;
        return Number(this.selectedUser.shareholder_capital) * 10;
    },

    get filteredComakers() {
        return this.users.filter(u => u.id != this.borrowerId);
    },

    updateBorrower(value) {
        const id = value.split(' - ')[0];
        this.borrowerId = id;
        this.selectedUser = this.users.find(u => u.id == id);
        this.amount = 0;
    },

    updateComaker1(value) {
        this.comaker1Id = value.split(' - ')[0];
    },

    updateComaker2(value) {
        this.comaker2Id = value.split(' - ')[0];
    },

    getDisplayString(user) {
        return `${user.id} - ${user.name} (${user.email})`;
    }
}">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Form --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="card p-6 bg-white border border-black/5 shadow-sm">
                <form action="{{ route('admin.loans.store') }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- Primary Borrower --}}
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-black/40 mb-2">Primary Borrower</label>
                        <input type="text" list="borrower-list"
                               placeholder="Search identities..."
                               class="w-full bg-[#FEF6F0] border-none rounded-xl px-6 py-3 text-black font-bold focus:ring-2 focus:ring-[#FF6B00]/5 text-xs"
                               @input="updateBorrower($event.target.value)">
                        <input type="hidden" name="user_id" :value="borrowerId">
                        <datalist id="borrower-list">
                            @foreach($users as $user)
                                <option value="{{ $user->id }} - {{ $user->name }} ({{ $user->email }})"></option>
                            @endforeach
                        </datalist>
                        <div x-show="selectedUser" class="mt-2 text-[10px] font-black uppercase text-[#FF6B00]">
                            Shareholder Capital: ₱<span x-text="Number(selectedUser ? selectedUser.shareholder_capital : 0).toLocaleString()"></span>
                        </div>
                        <x-input-error :messages="$errors->get('user_id')" />
                    </div>

                    {{-- Amount --}}
                    <div class="space-y-4" x-show="selectedUser">
                        <div class="flex justify-between items-end">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-black/40">Capital Deployment</label>
                            <p class="text-2xl font-black text-black">₱<span x-text="Number(amount).toLocaleString()"></span></p>
                        </div>
                        <input type="range" name="principal_amount" min="0" :max="maxCapacity" x-model="amount"
                               class="w-full h-2 bg-[#FEF6F0] rounded-lg appearance-none cursor-pointer accent-[#FF6B00]">
                        <div class="flex justify-between text-[8px] font-black uppercase tracking-widest text-black/20">
                            <span>Min: ₱0</span>
                            <span class="text-[#FF6B00]" x-text="'Max capacity: ₱' + Number(maxCapacity).toLocaleString()"></span>
                        </div>
                        <x-input-error :messages="$errors->get('principal_amount')" />
                    </div>

                    {{-- Purpose --}}
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-black/40 mb-2">Protocol Purpose</label>
                        <textarea name="purpose" rows="2" placeholder="deployment objective..."
                                  class="w-full bg-[#FEF6F0] border-none rounded-xl px-6 py-3 text-black font-bold focus:ring-2 focus:ring-[#FF6B00]/5 text-xs">{{ old('purpose') }}</textarea>
                        <x-input-error :messages="$errors->get('purpose')" />
                    </div>

                    {{-- Co-makers --}}
                    <div class="grid grid-cols-2 gap-6 pt-4 border-t border-black/5">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-black/40 mb-2">Primary Co-maker</label>
                            <input type="text" list="comaker-list-1"
                                   placeholder="Search..."
                                   class="w-full bg-[#FEF6F0] border-none rounded-xl px-6 py-3 text-black font-bold focus:ring-2 focus:ring-[#FF6B00]/5 text-xs"
                                   @input="updateComaker1($event.target.value)">
                            <input type="hidden" name="comaker_1_id" :value="comaker1Id">
                            <datalist id="comaker-list-1">
                                <template x-for="user in filteredComakers" :key="user.id">
                                    <option :value="getDisplayString(user)"></option>
                                </template>
                            </datalist>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-black/40 mb-2">Secondary Co-maker</label>
                            <input type="text" list="comaker-list-2"
                                   placeholder="Search..."
                                   class="w-full bg-[#FEF6F0] border-none rounded-xl px-6 py-3 text-black font-bold focus:ring-2 focus:ring-[#FF6B00]/5 text-xs"
                                   @input="updateComaker2($event.target.value)">
                            <input type="hidden" name="comaker_2_id" :value="comaker2Id">
                            <datalist id="comaker-list-2">
                                <template x-for="user in filteredComakers" :key="user.id">
                                    <option :value="getDisplayString(user)"></option>
                                </template>
                            </datalist>
                        </div>
                    </div>

                    <button type="submit" class="w-full btn-primary py-4 uppercase font-black tracking-widest text-[10px]">Initialize Protocol</button>
                </form>
            </div>
        </div>

        {{-- Shareholder List --}}
        <div class="space-y-4">
            <h2 class="text-xs font-black uppercase tracking-[0.3em] text-black">Registry Nodes</h2>
            <div class="card overflow-hidden bg-white border-none shadow-sm">
                <div class="max-h-[600px] overflow-y-auto">
                    <table class="w-full text-left">
                        <thead class="bg-black text-white text-[8px] font-black uppercase tracking-widest sticky top-0">
                            <tr>
                                <th class="px-4 py-3">Node</th>
                                <th class="px-4 py-3 text-right">Capital</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5">
                            @foreach($users->sortByDesc('shareholder_capital') as $user)
                                <tr class="group hover:bg-[#FEF6F0] transition-colors">
                                    <td class="px-4 py-3">
                                        <p class="text-[10px] font-black text-black leading-tight">{{ $user->name }}</p>
                                        <p class="text-[8px] text-black/40 font-bold uppercase tracking-tighter">#{{ $user->id }}</p>
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
