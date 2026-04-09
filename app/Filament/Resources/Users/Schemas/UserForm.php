<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
         ->components([
                TextInput::make('name')->required()->disabled(),
                TextInput::make('phone')->tel()->default(null)->disabled(),
                TextInput::make('email')->email()->required()->disabled(),
                Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                        'blocked' => 'Blocked',
                    ])
                    ->default('active')
                    ->required()
                    ->disabled(fn() => auth()->user()->type !== 'admin'),
            ]);
    }
}
