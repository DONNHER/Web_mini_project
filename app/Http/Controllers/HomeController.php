<?php

namespace App\Http\Controllers;

use App\Models\LoanProduct;

class HomeController extends Controller
{
    public function index()
    {
        if (auth()->check() && auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $featuredProducts = LoanProduct::where('is_active', true)
                            ->orderBy('created_at', 'desc')
                            ->take(8)
                            ->get();

        return view('home', compact('featuredProducts'));
    }
}
