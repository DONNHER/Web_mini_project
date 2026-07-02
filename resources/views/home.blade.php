@extends('layouts.app')

@section('title', 'PageTurner - Online Bookstore')

@section('content')
    <!-- Hero Section -->
    <div class="bg-gray-800 text-white rounded-lg p-8 mb-8 border border-gray-700 shadow-xl">
        <h1 class="text-4xl font-bold mb-4">Welcome to <span class="text-blue-400">PageTurner</span></h1>
        <p class="text-xl text-gray-400 mb-6">Discover your next favorite book from our extensive collection.</p>
        <a href="{{ route('books.index') }}"
           class="bg-blue-400 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-500 transition shadow-lg inline-block">
            Browse Books
        </a>
    </div>

    <!-- Categories Section -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-6 text-white">Browse by Category</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($categories as $category)
                <a href="{{ route('categories.show', $category) }}"
                   class="bg-gray-800 p-6 rounded-lg shadow-lg border border-gray-700 hover:border-blue-400 transition text-center group">
                    <h3 class="font-semibold text-white group-hover:text-blue-400 transition">{{ $category->name }}</h3>
                    <p class="text-sm text-gray-400">{{ $category->books_count }} books</p>
                </a>
            @endforeach
        </div>
    </section>

    <!-- Featured Books Section -->
    <section>
        <h2 class="text-2xl font-bold mb-6 text-white">Featured Books</h2>

        @forelse($featuredBooks->chunk(4) as $chunk)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-6">
                @foreach($chunk as $book)
                    <x-book-card :book="$book" />
                @endforeach
            </div>
        @empty
            <div class="bg-gray-800 border-l-4 border-blue-400 p-4 text-gray-300">
                <p>No books available at the moment. Check back soon!</p>
            </div>
        @endforelse
    </section>
@endsection
