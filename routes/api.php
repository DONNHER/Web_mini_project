<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookApiController;
use App\Http\Controllers\Api\OrderApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('throttle:api')->group(function () {
    // Public routes
    Route::get('/books', [BookApiController::class, 'index'])->name('api.books.index');
    Route::get('/books/{book}', [BookApiController::class, 'show'])->name('api.books.show');

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // Authenticated user info
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        // Book management (Administrative operations)
        Route::post('/books', [BookApiController::class, 'store'])->name('api.books.store');
        Route::put('/books/{book}', [BookApiController::class, 'update'])->name('api.books.update');
        Route::delete('/books/{book}', [BookApiController::class, 'destroy'])->name('api.books.destroy');

        // Order endpoints
        Route::get('/orders', [OrderApiController::class, 'index']);
        Route::post('/orders', [OrderApiController::class, 'store']);
    });
});
