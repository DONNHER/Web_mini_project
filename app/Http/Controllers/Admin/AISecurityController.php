<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Loan;
use App\Models\AIUsageLog;
use App\Models\AISecurityLog;
use App\Services\AI\RiskAssessmentService;

class AISecurityController extends Controller
{
    /**
     * Display AI risk assessment logs and flagged loans.
     */
    public function index(Request $request)
    {
        $logs = AISecurityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $flaggedLoans = Loan::where('status', 'flagged')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.ai-security.index', compact('logs', 'flaggedLoans'));
    }

    /**
     * Display AI Usage and Cost tracking dashboard.
     */
    public function usage()
    {
        $usageLogs = AIUsageLog::orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'total_tokens' => AIUsageLog::sum('tokens_used'),
            'total_cost' => AIUsageLog::sum('cost_estimate'),
            'provider_breakdown' => AIUsageLog::select('provider', DB::raw('count(*) as count'), DB::raw('sum(cost_estimate) as cost'))
                ->groupBy('provider')
                ->get(),
            'daily_cost' => AIUsageLog::whereDate('created_at', now())->sum('cost_estimate'),
        ];

        return view('admin.ai-security.usage', compact('usageLogs', 'stats'));
    }

    /**
     * Resolve a flagged loan based on risk assessment.
     */
    public function resolve(Request $request, Loan $loan)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        if ($validated['action'] === 'approve') {
            $loan->update(['status' => 'pending']);
            $message = 'Loan has been cleared and moved back to processing.';
        } else {
            $loan->update(['status' => 'rejected']);
            $message = 'Loan has been rejected due to high credit risk.';
        }

        return back()->with('success', $message);
    }

    /**
     * Display log details.
     */
    public function showLog(AISecurityLog $log)
    {
        return view('admin.ai-security.show-log', compact('log'));
    }

    /**
     * Delete a log entry.
     */
    public function destroyLog(AISecurityLog $log)
    {
        $log->delete();
        return back()->with('success', 'Security log entry has been removed.');
    }

    /**
     * Re-trigger AI risk assessment for a flagged loan.
     */
    public function rescanLoan(Loan $loan, RiskAssessmentService $riskService)
    {
        $result = $riskService->analyzeRisk($loan, request()->ip());

        $status = $result['score'] > 70 ? 'flagged' : 'pending';
        $loan->update(['status' => $status]);

        return back()->with('success', "Risk assessment completed. AI Score: {$result['score']}% - Result: {$result['category']}");
    }

    /**
     * Batch sync status for all loans that have high risk logs but aren't flagged.
     */
    public function syncFlaggedStatus()
    {
        $highRiskLogs = AISecurityLog::where('risk_score', '>', 70)
            ->where('resource_type', 'Loan')
            ->get();

        $updatedCount = 0;
        foreach ($highRiskLogs as $log) {
            $loan = Loan::find($log->resource_id);
            if ($loan && $loan->status !== 'flagged' && $loan->status !== 'rejected' && $loan->status !== 'completed') {
                $loan->update(['status' => 'flagged']);
                $updatedCount++;
            }
        }

        return back()->with('success', "Synchronized status for {$updatedCount} high-risk loans.");
    }
}
