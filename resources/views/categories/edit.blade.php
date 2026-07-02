@extends('layouts.app')

@section('title', 'Edit Category - PageTurner')

@section('header')
    <h1 class="text-3xl font-bold text-white tracking-tight">Edit Category</h1>
@endsection

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-gray-800 rounded-xl shadow-xl p-8 border border-gray-700">
            <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-gray-300 font-bold text-xs uppercase tracking-widest mb-2">Category Name *</label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name', $category->name) }}"
                           class="w-full bg-gray-700 border-gray-600 rounded-md shadow-sm focus:ring-white focus:border-white text-white @error('name') border-red-500 @enderror"
                           required>
                    @error('name')
                        <p class="text-red-400 text-xs mt-1 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-gray-300 font-bold text-xs uppercase tracking-widest mb-2">Description</label>
                    <textarea name="description"
                              id="description"
                              rows="4"
                              class="w-full bg-gray-700 border-gray-600 rounded-md shadow-sm focus:ring-white focus:border-white text-white">{{ old('description', $category->description) }}</textarea>
                </div>

                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-700">
                    <a href="{{ route('categories.index') }}"
                       class="bg-gray-700 text-white border border-gray-600 px-6 py-2 rounded-md hover:bg-gray-600 transition font-bold uppercase tracking-widest text-xs flex items-center">
                        Cancel
                    </a>
                    <button type="submit"
                            class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-3 rounded-lg hover:from-blue-500 hover:to-indigo-500 transition-all duration-300 font-black uppercase tracking-widest shadow-xl shadow-blue-500/25 transform hover:-translate-y-0.5">
                        Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
