<?php

namespace App\Services;

use App\Models\LoanProduct;

class LendingService
{
    /**
     * Calculate total loan amount including interest.
     */
    public function calculateTotal($principal, $interestRate, $termMonths)
    {
        // Simple interest calculation for now
        $interest = $principal * ($interestRate / 100) * ($termMonths / 12);
        return $principal + $interest;
    }

    /**
     * Calculate monthly installment.
     */
    public function calculateMonthlyInstallment($totalAmount, $termMonths)
    {
        if ($termMonths <= 0) return 0;
        return $totalAmount / $termMonths;
    }

    /**
     * Validate loan application against product limits.
     */
    public function validateApplication(LoanProduct $product, $amount)
    {
        if ($amount < $product->min_amount) {
            throw new \Exception("The amount requested is below the minimum for this loan product (PHP " . number_format($product->min_amount, 2) . ").");
        }

        if ($amount > $product->max_amount) {
            throw new \Exception("The amount requested exceeds the maximum for this loan product (PHP " . number_format($product->max_amount, 2) . ").");
        }

        return true;
    }
}
