<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use App\Traits\Shardable;
use App\Traits\OptimisticLocking;

class Loan extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use Shardable;
    use SoftDeletes;
    use OptimisticLocking;

    protected $fillable = [
        'user_id',
        'loan_product_id',
        'comaker_id',
        'principal_amount',
        'interest_rate',
        'term_months',
        'total_amount',
        'status',
        'payment_method',
        'purpose',
        'ai_tag',
        'due_date',
        'released_at',
        'completed_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function borrower()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comaker()
    {
        return $this->belongsTo(User::class, 'comaker_id');
    }

    public function loanProduct()
    {
        return $this->belongsTo(LoanProduct::class);
    }

    public function repayments()
    {
        return $this->hasMany(Repayment::class);
    }

    public function getMonthlyInstallmentAttribute()
    {
        if ($this->term_months <= 0) return 0;
        return $this->total_amount / $this->term_months;
    }
}
