@extends('layouts.app')

@section('title', 'Loan Categories - LendingSystem')

@section('header')
    <h1 class="text-3xl font-black text-black uppercase tracking-tighter">Loan Categories</h1>
@endsection

@section('content')
    @auth
        @if(auth()->user()->isAdmin())
            <div class="mb-12">
                <a href="{{ route('admin.loan-categories.create') }}"
                   class="bg-black text-brand px-8 py-4 rounded-xl hover:opacity-80 transition shadow-xl font-black uppercase tracking-widest text-xs inline-block no-underline">
                    Add New Category
                </a>
            </div>
        @endif
    @endauth

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($categories as $category)
            <div class="bg-black/5 rounded-3xl p-10 border border-black/5 hover:border-black/20 transition group relative">
                <h2 class="text-2xl font-black text-black mb-4 uppercase tracking-tight">
                    <a href="{{ route('loan_products.index', ['category' => $category->id]) }}" class="no-underline hover:text-white transition">
                        {{ $category->name }}
                    </a>
                </h2>
                <p class="text-black/60 font-bold text-sm mb-8 leading-relaxed">{{ Str::limit($category->description, 100) }}</p>

                <div class="flex justify-between items-center border-t border-black/10 pt-8">
                    <span class="text-[10px] font-black uppercase tracking-widest text-black/40">{{ $category->loan_products_count }} products</span>
                    <a href="{{ route('loan_products.index', ['category' => $category->id]) }}"
                       class="text-black font-black text-[10px] uppercase tracking-[0.2em] no-underline border-b-2 border-black pb-1 hover:opacity-60 transition">
                        Browse →
                    </a>
                </div>

                @auth
                    @if(auth()->user()->isAdmin())
                        <div class="mt-8 pt-6 border-t border-black/10 flex space-x-6">
                            <a href="{{ route('admin.loan-categories.edit', $category) }}"
                               class="text-black/40 hover:text-black text-[10px] font-black uppercase tracking-widest no-underline transition">
                                Edit
                            </a>

                            <button type="button"
                                    onclick="confirmDelete('{{ $category->id }}', '{{ $category->name }}', '{{ $category->loan_products_count }}')"
                                    class="text-red-600/40 hover:text-red-600 text-[10px] font-black uppercase tracking-widest bg-transparent p-0 border-none shadow-none cursor-pointer transition">
                                Delete
                            </button>

                            <form id="delete-form-{{ $category->id }}"
                                  action="{{ route('admin.loan-categories.destroy', $category) }}"
                                  method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                                <input type="password" name="password" id="confirm-pass-{{ $category->id }}">
                            </form>
                        </div>
                    @endif
                @endauth
            </div>
        @endforeach
    </div>

    <div class="mt-16">
        {{ $categories->links() }}
    </div>
@endsection

@push('scripts')
<script>
    function confirmDelete(id, name, count) {
        let warning = `CASCADE DELETE WARNING:\n\nDeleting the category "${name}" will affect ${count} associated products.\n\n`;
        if (confirm(warning + "Are you sure you want to proceed?")) {
            let password = prompt("Please enter your password to confirm deletion:");
            if (password) {
                const form = document.getElementById('delete-form-' + id);
                document.getElementById('confirm-pass-' + id).value = password;
                form.submit();
            }
        }
    }
</script>
@endpush
