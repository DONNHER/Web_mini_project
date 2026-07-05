<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoanApiController;
use App\Http\Controllers\Api\LoanProductApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('throttle:api')->group(function () {
    // Public routes
    Route::get('/loan-products', [LoanProductApiController::class, 'index'])->name('api.loan_products.index');
    Route::get('/loan-products/{loanProduct}', [LoanProductApiController::class, 'show'])->name('api.loan_products.show');

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // Authenticated user info
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        // Loan endpoints
        Route::get('/loans', [LoanApiController::class, 'index']);
        Route::post('/loans', [LoanApiController::class, 'store']);
    });
});
