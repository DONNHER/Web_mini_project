<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        // Optimized with eager loading for categories and reviews to prevent N+1 queries in the view
        $featuredBooks = Book::with(['category', 'reviews'])
                            ->orderBy('created_at', 'desc')
                            ->take(8)
                            ->get();

        $categories = Category::withCount('books')->get();

        return view('home', compact('featuredBooks', 'categories'));
    }
}
