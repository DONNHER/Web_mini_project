<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\AIUsageLog;
use App\Models\AISecurityLog;
use App\Services\AI\FraudDetectionService;

class AISecurityController extends Controller
{
    /**
     * Display AI security logs and flagged orders.
     */
    public function index(Request $request)
    {
        $logs = AISecurityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $flaggedOrders = Order::where('status', 'flagged')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.ai-security.index', compact('logs', 'flaggedOrders'));
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
     * Resolve a flagged order.
     */
    public function resolve(Request $request, Order $order)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,cancel',
        ]);

        if ($validated['action'] === 'approve') {
            $order->update(['status' => 'pending']);
            $message = 'Order has been approved and moved back to processing.';
        } else {
            $order->update(['status' => 'cancelled']);
            $message = 'Order has been cancelled due to fraud risk.';
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
     * Re-trigger AI analysis for a flagged order.
     */
    public function rescanOrder(Order $order, FraudDetectionService $fraudService)
    {
        $result = $fraudService->analyzeOrder($order, request()->ip());

        $status = $result['score'] > 70 ? 'flagged' : 'pending';
        $order->update(['status' => $status]);

        return back()->with('success', "Rescan completed. AI Score: {$result['score']}% - Result: {$result['category']}");
    }

    /**
     * Batch sync status for all orders that have high risk logs but aren't flagged.
     */
    public function syncFlaggedStatus()
    {
        $highRiskLogs = AISecurityLog::where('risk_score', '>', 70)
            ->where('resource_type', 'Order')
            ->get();

        $updatedCount = 0;
        foreach ($highRiskLogs as $log) {
            $order = Order::find($log->resource_id);
            if ($order && $order->status !== 'flagged' && $order->status !== 'cancelled') {
                $order->update(['status' => 'flagged']);
                $updatedCount++;
            }
        }

        return back()->with('success', "Synchronized status for {$updatedCount} high-risk orders.");
    }
}
