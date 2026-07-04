<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Book;
use App\Services\CartService;
use App\Exports\OrdersExport;
use App\Exports\FinancialReportExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Jobs\ProcessAITask;

class OrderController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Display cart page
     */
    public function cart()
    {
        $cartItems = $this->cartService->getCart();
        $total = $this->cartService->getTotal();

        return view('orders.cart', compact('cartItems', 'total'));
    }

    /**
     * Add item to cart
     */
    public function addToCart(Request $request, Book $book)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $book->stock_quantity
        ]);

        try {
            $this->cartService->addItem($book->id, $request->quantity);
            return redirect()->back()->with('success', 'Book added to cart!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update cart item
     */
    public function updateCart(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'quantity' => 'required|integer|min:0'
        ]);

        try {
            $this->cartService->updateItem($request->book_id, $request->quantity);
            return redirect()->route('orders.cart')->with('success', 'Cart updated!');
        } catch (\Exception $e) {
            return redirect()->route('orders.cart')->with('error', $e->getMessage());
        }
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart($bookId)
    {
        $this->cartService->removeItem($bookId);
        return redirect()->route('orders.cart')->with('success', 'Item removed from cart.');
    }

    /**
     * Display checkout page
     */
    public function checkout()
    {
        $cartItems = $this->cartService->getCart();
        $total = $this->cartService->getTotal();

        if (count($cartItems) == 0) {
            return redirect()->route('orders.cart')->with('error', 'Your cart is empty!');
        }

        return view('orders.checkout', compact('cartItems', 'total'));
    }

    /**
     * Place order
     */
    public function store(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string|max:255',
            'payment_method' => 'required|in:credit_card,paypal,bank_transfer,gcash,card,cod'
        ]);

        try {
            // Validate stock before processing
            $this->cartService->validateStock();

            DB::beginTransaction();

            $cartItems = $this->cartService->getCart();
            $total = $this->cartService->getTotal();

            // Create order
            $order = Order::create([
                'user_id' => auth()->id(),
                'total_amount' => $total,
                'status' => 'pending',
                'payment_method' => $request->payment_method, // Save payment method
                'shipping_address' => $request->shipping_address // Save shipping address
            ]);

            // Create order items and update stock
            foreach ($cartItems as $item) {
                $order->orderItems()->create([
                    'book_id' => $item['book']->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['book']->price
                ]);

                // Update stock
                $item['book']->decrement('stock_quantity', $item['quantity']);
            }

            // Clear the cart
            $this->cartService->clearCart();

            DB::commit();

            // 3. Dispatch AI Security Scan in background
            ProcessAITask::dispatch($order->id, request()->ip())->onQueue('ai-tasks');

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to place order: ' . $e->getMessage());
        }
    }

    /**
     * Display order history
     */
    public function index()
    {
        $orders = Order::with('orderItems.book')
                    ->where('user_id', auth()->id())
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Display order details
     */
    public function show(Order $order)
    {
        // Ensure user can only view their own orders (unless admin)
        if (auth()->id() !== $order->user_id && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $order->load(['orderItems.book', 'user']);

        return view('orders.show', compact('order'));
    }

    /**
     * Admin: List all orders
     */
    public function adminIndex(Request $request)
    {
        $query = Order::with('user', 'orderItems.book');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Admin: Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Order status updated!');
    }

    /**
     * Admin: Export orders based on filters
     */
    public function export(Request $request)
    {
        $filters = $request->only(['status', 'date_from', 'date_to', 'customer_id']);
        return Excel::download(new OrdersExport($filters), 'orders_' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Admin: Financial reporting export
     */
    public function exportFinancial(Request $request)
    {
        $filters = $request->only(['date_from', 'date_to']);
        return Excel::download(new FinancialReportExport($filters), 'financial_report_' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Customer: Download PDF Invoice
     */
    public function downloadInvoice(Order $order)
    {
        if (auth()->id() !== $order->user_id && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $order->load(['orderItems.book', 'user']);

        $html = view('orders.invoice', compact('order'))->render();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream('invoice_' . $order->id . '.pdf');
    }
}
