<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class SpendingStatsOverview extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $today = today();
        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();
        $yearStart = $today->copy()->startOfYear();
        $yearEnd = $today->copy()->endOfYear();

        return [
            Stat::make('Today\'s Net Cash Flow', $this->formatMoney($this->netFor(
                fn (Builder $query): Builder => $query->whereDate('transaction_date', $today),
            )))
                ->description('Average daily spending this month: '.$this->formatUnsignedMoney(
                    $this->transactions()
                        ->where('type', 'expense')
                        ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                        ->sum('amount') / $today->day,
                )),
            Stat::make('Monthly Net Cash Flow', $this->formatMoney($this->netFor(
                fn (Builder $query): Builder => $query->whereBetween('transaction_date', [$monthStart, $monthEnd]),
            ))),
            Stat::make('Yearly Net Cash Flow', $this->formatMoney($this->netFor(
                fn (Builder $query): Builder => $query->whereBetween('transaction_date', [$yearStart, $yearEnd]),
            ))),
        ];
    }

    private function transactions(): Builder
    {
        return Transaction::query()->where('user_id', auth()->id());
    }

    private function netFor(callable $scope): float
    {
        $query = $scope($this->transactions());

        return (float) $query->clone()->where('type', 'income')->sum('amount')
            - (float) $query->clone()->where('type', 'expense')->sum('amount');
    }

    private function formatMoney(float $amount): string
    {
        return ($amount >= 0 ? '+' : '-').$this->formatUnsignedMoney(abs($amount));
    }

    private function formatUnsignedMoney(float $amount): string
    {
        return number_format($amount, 2, '.', '');
    }
}
