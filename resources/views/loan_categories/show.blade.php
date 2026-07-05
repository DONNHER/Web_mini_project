@extends('layouts.app')

@section('title', $category->name . ' - PageTurner')

@section('header')
    <div class="flex flex-col">
        <h1 class="text-4xl font-extrabold text-white tracking-tight">{{ $category->name }}</h1>
        <p class="text-gray-400 mt-3 text-lg leading-relaxed max-w-3xl">{{ $category->description }}</p>
    </div>
@endsection

@section('content')
    <div class="mb-8 flex items-center space-x-2">
        <span class="bg-blue-600/20 text-blue-400 px-3 py-1 rounded-full text-sm font-bold border border-blue-500/30">
            {{ $category->books->count() }} books
        </span>
        <span class="text-gray-500 text-sm">available in this category</span>
    </div>

    @if($books->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($books as $book)
                <x-book-card :book="$book" />
            @endforeach
        </div>

        <div class="mt-10 pagination-dark">
            {{ $books->links() }}
        </div>
    @else
        <div class="bg-gray-800 border-l-4 border-blue-500 p-6 text-gray-300 rounded shadow-lg">
            <div class="flex items-center">
                <svg class="h-6 w-6 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="font-medium">No books found in this category at the moment. Please check back later.</p>
            </div>
        </div>
    @endif
@endsection
