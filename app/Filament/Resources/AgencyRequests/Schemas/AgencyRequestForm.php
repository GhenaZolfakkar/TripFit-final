<?php

namespace App\Filament\Resources\AgencyRequests\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;

class AgencyRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
       ->components([
                TextInput::make('name')
                    ->label('First Name')
                    ->required()
                    ->maxLength(255)
                    ->disabled(), 
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignorable: fn($record) => $record)
                    ->disabled(),
                TextInput::make('phone')
                    ->label('Phone')
                    ->maxLength(20)
                    ->disabled(fn() => auth()->user()->type !== 'agency_owner'),
                TextInput::make('agency_name')
                    ->label('Agency Name')
                    ->required()
                    ->maxLength(255)
                    ->disabled(),
                FileUpload::make('logo')
                    ->label('Logo')
                    ->image()
                    ->directory('agency-logos')
                    ->disabled(fn() => auth()->user()->type !== 'agency_owner'),
                Textarea::make('description')
                    ->label('Description')
                    ->disabled(fn() => auth()->user()->type !== 'agency_owner'),
                TextInput::make('website')
                    ->label('Website')
                    ->url()
                    ->disabled(fn() => auth()->user()->type !== 'agency_owner'),
                Textarea::make('contact_details')
                    ->label('Contact Details')
                    ->disabled(fn() => auth()->user()->type !== 'agency_owner'),
                FileUpload::make('business_license')
                    ->label('Business License')
                    ->directory('licenses')
                    ->disabled(),
                TextInput::make('documentation_url')
                    ->url()
                    ->nullable()
                    ->disabled(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending')
                    ->disabled(fn() => auth()->user()->type !== 'admin'), // only admin can change status
            ]);
            
    }
}

