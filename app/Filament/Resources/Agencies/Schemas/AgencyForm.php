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
    ->label('Agency Owner')
    ->relationship(
        'owner',
        'name',
        fn($query, $record) => $query->where('type', 'user')
            ->orWhere('id', $record?->owner_id)
    )
    ->preload()
    ->required()
    ->disabled(),

                
                Hidden::make('owner_id')
                    ->default(fn() => auth()->id())
                    ->visible(fn() => auth()->user()->type !== 'admin'),

                TextInput::make('agency_name')
                    ->required()
                    ->maxLength(255),

            
                FileUpload::make('logo')
                    ->image()
                    ->directory('agency')
                    ->nullable(),

                Textarea::make('description')
                    ->columnSpanFull(),


                TextInput::make('website')
                    ->url()
                    ->nullable(),

                
                TextInput::make('rating')
                    ->numeric()
                    ->default(0),

            
                Textarea::make('contact_details'),

            
                FileUpload::make('business_license')
                    ->nullable()
                    ->disabled(),

    
                TextInput::make('documentation_url')
                    ->url()
                    ->nullable(),

        
                Select::make('verification_status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending')
                    ->visible(fn() => auth()->user()->type === 'admin')
                    ->disabled(),
            ]);
    }
}
