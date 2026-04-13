<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('booking_id')
                    ->required()
                    ->numeric(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('currency')
                    ->required()
                    ->default('EGP'),
                TextInput::make('transaction_ref')
                    ->required(),
                TextInput::make('method')
                    ->required()
                    ->default('card'),
                DateTimePicker::make('paid_at'),
                Select::make('status')
                    ->options(['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed'])
                    ->default('pending')
                    ->required(),
                TextInput::make('refund_amount')
                    ->numeric()
                    ->default(null),
                Select::make('refund_status')
                    ->options(['none' => 'None', 'partial' => 'Partial', 'refunded' => 'Refunded'])
                    ->default('none')
                    ->required(),
            ]);
    }
}
