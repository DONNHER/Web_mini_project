<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Loan statistics
        $totalLoans = Loan::where('user_id', $user->id)->count();
        $recentLoans = Loan::where('user_id', $user->id)
            ->with('loanProduct')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Loan status counts
        $activeLoans = Loan::where('user_id', $user->id)
            ->where('status', 'released')
            ->count();

        $completedLoans = Loan::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $totalBalance = Loan::where('user_id', $user->id)
            ->where('status', 'released')
            ->sum('total_amount');

        return view('user.dashboard', compact(
            'user',
            'totalLoans',
            'recentLoans',
            'activeLoans',
            'completedLoans',
            'totalBalance'
        ));
    }

    /**
     * Export personal financial data in JSON format (GDPR-compliant).
     */
    public function exportPersonalData()
    {
        $user = Auth::user()->load(['loans.loanProduct']);

        $data = [
            'personal_info' => [
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at,
            ],
            'loans' => $user->loans->map(function ($loan) {
                return [
                    'loan_id' => $loan->id,
                    'product' => $loan->loanProduct?->name,
                    'principal_amount' => $loan->principal_amount,
                    'interest_rate' => $loan->interest_rate,
                    'total_amount' => $loan->total_amount,
                    'status' => $loan->status,
                    'due_date' => $loan->due_date,
                    'released_at' => $loan->released_at,
                    'completed_at' => $loan->completed_at,
                    'created_at' => $loan->created_at,
                ];
            }),
        ];

        return Response::json($data, 200, [
            'Content-Disposition' => 'attachment; filename="my_financial_data.json"',
        ]);
    }
}
