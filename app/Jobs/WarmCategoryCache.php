<?php

namespace App\Jobs;

use App\Models\LoanProduct;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WarmCategoryCache implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $categoryId;

    /**
     * Create a new job instance.
     */
    public function __construct($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $products = LoanProduct::select(['id', 'name', 'interest_rate', 'duration_months'])
            ->where('category_id', $this->categoryId)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(1000)
            ->get();

        $cacheKey = "loan_category:{$this->categoryId}:popular";

        try {
            Cache::tags(["category:{$this->categoryId}"])
                ->put($cacheKey, $products, 7200);
        } catch (\BadMethodCallException $e) {
            Cache::put($cacheKey, $products, 7200);
        }
    }
}
