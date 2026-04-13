<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextColumn;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Agency;
use App\Models\Trip;
use Filament\Forms\Components\Toggle;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
 
                Select::make('user_id')
                    ->relationship('user','name')
                    ->label('Traveler')
                    ->disabled(),
 
                Select::make('trip_id')
                    ->relationship('trip','title')
                    ->label('Trip')
                    ->disabled(),

                Select::make('agency_id')
                ->relationship('agency','agency_name')
                    ->label('Agency')
                    ->disabled()
                    ->visible(fn () => auth()->user()->type === 'admin'),    
 
                TextInput::make('traveler_count')
                    ->label('Number of Travelers')
                    ->disabled(),
 
                TextInput::make('price_per_person')
                    ->prefix('EGP')
                    ->disabled(),
 
 
                TextInput::make('agency_commission_rate')
                    ->suffix('%')
                    ->disabled(),
 

                Placeholder::make('remaining_seats')
                    ->label('Remaining Seats')
                    ->content(fn ($record) => $record?->trip?->remainingSeats() ?? '-'),
 Placeholder::make('total_price')
    ->label('Trip Price')
    ->content(fn ($record) => $record?->total_price . ' EGP'),

Placeholder::make('agency_commission_amount')
    ->label('Platform Commission')
    ->content(fn ($record) => '- ' . $record?->agency_commission_amount . ' EGP'),

Placeholder::make('agency_earnings')
    ->label('Your Earnings')
    ->content(fn ($record) => $record?->agency_earnings . ' EGP'),

Placeholder::make('final_price')
    ->label('Customer Paid')
    ->content(fn ($record) => $record?->final_price . ' EGP'),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'cancelled' => 'Rejected',
                    ])
                    ->default('pending')
                    ->disabled()
                    ->required(),
                
            ]);

    }
}
