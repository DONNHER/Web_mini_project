<?php

namespace App\Http\Controllers;

use App\Models\LoanProduct;
use App\Models\LoanCategory;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = LoanProduct::with(['category'])
                            ->where('is_active', true)
                            ->orderBy('created_at', 'desc')
                            ->take(8)
                            ->get();

        $categories = LoanCategory::withCount('loanProducts')->get();

        return view('home', compact('featuredProducts', 'categories'));
    }
}
