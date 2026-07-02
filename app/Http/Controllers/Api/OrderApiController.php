<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderApiController extends Controller
{
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
        // Placeholder for store logic if needed
        return response()->json([
            'success' => true,
            'message' => 'Order processing via API'
        ], 201);
    }
}
