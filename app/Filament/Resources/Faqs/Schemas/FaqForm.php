<?php

namespace App\Filament\Resources\Faqs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FaqForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('question')
                    ->required(),
                Textarea::make('answer')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('category')
                    ->default(null),
                Toggle::make('is_active')
                    ->required()
                    ->default('active'),
                TextInput::make('display_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
