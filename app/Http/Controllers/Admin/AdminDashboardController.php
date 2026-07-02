<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Book;
use App\Models\Category;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Statistics
        $totalUsers = User::count();
        $totalBooks = Book::count();
        $totalCategories = Category::count();
        $totalOrders = Order::count();

        // Recent orders
        $recentOrders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Order status summary
        $orderStatusSummary = Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        // Recent reviews
        $recentReviews = Review::with(['user', 'book'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Monthly sales data (for chart)
        $monthlySales = Order::select(
                DB::raw('strftime("%Y-%m", created_at) as month'),
                DB::raw('sum(total_amount) as total')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top selling books - optimized with caching
        $topBooks = Book::getBestsellers(5);

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalBooks',
            'totalCategories',
            'totalOrders',
            'recentOrders',
            'orderStatusSummary',
            'recentReviews',
            'monthlySales',
            'topBooks'
        ));
    }

    /**
     * Trigger manual backup (Requirement 4.2.1)
     */
    public function runBackup(Request $request)
    {
        try {
            // Run backup in background to avoid timeout
            Artisan::queue('backup:run');

            return redirect()->back()->with('success', 'Manual backup has been triggered and is running in the background.');
        } catch (\Exception $e) {
            Log::error('Manual backup failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to trigger backup: ' . $e->getMessage());
        }
    }
}
