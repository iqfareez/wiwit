<?php

namespace App\Filament\Imports;

use App\Models\Category;
use App\Models\Transaction;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;

class TransactionImporter extends Importer
{
    protected static ?string $model = Transaction::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('amount')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('category')
                ->guess(['category'])
                ->relationship(resolveUsing: function (string $state): ?Category {
                    // Find or create category
                    $category = Category::firstOrCreate(
                        ['name' => $state],
                        [
                            'user_id' => Auth::id(),
                            'is_active' => true,
                        ]
                    );

                    return $category;
                }),
            ImportColumn::make('notes')
                ->guess(['description'])
                ->rules(['string', 'nullable']),
            ImportColumn::make('transaction_date')
                ->guess(['date'])
                ->rules(['required', 'date']),
        ];
    }

    public function resolveRecord(): Transaction
    {
        return new Transaction([
            'user_id' => Auth::id(),
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your transaction import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
