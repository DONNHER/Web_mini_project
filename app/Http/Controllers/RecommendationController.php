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
     * Show personalized recommendations to the user.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Show a loading state or cached results
        $recommendations = $this->recommendationService->getRecommendations($user, 8);

        return view('user.recommendations', [
            'books' => $recommendations
        ]);
    }
}
