<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Repayment extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'loan_id',
        'amount_paid',
        'payment_date',
        'payment_method',
        'transaction_reference',
        'status', // e.g., pending, verified, rejected
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
