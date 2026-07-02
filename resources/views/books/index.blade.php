@extends('layouts.app')

@section('title', 'All Books - PageTurner')

@section('header')
    <h1 class="text-3xl font-bold text-white">All Books</h1>
@endsection

@section('content')
    <!-- Search and Filter -->
    <div class="bg-gray-800 p-6 rounded-lg shadow-lg mb-8 border border-gray-700">
        <form action="{{ route('books.index') }}" method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search by title or author..."
                       class="w-full bg-gray-700 border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400">
            </div>

            <div class="w-48">
                <select name="category" class="w-full bg-gray-700 border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-white">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-8 py-2 rounded-md hover:bg-blue-700 transition shadow-md font-semibold">
                Search
            </button>
        </form>
    </div>

    <!-- Books Grid -->
    @if($books->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($books as $book)
                <x-book-card :book="$book" />
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-10 pagination-dark">
            {{ $books->withQueryString()->links() }}
        </div>
    @else
        <div class="bg-gray-800 border-l-4 border-blue-500 p-4 text-gray-300 rounded shadow-md">
            <p>No books found matching your criteria.</p>
        </div>
    @endif
@endsection
