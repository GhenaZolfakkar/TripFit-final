<?php

namespace App\Filament\Resources\Trips\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use App\Models\TripCategory;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;

class TripForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('tier')
    ->options([
        'basic' => 'Basic',
        'premium' => 'Premium (Featured)',
        'exclusive' => 'Exclusive Deal'
    ])
    ->required(),
            TextInput::make('title')
                ->label('Trip Title')
                ->required()
                ->maxLength(255),

            Textarea::make('description')
    ->label('Description')
    ->required(),

            TextInput::make('destination')
                ->label('Destination')
                ->required(),

            TextInput::make('price')
                ->label('Price')
                ->numeric()
                ->required(),

            TextInput::make('duration')
                ->label('Duration (days)')
                ->numeric()
                ->required(),

            TextInput::make('max_travelers')
                ->label('Max Travelers')
                ->numeric()
                ->required(),
                
            DatePicker::make('start_date')
                ->label('Start Date')
                ->required(),

            DatePicker::make('end_date')
                ->label('End Date')
                ->required(),

            TextInput::make('rating')
                ->label('Rating')
                ->numeric()
                ->minValue(0)
                ->maxValue(5)
                ->nullable(),

   Select::make('trip_category_id')
                ->label('Category')
                ->relationship('category', 'name') 
                ->required(),
Select::make('agency_id')
    ->relationship('agency', 'agency_name')
    ->visible(fn () => auth()->user()->type === 'admin')
    ->required(fn () => auth()->user()->type === 'admin'),
            Select::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'InActive'])
                    ->default('inactive')
                    ->required(),
            Toggle::make('featured')
                    ->required(),

            FileUpload::make('images')
                  ->multiple()
                  ->image()
                 ->directory('trips/images'),
                 FileUpload::make('videos')
    ->multiple()
    ->directory('trips/videos'),
            Repeater::make('services')
                  ->relationship('services')
                  ->schema([
                TextInput::make('service_name')
                  ->label('Service')
                  ->required(),
                Select::make('type')
                  ->options([
                    'included' => 'Included',
                    'not_included' => 'Not Included', ])
                 ->required(),
    ])
            ]);
    }
}
