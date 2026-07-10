<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;

class CashFlowSpendingChart extends ChartWidget
{
    protected ?string $heading = 'Current Month Spending by Category';

    protected static ?int $sort = 2;

    private const COLORS = [
        'rgb(255, 99, 132)',
        'rgb(54, 162, 235)',
        'rgb(255, 205, 86)',
        'rgb(75, 192, 192)',
        'rgb(153, 102, 255)',
        'rgb(255, 159, 64)',
    ];

    protected function getData(): array
    {
        $totals = Transaction::query()
            ->with('category')
            ->where('user_id', auth()->id())
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [today()->startOfMonth(), today()->endOfMonth()])
            ->get()
            ->groupBy(fn (Transaction $transaction): string => $transaction->category?->name ?? 'Uncategorized')
            ->sortKeys()
            ->map(fn ($transactions): float => (float) $transactions->sum('amount'));

        return [
            'datasets' => [
                [
                    'label' => 'Spending',
                    'data' => $totals->values()->all(),
                    'backgroundColor' => self::COLORS,
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => $totals->keys()->all(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'cutout' => '60%',
        ];
    }
}
