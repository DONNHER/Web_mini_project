<?php

namespace App\Http\Controllers;

use App\Services\AI\RecommendationService;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    protected $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    /**
     * Show AI-powered credit insights and pre-approvals.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $insights = $this->recommendationService->getFinancialInsights($user);

        return view('user.recommendations', compact('insights'));
    }
}
