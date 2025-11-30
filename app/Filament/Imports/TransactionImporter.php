<?php

namespace App\Filament\Imports;

use App\Models\Transaction;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class TransactionImporter extends Importer
{
    protected static ?string $model = Transaction::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('user_id')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('amount')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('category_id')
                ->relationship('category', 'name'),
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
        return new Transaction;
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
