<?php

namespace App\Filament\Resources\Debts\Schemas;

use App\Filament\Resources\Debts\DebtResource;
use App\Models\Debt;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Operation;
use Filament\Support\Enums\VerticalAlignment;

class DebtForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('other_person')
                    ->label('Friend or family member')
                    ->required()
                    ->maxLength(255),
                Select::make('direction')
                    ->label('Who owes whom?')
                    ->options([
                        'borrowed' => 'I owe them',
                        'lent' => 'They owe me',
                    ])
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->minValue(0.01),
                DatePicker::make('borrowed_date')
                    ->label('Borrowed date')
                    ->default(today())
                    ->required(),
                Textarea::make('notes')
                    ->columnSpanFull(),
                TextEntry::make('balance')
                    ->label(fn (Debt $record): string => $record->direction === 'borrowed' ? 'Balance to pay' : 'Balance to receive')
                    ->state(fn (Debt $record): string => $record->balance())
                    ->visibleOn(Operation::Edit),
                Actions::make([
                    Action::make('settle')
                        ->label(fn (Debt $record): string => $record->direction === 'borrowed' ? 'I paid in full' : 'They paid in full')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->disabled(fn (Debt $record): bool => (float) $record->balance() <= 0)
                        ->action(function (Debt $record, Action $action): void {
                            $payment = $record->settle();

                            $notification = Notification::make()
                                ->title($payment ? 'Debt settled' : 'Debt is already settled');

                            if ($payment) {
                                $notification->success();
                            } else {
                                $notification->warning();
                            }

                            $notification->send();

                            $action->redirect(DebtResource::getUrl('edit', ['record' => $record]));
                        }),
                ])
                    ->verticalAlignment(VerticalAlignment::End)
                    ->visibleOn(Operation::Edit),
            ]);
    }
}
