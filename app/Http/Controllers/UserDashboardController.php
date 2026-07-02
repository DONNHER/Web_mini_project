<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use App\Exports\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;
use Dompdf\Dompdf;
use Dompdf\Options;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Order statistics
        $totalOrders = Order::where('user_id', $user->id)->count();
        $recentOrders = Order::where('user_id', $user->id)
            ->with('orderItems.book')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Order status counts
        $pendingOrders = Order::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        $completedOrders = Order::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        // Recent reviews
        $recentReviews = Review::where('user_id', $user->id)
            ->with('book')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recently purchased books (unique books from recent orders)
        $recentBooks = Order::where('user_id', $user->id)
            ->with('orderItems.book')
            ->where('status', 'completed')
            ->latest()
            ->take(3)
            ->get()
            ->pluck('orderItems')
            ->flatten()
            ->pluck('book')
            ->unique('id')
            ->take(4);

        return view('user.dashboard', compact(
            'user',
            'totalOrders',
            'recentOrders',
            'pendingOrders',
            'completedOrders',
            'recentReviews',
            'recentBooks'
        ));
    }

    /**
     * Export personal data in JSON format (GDPR-compliant).
     */
    public function exportPersonalData()
    {
        $user = Auth::user()->load(['orders.orderItems.book', 'reviews.book']);

        $data = [
            'personal_info' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'tier' => $user->tier,
                'created_at' => $user->created_at,
            ],
            'orders' => $user->orders->map(function ($order) {
                return [
                    'order_id' => $order->id,
                    'total_amount' => $order->total_amount,
                    'status' => $order->status,
                    'created_at' => $order->created_at,
                    'items' => $order->orderItems->map(function ($item) {
                        return [
                            'book_title' => $item->book->title,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                        ];
                    }),
                ];
            }),
            'reviews' => $user->reviews->map(function ($review) {
                return [
                    'book_title' => $review->book->title,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'created_at' => $review->created_at,
                ];
            }),
        ];

        return Response::json($data, 200, [
            'Content-Disposition' => 'attachment; filename="my_personal_data.json"',
        ]);
    }

    /**
     * Export order history in Excel format.
     */
    public function exportOrdersExcel()
    {
        return Excel::download(new OrdersExport(['customer_id' => Auth::id()]), 'order_history.xlsx');
    }

    /**
     * Export order history in PDF format.
     */
    public function exportOrdersPdf()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['orderItems.book'])
            ->orderBy('created_at', 'desc')
            ->get();

        $html = view('orders.export_pdf', compact('orders'))->render();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false); // Disabled to prevent external lookups in tests

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="order_history.pdf"');
    }

    /**
     * Export reading/purchase history.
     */
    public function exportReadingHistory()
    {
        $user = Auth::user();

        // Purchase History
        $purchasedBooks = Order::where('user_id', $user->id)
            ->where('status', 'completed')
            ->with('orderItems.book')
            ->get()
            ->pluck('orderItems')
            ->flatten()
            ->map(function($item) {
                return [
                    'type' => 'Purchase',
                    'title' => $item->book->title,
                    'author' => $item->book->author,
                    'date' => $item->created_at->format('Y-m-d H:i:s'),
                ];
            });

        // Reviews as part of reading history
        $reviewedBooks = Review::where('user_id', $user->id)
            ->with('book')
            ->get()
            ->map(function($review) {
                return [
                    'type' => 'Review',
                    'title' => $review->book->title,
                    'author' => $review->book->author,
                    'date' => $review->created_at->format('Y-m-d H:i:s'),
                    'rating' => $review->rating,
                ];
            });

        $history = $purchasedBooks->concat($reviewedBooks)->sortByDesc('date')->values();

        return Response::json([
            'user' => $user->name,
            'reading_and_purchase_history' => $history
        ], 200, [
            'Content-Disposition' => 'attachment; filename="reading_history.json"',
        ]);
    }
}
