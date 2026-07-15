<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Debt extends Model
{
    protected $fillable = [
        'user_id',
        'other_person',
        'direction',
        'amount',
        'borrowed_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'borrowed_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(DebtTransaction::class);
    }

    public function balance(): string
    {
        $paid = array_key_exists('transactions_sum_amount', $this->attributes)
            ? $this->attributes['transactions_sum_amount']
            : $this->transactions()->sum('amount');

        return number_format((float) $this->amount - (float) $paid, 2, '.', '');
    }

    public function settle(): ?DebtTransaction
    {
        return DB::transaction(function (): ?DebtTransaction {
            $debt = static::query()->lockForUpdate()->findOrFail($this->getKey());
            $balance = $debt->balance();

            if ((float) $balance <= 0) {
                return null;
            }

            return $debt->transactions()->create([
                'amount' => $balance,
                'paid_date' => today(),
            ]);
        });
    }
}
