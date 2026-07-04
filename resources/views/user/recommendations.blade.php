@extends('layouts.app')

@section('title', 'Your Personalized Recommendations')

@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-blue-400 leading-tight">
            ✨ AI-Powered Recommendations
        </h2>
        <div class="flex items-center space-x-2 text-xs text-gray-500 bg-gray-900 px-3 py-1 rounded-full border border-gray-800">
            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
            <span>Real-time Behavioral Analysis Active</span>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-8">
    <!-- AI Intro Card -->
    <div class="bg-gradient-to-r from-blue-900 to-black p-6 rounded-xl border border-blue-800 shadow-2xl">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0">
            <div class="max-w-2xl">
                <h3 class="text-white text-lg font-bold">Curated Just for You</h3>
                <p class="text-blue-200 text-sm mt-1">
                    Our AI has analyzed your past purchases and favorite genres to find hidden gems among our 1,000,000+ collection. These suggestions evolve every time you browse.
                </p>
            </div>
            <div class="flex items-center space-x-2 bg-black bg-opacity-40 px-4 py-2 rounded-lg border border-blue-900">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                </svg>
                <span class="text-blue-300 text-xs font-mono">Profiled on {{ auth()->user()->orders()->count() }} orders</span>
            </div>
        </div>
    </div>

    <!-- Recommendation Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($books as $book)
            <div class="group bg-gray-800 rounded-lg border border-gray-700 overflow-hidden hover:border-blue-500 transition-all duration-300 transform hover:-translate-y-1 shadow-lg">
                <div class="relative h-48 bg-gray-900 flex items-center justify-center">
                    <span class="text-4xl text-gray-700 group-hover:scale-110 transition duration-300">📖</span>
                    <div class="absolute top-2 right-2">
                        <span class="bg-blue-600 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow-lg uppercase tracking-widest">
                            Match Found
                        </span>
                    </div>
                </div>
                <div class="p-4 space-y-2">
                    <h4 class="text-white font-bold truncate">{{ $book['title'] }}</h4>
                    <p class="text-gray-400 text-xs truncate">by {{ $book['author'] }}</p>

                    <div class="flex justify-between items-center pt-2">
                        <span class="text-blue-400 font-mono text-sm">₱{{ number_format($book['price'], 2) }}</span>
                        <a href="{{ route('books.show', $book['id']) }}" class="text-gray-400 hover:text-white transition">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center">
                <div class="animate-bounce mb-4 text-4xl">✨</div>
                <h3 class="text-gray-400 font-medium">Keep reading to unlock AI recommendations!</h3>
                <p class="text-gray-600 text-sm max-w-xs mx-auto mt-2">Place your first order, and we'll use neural analysis to find your next favorite book.</p>
                <a href="{{ route('books.index') }}" class="mt-6 inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
                    Browse Catalog
                </a>
            </div>
        @endforelse
    </div>

    <!-- AI Disclosure -->
    <div class="pt-10 border-t border-gray-800 flex items-center justify-center space-x-2 text-gray-600 text-[10px] uppercase tracking-tighter">
        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
            <path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1a1 1 0 112 0v1a1 1 0 11-2 0zM13.536 14.243a1 1 0 010 1.414l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 0zM16.243 16.243a1 1 0 01-1.414 0l-.707-.707a1 1 0 011.414-1.414l.707.707a1 1 0 010 1.414z" />
        </svg>
        <span>Content generated via Hybrid Cloud/Local Neural Network (Gemini 1.5 Flash / llama3.2)</span>
    </div>
</div>
@endsection
