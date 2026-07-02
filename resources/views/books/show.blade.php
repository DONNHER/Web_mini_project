@extends('layouts.app')

@section('title', $book->title . ' - PageTurner')

@section('content')
    <div class="bg-gray-800 rounded-lg shadow-xl overflow-hidden border border-gray-700">
        <div class="md:flex">
            <!-- Book Cover -->
            <div class="md:w-1/3 bg-gray-900 p-8 flex items-center justify-center border-r border-gray-700">
                @if($book->cover_image)
                    <img src="{{ asset('storage/' . $book->cover_image) }}"
                        alt="{{ $book->title }}"
                        class="max-h-96 object-contain shadow-2xl">
                @else
                    <!-- Default placeholder SVG -->
                    <svg class="h-48 w-48 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                @endif
            </div>

            <!-- Book Details -->
            <div class="md:w-2/3 p-8">
                <span class="text-blue-400 text-sm font-bold uppercase tracking-wider">{{ $book->category->name }}</span>
                <h1 class="text-4xl font-extrabold text-white mt-2">{{ $book->title }}</h1>
                <p class="text-xl text-gray-400 mt-1 italic">by {{ $book->author }}</p>

                <!-- Rating -->
                <div class="flex items-center mt-4 bg-gray-900/50 w-fit px-3 py-1 rounded-full border border-gray-700">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="h-5 w-5 {{ $i <= round($book->average_rating) ? 'text-yellow-500' : 'text-gray-600' }}"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    @endfor
                    <span class="ml-2 text-sm text-gray-300 font-medium">{{ number_format($book->average_rating, 1) }} ({{ $book->reviews->count() }} reviews)</span>
                </div>

                <p class="text-4xl font-bold text-blue-500 mt-6">₱{{ number_format($book->price, 2) }}</p>

                <div class="mt-4 flex items-center space-x-2">
                    <span class="flex h-2.5 w-2.5 rounded-full {{ $book->stock_quantity > 0 ? 'bg-green-500' : 'bg-red-500' }}"></span>
                    <span class="text-sm font-medium {{ $book->stock_quantity > 0 ? 'text-green-400' : 'text-red-400' }}">
                        @if($book->stock_quantity > 0)
                            In Stock ({{ $book->stock_quantity }} available)
                        @else
                            Out of Stock
                        @endif
                    </span>
                </div>

                <div class="mt-4 p-3 bg-gray-900/30 rounded-md border border-gray-700/50">
                    <p class="text-gray-400 text-sm"><strong>ISBN:</strong> <span class="text-gray-200">{{ $book->isbn }}</span></p>
                </div>

                <div class="mt-8">
                    <h3 class="font-bold text-white text-lg uppercase tracking-tight border-b border-gray-700 pb-2 mb-4">Description</h3>
                    <p class="text-gray-300 leading-relaxed">{{ $book->description }}</p>
                </div>

                <!-- Add to Cart Section -->
                @auth
                    @if($book->stock_quantity > 0)
                        <div class="mt-8 pt-8 border-t border-gray-700">
                            <form action="{{ route('orders.cart.add', $book) }}" method="POST" class="flex items-end space-x-4">
                                @csrf
                                <div>
                                    <label for="quantity" class="block text-sm font-bold text-gray-400 mb-1 uppercase tracking-wider">Qty</label>
                                    <input type="number"
                                           name="quantity"
                                           id="quantity"
                                           value="1"
                                           min="1"
                                           max="{{ $book->stock_quantity }}"
                                           class="w-24 bg-gray-700 border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-white font-bold text-center">
                                </div>
                                <button type="submit"
                                        class="flex-1 bg-blue-600 text-white px-8 py-3 rounded-md hover:bg-blue-700 transition flex items-center justify-center font-bold shadow-lg uppercase tracking-widest">
                                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Add to Cart
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="mt-8 pt-8 border-t border-gray-700">
                            <button disabled class="w-full bg-gray-700 text-gray-400 px-8 py-3 rounded-md cursor-not-allowed flex items-center justify-center font-bold uppercase tracking-widest border border-gray-600">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Out of Stock
                            </button>
                        </div>
                    @endif
                @else
                    <div class="mt-8 pt-8 border-t border-gray-700">
                        <a href="{{ route('login') }}" class="inline-flex items-center text-blue-400 hover:text-blue-300 font-bold uppercase tracking-wider text-sm">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            Login to add items to cart
                        </a>
                    </div>
                @endauth

                <!-- Admin Actions -->
                @auth
                    @if(auth()->user()->isAdmin())
                        <div class="mt-10 flex space-x-4">
                            <a href="{{ route('admin.books.edit', $book) }}"
                               class="bg-gray-700 text-white border border-yellow-500/50 px-4 py-2 rounded hover:bg-gray-600 transition text-sm font-bold uppercase">
                                Edit Book
                            </a>
                            <form action="{{ route('admin.books.destroy', $book) }}" method="POST"
                                  onsubmit="return confirm('Are you sure you want to delete this book?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-gray-700 text-red-400 border border-red-500/50 px-4 py-2 rounded hover:bg-gray-600 transition text-sm font-bold uppercase">
                                    Delete Book
                                </button>
                            </form>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="mt-12">
        <h2 class="text-3xl font-extrabold mb-8 text-white flex items-center">
            <svg class="h-8 w-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            Customer Reviews
        </h2>

        <!-- Review Form (for authenticated users) -->
        @auth
            <div class="bg-gray-800 rounded-lg shadow-xl p-8 mb-8 border border-gray-700">
                <h3 class="font-bold text-xl mb-6 text-white uppercase tracking-tight">Write a Review</h3>
                <form action="{{ route('reviews.store', $book) }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="max-w-xs">
                        <label class="block text-gray-400 font-bold text-sm mb-2 uppercase tracking-wider">Rating</label>
                        <select name="rating" class="w-full bg-gray-700 border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-white font-bold" required>
                            <option value="">Select rating</option>
                            @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}">{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-400 font-bold text-sm mb-2 uppercase tracking-wider">Comment</label>
                        <textarea name="comment" rows="4"
                                  class="w-full bg-gray-700 border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-white"
                                  placeholder="Share your thoughts about this book..."></textarea>
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-md hover:bg-blue-700 transition font-bold shadow-lg uppercase tracking-widest">
                        Submit Review
                    </button>
                </form>
            </div>
        @else
            <div class="bg-gray-800 border-l-4 border-blue-500 p-6 text-gray-300 rounded shadow-md mb-8">
                <p><a href="{{ route('login') }}" class="text-blue-400 font-bold hover:underline">Login</a> to write a review and share your experience.</p>
            </div>
        @endauth

        <!-- Display Reviews -->
        @forelse($book->reviews as $review)
            <div class="bg-gray-800 rounded-lg shadow-lg p-6 mb-4 border border-gray-700 hover:border-gray-600 transition-colors">
                <div class="flex justify-between items-start">
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold mr-4">
                            {{ strtoupper(substr($review->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-bold text-white">{{ $review->user->name }}</p>
                            <div class="flex items-center mt-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="h-4 w-4 {{ $i <= $review->rating ? 'text-yellow-500' : 'text-gray-600' }}"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <span class="text-gray-500 text-xs font-medium">{{ $review->created_at->diffForHumans() }}</span>

                        @auth
                            @if(auth()->id() === $review->user_id || auth()->user()->isAdmin())
                                <form action="{{ route('reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('Delete this review?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 text-xs font-bold uppercase tracking-tighter">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        @endauth
                    </div>
                </div>

                @isset($review->comment)
                    <p class="text-gray-300 mt-4 leading-relaxed bg-gray-900/40 p-4 rounded border-l-2 border-gray-700 italic">"{{ $review->comment }}"</p>
                @endisset
            </div>
        @empty
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-10 text-center shadow-inner">
                <svg class="h-12 w-12 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <p class="text-gray-400 font-medium">No reviews yet. Be the first to share your experience with this book!</p>
            </div>
        @endforelse
    </div>
@endsection
