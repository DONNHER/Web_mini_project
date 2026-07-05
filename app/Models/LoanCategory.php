<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\OptimisticLocking;

class LoanCategory extends Model
{
    use HasFactory;
    use SoftDeletes;
    use OptimisticLocking;

    protected $fillable = ['name', 'description'];

    public function loanProducts()
    {
        return $this->hasMany(LoanProduct::class, 'category_id');
    }
}
