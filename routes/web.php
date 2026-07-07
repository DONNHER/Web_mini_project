<?php

use App\Http\Controllers\LoanProductController;
use App\Http\Controllers\LoanCategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\Admin\AdminDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DataPortabilityController;
use App\Http\Controllers\Admin\AuditLogController;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Loan Product browsing (public)
Route::get('/loan-products', [LoanProductController::class, 'index'])->name('loan_products.index');
Route::get('/loan-products/{loanProduct}', [LoanProductController::class, 'show'])->name('loan_products.show');

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
Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/logout-other-sessions', [ProfileController::class, 'logoutOtherBrowserSessions'])->name('profile.logout-other-sessions');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/loans/apply/{loanProduct}', [LoanController::class, 'apply'])->name('loans.apply');
    Route::post('/loans/apply', [LoanController::class, 'store'])->name('loans.store');
    Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
    Route::get('/loans/{loan}', [LoanController::class, 'show'])->name('loans.show');
    Route::get('/loans/{loan}/invoice', [LoanController::class, 'invoice'])->name('loans.invoice');

    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/profile/export', [ProfileController::class, 'export'])->name('user.export.personal');
    Route::get('/recommendations', [RecommendationController::class, 'index'])->name('user.recommendations');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [\App\Http\Controllers\NotificationController::class, 'getUnread'])->name('notifications.unread');
    Route::post('/notifications/mark-read/{id}', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/profile/notifications', [\App\Http\Controllers\NotificationController::class, 'preferences'])->name('profile.notifications');
    Route::patch('/profile/notifications', [\App\Http\Controllers\NotificationController::class, 'updatePreferences'])->name('profile.notifications.update');

    // AI Integration Routes
    Route::post('/ai/categorize', [\App\Http\Controllers\AIIntegrationController::class, 'categorize'])->name('ai.categorize');
});

// Admin-only routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [AdminDashboardController::class, 'stats'])->name('dashboard.stats');
    Route::post('/dashboard/backup', [AdminDashboardController::class, 'runBackup'])->name('dashboard.backup');

    // User Impersonation
    // Data Portability (Requirement 8)
    Route::get('/data-portability', [DataPortabilityController::class, 'index'])->name('data-portability.index');
    Route::get('/loan-products/export', [DataPortabilityController::class, 'export'])->name('loan-products.export');
    Route::post('/loan-products/import', [DataPortabilityController::class, 'import'])->name('loan-products.import');
    Route::get('/users/export', [DataPortabilityController::class, 'exportUsers'])->name('users.export');
    Route::post('/users/import', [DataPortabilityController::class, 'importUsers'])->name('users.import');

    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::resource('loan-products', LoanProductController::class)->except(['index', 'show']);
    Route::post('loan-products/{id}/restore', [LoanProductController::class, 'restore'])->name('loan-products.restore');

    // Admin loan management
    Route::get('/loans', [LoanController::class, 'adminIndex'])->name('loans.index');
    Route::patch('/loans/{loan}/status', [LoanController::class, 'updateStatus'])->name('loans.status');

    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/{audit}', [AuditLogController::class, 'show'])->name('audit-logs.show');

    Route::get('/ai-security', [\App\Http\Controllers\Admin\AISecurityController::class, 'index'])->name('ai-security.index');

    // System Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::patch('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');

    // Reports
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate', [\App\Http\Controllers\Admin\ReportController::class, 'generate'])->name('reports.generate');
});

require __DIR__.'/auth.php';
