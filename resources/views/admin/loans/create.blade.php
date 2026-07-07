@extends('layouts.app')

@section('title', 'Initialize Loan')

@section('header')
    <div class="flex items-center space-x-6">
        <a href="{{ route('loan_products.index') }}" class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-[#1A1A1A] hover:bg-[#FF6B00] hover:text-white transition-all duration-300 shadow-sm group">
            <svg class="h-5 w-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        </a>
        <div>
            <h1 class="text-4xl font-black text-[#1A1A1A] uppercase tracking-tighter">Initialize Loan</h1>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto" x-data="{
    borrowerId: '',
    comaker1Id: '',
    comaker2Id: '',
    selectedProduct: null,
    selectedUser: null,
    amount: 0,
    products: {{ $loanProducts->toJson() }},
    users: {{ $users->toJson() }},

    get maxBorrowingCapacity() {
        if (!this.selectedUser || !this.selectedProduct) return 0;

        // Requirement: 10x Shareholder Capital
        const capitalLimit = Number(this.selectedUser.shareholder_capital) * 10;

        // System limit is the lesser of the Product Max or the Capital Limit
        return Math.min(this.selectedProduct.max_amount, capitalLimit);
    },

    get filteredComakers() {
        return this.users.filter(u => u.id != this.borrowerId);
    },

    updateBorrower(value) {
        const id = value.split(' - ')[0];
        this.borrowerId = id;
        this.selectedUser = this.users.find(u => u.id == id);
    },

    updateProduct(id) {
        this.selectedProduct = this.products.find(p => p.id == id);
        if (this.selectedProduct) {
            this.amount = this.selectedProduct.min_amount;
        }
    }
}">
    <div class="card p-12 bg-black text-white border-none shadow-2xl rounded-[3rem]">
        <form action="{{ route('admin.loans.store') }}" method="POST" class="space-y-10">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Borrower Selection -->
                <div class="space-y-4">
                    <label class="block text-[10px] font-black uppercase tracking-[0.3em] text-[#FF6B00]">Primary Borrower</label>
                    <div class="relative">
                        <input type="text" list="borrower-list"
                               placeholder="Search identities..."
                               class="w-full bg-[#1A1A1A] border-none rounded-2xl px-8 py-5 text-white font-bold focus:ring-4 focus:ring-[#FF6B00]/20"
                               @change="updateBorrower($event.target.value)">
                        <input type="hidden" name="user_id" :value="borrowerId">
                        <datalist id="borrower-list">
                            @foreach($users as $user)
                                <option value="{{ $user->id }} - {{ $user->name }} ({{ $user->email }})"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div x-show="selectedUser" class="mt-2 flex items-center space-x-2">
                        <span class="text-[8px] font-black uppercase text-white/40 tracking-widest">Shareholder Capital:</span>
                        <span class="text-[10px] font-black text-[#FF6B00]" x-text="'₱' + Number(selectedUser ? selectedUser.shareholder_capital : 0).toLocaleString()"></span>
                    </div>
                    <x-input-error :messages="$errors->get('user_id')" />
                </div>

                <!-- Asset Selection -->
                <div class="space-y-4">
                    <label class="block text-[10px] font-black uppercase tracking-[0.3em] text-[#FF6B00]">Asset Template</label>
                    <select name="loan_product_id"
                            @change="updateProduct($event.target.value)"
                            class="w-full bg-[#1A1A1A] border-none rounded-2xl px-8 py-5 text-white font-bold focus:ring-4 focus:ring-[#FF6B00]/20 appearance-none">
                        <option value="">Select Configuration</option>
                        @foreach($loanProducts as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->interest_rate }}%)</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('loan_product_id')" />
                </div>
            </div>

            <!-- Amount Slider -->
            <div class="space-y-6" x-show="selectedProduct && selectedUser">
                <div class="flex justify-between items-end">
                    <label class="block text-[10px] font-black uppercase tracking-[0.3em] text-[#FF6B00]">Requested Capital</label>
                    <div class="text-4xl font-black text-white">
                        ₱<span x-text="Number(amount).toLocaleString()"></span>
                    </div>
                </div>
                <input type="range" name="principal_amount"
                       :min="selectedProduct ? selectedProduct.min_amount : 0"
                       :max="maxBorrowingCapacity"
                       x-model="amount"
                       class="w-full h-3 bg-[#1A1A1A] rounded-lg appearance-none cursor-pointer accent-[#FF6B00]">
                <div class="flex justify-between text-[10px] font-black uppercase tracking-widest text-white/20">
                    <span x-text="'Min: ₱' + Number(selectedProduct ? selectedProduct.min_amount : 0).toLocaleString()"></span>
                    <span class="text-[#FF6B00]" x-text="'Max Capacity (10x Capital): ₱' + Number(maxBorrowingCapacity).toLocaleString()"></span>
                </div>
            </div>

            <!-- Purpose -->
            <div class="space-y-4">
                <label class="block text-[10px] font-black uppercase tracking-[0.3em] text-[#FF6B00]">Registry Purpose</label>
                <textarea name="purpose" rows="3"
                          placeholder="State the objective of this capital deployment..."
                          class="w-full bg-[#1A1A1A] border-none rounded-2xl px-8 py-5 text-white font-bold focus:ring-4 focus:ring-[#FF6B00]/20 placeholder-white/10">{{ old('purpose') }}</textarea>
                <x-input-error :messages="$errors->get('purpose')" />
            </div>

            <!-- Co-makers -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 pt-8 border-t border-white/5">
                <div class="space-y-4">
                    <label class="block text-[10px] font-black uppercase tracking-[0.3em] text-[#FF6B00]">Primary Co-maker</label>
                    <input type="text" list="comaker-list-1"
                           placeholder="Search secondary node..."
                           class="w-full bg-[#1A1A1A] border-none rounded-2xl px-8 py-5 text-white font-bold focus:ring-4 focus:ring-[#FF6B00]/20"
                           @change="comaker1Id = $event.target.value.split(' - ')[0]">
                    <input type="hidden" name="comaker_1_id" :value="comaker1Id">
                    <datalist id="comaker-list-1">
                        <template x-for="user in filteredComakers" :key="user.id">
                            <option :value="user.id + ' - ' + user.name"></option>
                        </template>
                    </datalist>
                </div>

                <div class="space-y-4">
                    <label class="block text-[10px] font-black uppercase tracking-[0.3em] text-[#FF6B00]">Secondary Co-maker</label>
                    <input type="text" list="comaker-list-2"
                           placeholder="Search tertiary node..."
                           class="w-full bg-[#1A1A1A] border-none rounded-2xl px-8 py-5 text-white font-bold focus:ring-4 focus:ring-[#FF6B00]/20"
                           @change="comaker2Id = $event.target.value.split(' - ')[0]">
                    <input type="hidden" name="comaker_2_id" :value="comaker2Id">
                    <datalist id="comaker-list-2">
                        <template x-for="user in filteredComakers" :key="user.id">
                            <option :value="user.id + ' - ' + user.name"></option>
                        </template>
                    </datalist>
                </div>
            </div>

            <div class="flex justify-end pt-12">
                <button type="submit" class="btn-primary px-16 py-6 rounded-3xl text-sm shadow-2xl shadow-[#FF6B00]/20">
                    Initialize Protocol
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
