<?php

namespace App\Services\AI;

use App\Models\Book;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RecommendationService
{
    protected $aiManager;

    public function __construct(AIServiceManager $aiManager)
    {
        $this->aiManager = $aiManager;
    }

    /**
     * Get personalized recommendations for a user.
     */
    public function getRecommendations(User $user, int $limit = 5): array
    {
        $cacheKey = "user_{$user->id}_recommendations";

        return Cache::remember($cacheKey, 3600, function () use ($user, $limit) {
            // 1. Gather User Context (Purchase History)
            $purchasedBooks = $user->orders()
                ->where('status', 'completed')
                ->with('orderItems.book.category')
                ->get()
                ->pluck('orderItems')
                ->flatten()
                ->pluck('book')
                ->unique('id');

            if ($purchasedBooks->isEmpty()) {
                // Fallback to general bestsellers if no history
                return Book::where('is_active', true)
                    ->orderBy('published_at', 'desc')
                    ->limit($limit)
                    ->get()
                    ->toArray();
            }

            // 2. Build AI Prompt
            $historySummary = $purchasedBooks->take(5)->map(fn($b) => "- {$b->title} ({$b->category->name})")->implode("\n");
            $prompt = $this->buildRecommendationPrompt($historySummary);

            // 3. Call AI with Fallback
            try {
                $aiResult = $this->aiManager->generateWithFallback($prompt, 'recommendations');
                $searchCriteria = $this->parseRecommendationResponse($aiResult['text']);

                // 4. Query 1M records based on AI "Intuition"
                return $this->fetchBooksFromCriteria($searchCriteria, $purchasedBooks->pluck('id')->toArray(), $limit);

            } catch (\Exception $e) {
                Log::error("AI Recommendation failed: " . $e->getMessage());
                return Book::where('is_active', true)->limit($limit)->get()->toArray();
            }
        });
    }

    protected function buildRecommendationPrompt(string $history): string
    {
        return "You are a expert librarian. Based on a user's reading history, suggest 3 specific search keywords and 2 preferred categories.
        User History:
        {$history}

        Respond ONLY in JSON format:
        {\"keywords\": [\"space exploration\", \"magic systems\", \"detective noir\"], \"categories\": [\"Science Fiction\", \"Mystery\"]}";
    }

    protected function parseRecommendationResponse(string $text): array
    {
        $cleanText = preg_replace('/```json|```/', '', $text);
        $data = json_decode(trim($cleanText), true);

        return [
            'keywords' => $data['keywords'] ?? [],
            'categories' => $data['categories'] ?? []
        ];
    }

    protected function fetchBooksFromCriteria(array $criteria, array $excludeIds, int $limit): array
    {
        $query = Book::where('is_active', true)
            ->whereNotIn('id', $excludeIds);

        // Filter by AI suggested categories if they exist
        if (!empty($criteria['categories'])) {
            $query->whereHas('category', function($q) use ($criteria) {
                $q->whereIn('name', $criteria['categories']);
            });
        }

        // Search suggested keywords in title/description
        if (!empty($criteria['keywords'])) {
            $query->where(function($q) use ($criteria) {
                foreach ($criteria['keywords'] as $keyword) {
                    $q->orWhere('title', 'like', "%{$keyword}%")
                      ->orWhere('description', 'like', "%{$keyword}%");
                }
            });
        }

        return $query->inRandomOrder()
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
