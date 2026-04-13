<?php

namespace App\Filament\Resources\RefundRequests\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class RefundRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('amount')
                ->disabled() 
                ->numeric(),

            Textarea::make('reason')
                ->disabled()
                ->columnSpanFull(),



            Textarea::make('admin_reason')
                ->label('Admin Note (only if rejecting)')
                ->columnSpanFull()
                ->disabled(fn() => auth()->user()->type === 'agency_owner'),
        ]);
    }
}
