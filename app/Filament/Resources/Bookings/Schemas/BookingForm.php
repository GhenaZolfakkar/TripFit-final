<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Agency;
use App\Models\Trip;

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
                    ->prefix('$')
                    ->disabled(),
 
                TextInput::make('total_price')
                    ->prefix('$')
                    ->disabled(),
 
                TextInput::make('commission_rate')
                    ->suffix('%')
                    ->disabled(),
 
                TextInput::make('commission_amount')
                    ->prefix('$')
                    ->disabled(),
 
                Placeholder::make('remaining_seats')
                    ->label('Remaining Seats')
                    ->content(fn ($record) => $record?->trip?->remainingSeats() ?? '-'),
 
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'cancelled' => 'Rejected',
                    ])
                    ->default('pending')
                    ->disabled(fn () => Auth::user()->type === 'admin')
                    ->required(),
 
            ]);
    }
}
