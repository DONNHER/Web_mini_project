<?php

namespace App\Http\Controllers;

use App\Services\AI\CategorizationService;
use Illuminate\Http\Request;

class AIIntegrationController extends Controller
{
    protected $categorizationService;

    public function __construct(CategorizationService $categorizationService)
    {
        $this->categorizationService = $categorizationService;
    }

    /**
     * Categorize user intent and suggest products.
     */
    public function categorize(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:500',
        ]);

        $result = $this->categorizationService->categorizeLoanIntent($request->description);

        return response()->json($result);
    }
}
