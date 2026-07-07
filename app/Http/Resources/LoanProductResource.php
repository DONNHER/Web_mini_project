<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'interest_rate' => $this->interest_rate,
            'duration_months' => $this->duration_months,
            'min_amount' => $this->min_amount,
            'max_amount' => $this->max_amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
