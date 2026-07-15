<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DebtTransaction extends Model
{
    protected $fillable = [
        'debt_id',
        'amount',
        'paid_date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_date' => 'date',
        ];
    }

    public function debt()
    {
        return $this->belongsTo(Debt::class);
    }
}
