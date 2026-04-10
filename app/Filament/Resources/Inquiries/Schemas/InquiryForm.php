<?php

namespace App\Filament\Resources\Inquiries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;

class InquiryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
        ->components([
             TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->disabled(),

            TextInput::make('email')
                ->label('Email address')
                ->email()
                ->required()
                ->maxLength(255)
                ->disabled(),

            TextInput::make('subject')
                ->required()
                ->maxLength(255)
                ->disabled(),

            Textarea::make('message')
                ->required()
                ->columnSpanFull()
                ->disabled(), 

            Textarea::make('reply')
                ->label('Admin Reply')
                ->columnSpanFull(),

            Select::make('status')
                ->required()
                ->options([
                    'open' => 'Open',
                    'in_progress' => 'In Progress',
                    'resolved' => 'Resolved',
                ])
                ->default('open'),
                 ]);
    }
}
