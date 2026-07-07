<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LoanProduct;
use App\Models\Loan;
use App\Models\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $stats = $this->getDashboardData($request->get('range', 'week'));

        return view('admin.dashboard', $stats);
    }

    /**
     * API for AJAX dashboard refresh
     */
    public function stats(Request $request)
    {
        return response()->json($this->getDashboardData($request->get('range', 'week')));
    }

    protected function getDashboardData($range = 'week')
    {
        $startDate = match ($range) {
            'today' => now()->startOfDay(),
            'week' => now()->subDays(7),
            'month' => now()->subMonth(),
            'year' => now()->subYear(),
            default => now()->subDays(7),
        };

        // 1. User Statistics
        $totalUsers = User::count();
        $activeNow = DB::table('sessions')->where('last_activity', '>=', now()->subMinutes(5)->getTimestamp())->count();

        $registrations = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->get();

        // 2. Transaction Overview (Monthly activity)
        $monthlyActivityQuery = Loan::select(
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->orderBy('month');

        if (config('database.default') === 'sqlite') {
            $monthlyActivityQuery->addSelect(DB::raw('strftime("%Y-%m", created_at) as month'))
                                ->groupBy('month');
        } else {
            $monthlyActivityQuery->addSelect(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'))
                                ->groupBy('month');
        }

        $monthlyActivity = $monthlyActivityQuery->get();

        // 3. System Health
        $dbName = config('database.connections.mysql.database');
        $dbSize = 0;
        try {
            $dbSizeResult = DB::select("SELECT SUM(data_length + index_length) / 1024 / 1024 AS size FROM information_schema.TABLES WHERE table_schema = '{$dbName}'");
            $dbSize = round($dbSizeResult[0]->size, 2);
        } catch (\Exception $e) {}

        // 4. Performance Metrics
        $totalAudits = Audit::where('created_at', '>=', now()->subDay())->count();
        $totalErrors = Audit::where('event', 'error_logged')->where('created_at', '>=', now()->subDay())->count();
        $errorRate = $totalAudits > 0 ? round(($totalErrors / $totalAudits) * 100, 2) : 0;

        // 5. Recent Activities
        $recentActivities = Audit::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Financial Stats
        $totalLoanProducts = LoanProduct::count();
        $totalLoans = Loan::count();
        $totalDisbursed = Loan::where('status', 'released')->sum('principal_amount');

        $loanStatusSummary = Loan::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        return [
            'totalUsers' => $totalUsers,
            'activeNow' => $activeNow,
            'registrations' => $registrations,
            'totalLoanProducts' => $totalLoanProducts,
            'totalLoans' => $totalLoans,
            'totalDisbursed' => $totalDisbursed,
            'loanStatusSummary' => $loanStatusSummary,
            'monthlyActivity' => $monthlyActivity,
            'dbSize' => $dbSize,
            'errorRate' => $errorRate,
            'recentActivities' => $recentActivities,
        ];
    }

    public function runBackup(Request $request)
    {
        try {
            Artisan::queue('backup:run');
            return redirect()->back()->with('success', 'Manual backup has been triggered.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed: ' . $e->getMessage());
        }
    }
}
