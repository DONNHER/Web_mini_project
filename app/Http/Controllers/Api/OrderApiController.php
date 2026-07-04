<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\AI\FraudDetectionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class OrderApiController extends Controller
{
    protected $fraudService;

    public function __construct(FraudDetectionService $fraudService)
    {
        $this->fraudService = $fraudService;
    }

    public function index(Request $request): JsonResponse
    {
        $query = Order::with('orderItems.book')
            ->where('user_id', $request->user()->id)
            ->orderBy('id', 'desc');

        // Field filtering
        if ($request->has('fields')) {
            $fields = explode(',', $request->get('fields'));
            $query->select($fields);
        }

        // Cursor-based pagination
        $orders = $query->cursorPaginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        // 1. Basic validation and order creation
        $validated = $request->validate([
            'total_amount' => 'required|numeric',
            'shipping_address' => 'required|string',
            'items' => 'required|array',
        ]);

        $order = Order::create([
            'user_id' => $request->user()->id,
            'total_amount' => $validated['total_amount'],
            'shipping_address' => $validated['shipping_address'],
            'status' => 'pending',
            'payment_method' => 'card', // simulated
        ]);

        // 2. AI Security Analysis with full database context
        $securityScan = $this->fraudService->analyzeOrder($order, $request->ip());

        // 3. Automated Decision Logic
        if ($securityScan['category'] === 'High') {
            $order->update(['status' => 'flagged']);
        }

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'status' => $order->status,
            'security' => [
                'rating' => $securityScan['category'],
                'score' => $securityScan['score'],
                'reason' => $securityScan['reason']
            ]
        ], 201);
    }
}
