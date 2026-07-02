<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\Admin\AdminDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DataPortabilityController;
use App\Http\Controllers\Admin\AuditLogController;
use Maatwebsite\Excel\Facades\Excel;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Book browsing (public)
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

// Category browsing (public)
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

// 2FA Routes
Route::middleware('auth')->group(function () {
    Route::get('/two-factor', [TwoFactorController::class, 'index'])->name('profile.two-factor');
    Route::get('/two-factor/enable', [TwoFactorController::class, 'enable'])->name('two-factor.enable');
    Route::post('/two-factor/confirm', [TwoFactorController::class, 'confirm'])->name('two-factor.confirm');
    Route::post('/two-factor/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
    Route::get('/two-factor-challenge', [TwoFactorController::class, 'challenge'])->name('two-factor.challenge');
    Route::post('/two-factor-challenge', [TwoFactorController::class, 'verify'])->name('two-factor.verify');
});

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/cart', [OrderController::class, 'cart'])->name('orders.cart');
    Route::post('/cart/add/{book}', [OrderController::class, 'addToCart'])->name('orders.cart.add');
    Route::post('/cart/update', [OrderController::class, 'updateCart'])->name('orders.cart.update');
    Route::get('/cart/remove/{book}', [OrderController::class, 'removeFromCart'])->name('orders.cart.remove');

    Route::get('/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');

    Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/invoice', [OrderController::class, 'downloadInvoice'])->name('orders.invoice');

    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');

    // User Data Portability
    Route::get('/dashboard/export-personal-data', [UserDashboardController::class, 'exportPersonalData'])->name('user.export.personal');
    Route::get('/dashboard/export-orders-excel', [UserDashboardController::class, 'exportOrdersExcel'])->name('user.export.orders.excel');
    Route::get('/dashboard/export-orders-pdf', [UserDashboardController::class, 'exportOrdersPdf'])->name('user.export.orders.pdf');
    Route::get('/dashboard/export-reading-history', [UserDashboardController::class, 'exportReadingHistory'])->name('user.export.reading');
});

// Admin-only routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/backup', [AdminDashboardController::class, 'runBackup'])->name('dashboard.backup');

    Route::resource('categories', CategoryController::class)->except(['index', 'show']);
    Route::resource('books', BookController::class)->except(['index', 'show']);

    // Admin order management & exports
    Route::get('/orders', [OrderController::class, 'adminIndex'])->name('orders.index');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    Route::get('/orders/export', [OrderController::class, 'export'])->name('orders.export');
    Route::get('/orders/financial-report', [OrderController::class, 'exportFinancial'])->name('orders.financial');

    // Data Portability
    Route::get('/data-portability', [DataPortabilityController::class, 'index'])->name('data-portability');
    Route::post('/import', [DataPortabilityController::class, 'import'])->name('import');
    Route::get('/export', [DataPortabilityController::class, 'export'])->name('export');
    Route::get('/template', [DataPortabilityController::class, 'template'])->name('template');
    Route::get('/data-portability/logs/{log}', [DataPortabilityController::class, 'show'])->name('data-portability.show');

    // User Portability Routes
    Route::post('/users/import', [DataPortabilityController::class, 'importUsers'])->name('users.import');
    Route::get('/users/export', [DataPortabilityController::class, 'exportUsers'])->name('users.export');
    Route::get('/users/template', [DataPortabilityController::class, 'userTemplate'])->name('users.template');

    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/{audit}', [AuditLogController::class, 'show'])->name('audit-logs.show');
});

require __DIR__.'/auth.php';
