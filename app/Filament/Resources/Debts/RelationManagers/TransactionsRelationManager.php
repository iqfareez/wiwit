<?php

namespace App\Filament\Resources\Debts\RelationManagers;

use App\Models\DebtTransaction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    protected static ?string $title = 'Payments';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->minValue(0.01),
                DatePicker::make('paid_date')
                    ->default(today())
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitle(fn (DebtTransaction $record): string => "{$record->amount} on {$record->paid_date->toDateString()}")
            ->columns([
                TextColumn::make('amount')
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 2, '.', ''))
                    ->sortable(),
                TextColumn::make('paid_date')
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('paid_date', 'desc')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
