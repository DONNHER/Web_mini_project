<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected $cart;

    public function __construct()
    {
        $this->cart = Session::get('cart', []);
    }

    /**
     * Get all cart items - Optimized with eager loading
     */
    public function getCart()
    {
        if (empty($this->cart)) {
            return [];
        }

        $bookIds = array_keys($this->cart);
        $books = Book::with('category')->whereIn('id', $bookIds)->get()->keyBy('id');

        $cartItems = [];
        foreach ($this->cart as $bookId => $quantity) {
            $book = $books->get($bookId);
            if ($book) {
                $cartItems[] = [
                    'book' => $book,
                    'quantity' => $quantity,
                    'subtotal' => $book->price * $quantity
                ];
            }
        }
        return $cartItems;
    }

    /**
     * Add item to cart
     */
    public function addItem($bookId, $quantity = 1)
    {
        $book = Book::findOrFail($bookId);

        if ($book->stock_quantity < $quantity) {
            throw new \Exception("Not enough stock available. Only {$book->stock_quantity} left.");
        }

        if (isset($this->cart[$bookId])) {
            $this->cart[$bookId] += $quantity;
        } else {
            $this->cart[$bookId] = $quantity;
        }

        Session::put('cart', $this->cart);
    }

    /**
     * Update item quantity
     */
    public function updateItem($bookId, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeItem($bookId);
            return;
        }

        $book = Book::findOrFail($bookId);

        if ($book->stock_quantity < $quantity) {
            throw new \Exception("Not enough stock available. Only {$book->stock_quantity} left.");
        }

        if (isset($this->cart[$bookId])) {
            $this->cart[$bookId] = $quantity;
            Session::put('cart', $this->cart);
        }
    }

    /**
     * Remove item from cart
     */
    public function removeItem($bookId)
    {
        if (isset($this->cart[$bookId])) {
            unset($this->cart[$bookId]);
            Session::put('cart', $this->cart);
        }
    }

    /**
     * Clear cart
     */
    public function clearCart()
    {
        Session::forget('cart');
        $this->cart = [];
    }

    /**
     * Get cart total - Optimized
     */
    public function getTotal()
    {
        if (empty($this->cart)) {
            return 0;
        }

        $bookIds = array_keys($this->cart);
        $books = Book::whereIn('id', $bookIds)->get();

        $total = 0;
        foreach ($books as $book) {
            $total += $book->price * $this->cart[$book->id];
        }
        return $total;
    }

    /**
     * Get cart count
     */
    public function getCount()
    {
        return array_sum($this->cart);
    }

    /**
     * Validate stock availability - Optimized
     */
    public function validateStock()
    {
        if (empty($this->cart)) {
            return true;
        }

        $bookIds = array_keys($this->cart);
        $books = Book::whereIn('id', $bookIds)->get()->keyBy('id');

        foreach ($this->cart as $bookId => $quantity) {
            $book = $books->get($bookId);
            if (!$book || $book->stock_quantity < $quantity) {
                throw new \Exception("Some items in your cart are no longer available in the requested quantity.");
            }
        }
        return true;
    }
}
