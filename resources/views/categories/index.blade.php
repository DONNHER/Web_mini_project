@extends('layouts.app')

@section('title', 'Categories - PageTurner')

@section('header')
    <h1 class="text-3xl font-bold text-white">Book Categories</h1>
@endsection

@section('content')
    @auth
        @if(auth()->user()->isAdmin())
            <div class="mb-8">
                <a href="{{ route('admin.categories.create') }}"
                   class="bg-white text-black px-6 py-3 rounded-md hover:bg-gray-200 transition shadow-lg font-black uppercase tracking-widest text-sm inline-block">
                    Add New Category
                </a>
            </div>
        @endif
    @endauth

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($categories as $category)
            <div class="bg-gray-800 rounded-lg shadow-xl overflow-hidden border border-gray-700 hover:border-blue-500 transition-all group">
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-white mb-3">
                        <a href="{{ route('categories.show', $category) }}" class="group-hover:text-blue-400 transition">
                            {{ $category->name }}
                        </a>
                    </h2>
                    <p class="text-gray-400 mb-6 leading-relaxed">{{ Str::limit($category->description, 100) }}</p>
                    <div class="flex justify-between items-center border-t border-gray-700 pt-6">
                        <span class="text-blue-400 font-bold tracking-tight">{{ $category->books_count }} books</span>
                        <a href="{{ route('categories.show', $category) }}"
                           class="bg-gray-900 text-blue-400 px-4 py-2 rounded-md hover:bg-blue-600 hover:text-white transition font-bold text-sm uppercase tracking-tighter">
                            Browse Books →
                        </a>
                    </div>

                    @auth
                        @if(auth()->user()->isAdmin())
                            <div class="mt-6 pt-4 border-t border-gray-700 flex space-x-4">
                                <a href="{{ route('admin.categories.edit', $category) }}"
                                   class="text-yellow-500 hover:text-yellow-400 text-xs font-bold uppercase tracking-widest">
                                    Edit
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}"
                                      method="POST"
                                      onsubmit="return confirm('Are you sure you want to delete this category?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-400 text-xs font-bold uppercase tracking-widest bg-transparent p-0 border-none shadow-none">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-10 pagination-dark">
        {{ $categories->links() }}
    </div>
@endsection
