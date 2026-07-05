@extends('layouts.app')

@section('title', 'Loan Application - LendingSystem')

@section('header')
    <h1 class="text-3xl font-black text-black uppercase tracking-tighter">Initialize Application</h1>
@endsection

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
        <div class="md:col-span-2">
            <div class="bg-black text-brand rounded-3xl p-10 border border-black shadow-xl">
                <h2 class="text-xs font-black uppercase tracking-[0.3em] mb-8 opacity-40">Asset Summary</h2>

                <div class="space-y-8">
                    <div>
                        <h3 class="text-4xl font-black text-white uppercase tracking-tighter">{{ $loanProduct->name }}</h3>
                        <p class="text-white/60 font-bold mt-4 leading-relaxed italic">"{{ $loanProduct->description }}"</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-8 border-t border-brand/10">
                        <div class="bg-brand/5 p-6 rounded-2xl border border-brand/10">
                            <p class="text-brand/40 text-[10px] font-black uppercase tracking-widest mb-1">Interest Rate</p>
                            <p class="text-2xl font-black text-white">{{ $loanProduct->interest_rate }}%</p>
                        </div>
                        <div class="bg-brand/5 p-6 rounded-2xl border border-brand/10">
                            <p class="text-brand/40 text-[10px] font-black uppercase tracking-widest mb-1">Max Duration</p>
                            <p class="text-2xl font-black text-white">{{ $loanProduct->duration_months }} Months</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="md:col-span-1">
            <div class="bg-black/5 rounded-3xl p-10 border border-black/5 shadow-sm">
                <h2 class="text-xs font-black text-black uppercase tracking-[0.3em] mb-8 opacity-40">Application Form</h2>

                <form id="loan-apply-form" action="{{ route('loans.store') }}" method="POST" class="space-y-8 dirty-check">
                    @csrf
                    <input type="hidden" name="loan_product_id" value="{{ $loanProduct->id }}">

                    <div>
                        <x-input-label for="principal_amount" :value="__('Requested Amount (PHP)')" :required="true" />
                        <input type="number"
                               name="principal_amount"
                               id="principal_amount"
                               step="0.01"
                               min="{{ $loanProduct->min_amount }}"
                               max="{{ $loanProduct->max_amount }}"
                               value="{{ old('principal_amount', $loanProduct->min_amount) }}"
                               class="w-full bg-white/20 border-black/10 rounded-xl px-4 py-3 text-black font-bold focus:ring-black mt-2"
                               required>
                        <p class="text-[10px] text-black/40 mt-2 font-black uppercase tracking-tighter">Min: {{ number_format($loanProduct->min_amount) }} | Max: {{ number_format($loanProduct->max_amount) }}</p>
                        <x-input-error :messages="$errors->get('principal_amount')" />
                    </div>

                    <div>
                        <x-input-label for="payment_method" :value="__('Disbursement Method')" :required="true" />
                        <select name="payment_method" id="payment_method" class="w-full bg-white/20 border-black/10 rounded-xl px-4 py-3 text-black font-bold focus:ring-black mt-2" required>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="G-Cash">G-Cash</option>
                            <option value="Over-the-counter">Over-the-counter</option>
                        </select>
                        <x-input-error :messages="$errors->get('payment_method')" />
                    </div>

                    <div>
                        <x-input-label for="purpose" :value="__('Loan Purpose')" :required="true" />
                        <textarea name="purpose"
                                  id="purpose"
                                  rows="3"
                                  placeholder="Describe what you will use this loan for..."
                                  class="w-full bg-white/20 border-black/10 rounded-xl px-4 py-3 text-black font-bold focus:ring-black mt-2"
                                  required>{{ old('purpose') }}</textarea>
                        <p class="text-[10px] text-black/40 mt-2 font-black uppercase tracking-tighter">AI will automatically categorize your purpose for faster processing.</p>
                        <x-input-error :messages="$errors->get('purpose')" />
                    </div>

                    <div>
                        <x-input-label for="comaker_id" :value="__('Co-maker (Guarantor)')" />
                        <select name="comaker_id"
                                id="comaker_id"
                                class="w-full bg-white/20 border-black/10 rounded-xl px-4 py-3 text-black font-bold focus:ring-black mt-2">
                            <option value="">None / Optional</option>
                            @foreach($comakers as $comaker)
                                <option value="{{ $comaker->id }}" {{ old('comaker_id') == $comaker->id ? 'selected' : '' }}>
                                    {{ $comaker->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-black/40 mt-2 font-black uppercase tracking-tighter">Required for amounts over PHP 100,000</p>
                        <x-input-error :messages="$errors->get('comaker_id')" />
                    </div>

                    <button type="submit"
                            class="w-full bg-black text-brand font-black py-5 rounded-2xl hover:opacity-90 transition uppercase tracking-widest text-sm shadow-xl flex items-center justify-center space-x-2">
                        <span>Execute Application</span>
                        <svg class="loading-spinner hidden animate-spin h-5 w-5 text-brand" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Auto-save draft functionality (Requirement 12.3)
    const form = document.getElementById('loan-apply-form');
    const storageKey = 'loan_draft_{{ $loanProduct->id }}';

    // Load draft
    window.addEventListener('load', () => {
        const draft = JSON.parse(localStorage.getItem(storageKey));
        if (draft) {
            Object.keys(draft).forEach(key => {
                const input = form.elements[key];
                if (input) input.value = draft[key];
            });
            showToast('Draft Loaded', 'Recovered your unsaved application data.', 'info');
        }
    });

    // Save on change
    form.addEventListener('input', () => {
        const formData = new FormData(form);
        const data = {};
        formData.forEach((value, key) => {
            if (key !== '_token') data[key] = value;
        });
        localStorage.setItem(storageKey, JSON.stringify(data));
    });

    // Clear on submit
    form.addEventListener('submit', () => {
        localStorage.removeItem(storageKey);
        form.classList.remove('dirty-check'); // Disable unsaved changes warning
    });
</script>
@endpush
