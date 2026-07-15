<?php

namespace App\Filament\Resources\Debts\Tables;

use App\Models\Debt;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DebtsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('other_person')
            ->columns([
                TextColumn::make('other_person')
                    ->label('Person')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('direction')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'borrowed' ? 'I owe them' : 'They owe me')
                    ->color(fn (string $state): string => $state === 'borrowed' ? 'danger' : 'success'),
                TextColumn::make('amount')
                    ->label('Original amount')
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 2, '.', ''))
                    ->sortable(),
                TextColumn::make('transactions_sum_amount')
                    ->label('Paid')
                    ->sum('transactions', 'amount')
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 2, '.', '')),
                TextColumn::make('remaining')
                    ->state(fn (Debt $record): string => $record->balance())
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 2, '.', '')),
                TextColumn::make('status')
                    ->state(fn (Debt $record): string => (float) $record->balance() <= 0 ? 'Settled' : 'Pending')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Settled' ? 'success' : 'warning'),
                TextColumn::make('borrowed_date')
                    ->label('Borrowed date')
                    ->date()
                    ->sortable(),
                TextColumn::make('notes')
                    ->limit(30)
                    ->wrap()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('direction')
                    ->options([
                        'borrowed' => 'I owe them',
                        'lent' => 'They owe me',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('borrowed_date', 'desc');
    }
}
