<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Filament\Resources\Bookings\BookingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBookings extends ListRecords
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
{
    $user = auth()->user();

    if ($user->type === 'agency_owner' || $user->type === 'admin') {
        return [];
    }

    return [
        CreateAction::make(),
    ];
}
}
