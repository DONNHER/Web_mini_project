@extends('layouts.app')

@section('title', 'Add New Book - PageTurner')

@section('header')
    <h1 class="text-3xl font-bold text-white tracking-tight">Add New Book</h1>
@endsection

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-gray-800 rounded-xl shadow-xl p-8 border border-gray-700">
            <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div>
                    <label for="title" class="block text-gray-300 font-bold text-xs uppercase tracking-widest mb-2">Title *</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}"
                           class="w-full bg-gray-700 border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-white @error('title') border-red-500 @enderror"
                           required>
                    @error('title')
                        <p class="text-red-400 text-xs mt-1 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="author" class="block text-gray-300 font-bold text-xs uppercase tracking-widest mb-2">Author *</label>
                    <input type="text" name="author" id="author" value="{{ old('author') }}"
                           class="w-full bg-gray-700 border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-white"
                           required>
                </div>

                <div>
                    <label for="category_id" class="block text-gray-300 font-bold text-xs uppercase tracking-widest mb-2">Category *</label>
                    <select name="category_id" id="category_id"
                            class="w-full bg-gray-700 border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-white font-bold"
                            required>
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="isbn" class="block text-gray-300 font-bold text-xs uppercase tracking-widest mb-2">ISBN *</label>
                        <input type="text" name="isbn" id="isbn" value="{{ old('isbn') }}"
                               class="w-full bg-gray-700 border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-white"
                               required>
                    </div>

                    <div>
                        <label for="price" class="block text-gray-300 font-bold text-xs uppercase tracking-widest mb-2">Price (₱) *</label>
                        <input type="number" step="0.01" name="price" id="price" value="{{ old('price') }}"
                               class="w-full bg-gray-700 border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-white"
                               required>
                    </div>
                </div>

                <div>
                    <label for="stock_quantity" class="block text-gray-300 font-bold text-xs uppercase tracking-widest mb-2">Stock Quantity *</label>
                    <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', 0) }}"
                           class="w-full bg-gray-700 border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-white"
                           required>
                </div>

                <div>
                    <label for="description" class="block text-gray-300 font-bold text-xs uppercase tracking-widest mb-2">Description</label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full bg-gray-700 border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-white">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label for="cover_image" class="block text-gray-300 font-bold text-xs uppercase tracking-widest mb-2">Cover Image</label>
                    <input type="file" name="cover_image" id="cover_image" accept="image/*"
                           class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-bold file:bg-gray-700 file:text-blue-400 hover:file:bg-gray-600">
                    <p class="text-[10px] text-gray-500 mt-2 italic">Optional. Max size: 2MB</p>
                </div>

                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-700">
                    <a href="{{ route('books.index') }}"
                       class="bg-gray-700 text-gray-300 border border-gray-600 px-6 py-2 rounded-md hover:bg-gray-600 transition font-bold uppercase tracking-widest text-xs flex items-center">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-8 py-2 rounded-md hover:bg-blue-700 transition font-black uppercase tracking-widest shadow-lg shadow-blue-500/20">
                        Add Book
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
