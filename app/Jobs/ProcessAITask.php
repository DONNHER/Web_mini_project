<?php

namespace App\Jobs;

use App\Models\Loan;
use App\Services\AI\RiskAssessmentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAITask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $loanId;
    protected $ip;

    public $tries = 3;
    public $backoff = 30;

    /**
     * Create a new job instance.
     * We use this for heavy, non-blocking AI risk assessments.
     */
    public function __construct(int $loanId, ?string $ip = null)
    {
        $this->loanId = $loanId;
        $this->ip = $ip;
    }

    /**
     * Execute the job.
     */
    public function handle(RiskAssessmentService $riskService): void
    {
        $loan = Loan::find($this->loanId);

        if (!$loan) {
            return;
        }

        Log::info("Starting background AI risk assessment for Loan #{$this->loanId}");

        // Perform the analysis
        $result = $riskService->analyzeRisk($loan, $this->ip);

        // Update loan status if AI detects high risk
        if ($result['score'] > 70 || $result['category'] === 'High') {
            $loan->update(['status' => 'flagged']);
            Log::warning("Loan #{$this->loanId} has been FLAGGED for high credit risk. Score: {$result['score']}%");
        }

        Log::info("Background AI risk assessment completed for Loan #{$this->loanId}. Result: " . $result['category']);
    }
}
