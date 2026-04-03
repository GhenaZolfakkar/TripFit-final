<?php

namespace App\Filament\Resources\Agencies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use App\Models\User;
use Filament\Forms\Components\FileUpload;

class AgencyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('owner_id')
                 ->relationship('owner', 'name', fn ($query) =>$query->where('type', 'agency')
            )
            ->searchable()
            ->required(),
            Hidden::make('owner_id')
                ->default(fn() => auth()->id())
                ->hidden(fn() => auth()->user()->agency),
                TextInput::make('agency_name')
                    ->required(),
                FileUpload::make('logo')
                    ->default(null)
                    ->image()
                    ->directory('agency'),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('website')
                    ->url()
                    ->default(null),
            ]);
    }
}
