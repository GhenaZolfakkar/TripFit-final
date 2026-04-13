<?php

namespace App\Filament\Resources\Reviews\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('booking_id')
                    ->required()
                    ->numeric(),
                TextInput::make('trip_id')
                    ->required()
                    ->numeric(),
                TextInput::make('rating')
                    ->required()
                    ->numeric(),
                TextInput::make('title')
                    ->default(null),
                Textarea::make('comment')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
