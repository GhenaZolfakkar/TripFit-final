<?php

namespace App\Filament\Resources\Trips\Pages;

use App\Filament\Resources\Trips\TripResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTrip extends CreateRecord
{
    protected static string $resource = TripResource::class;
protected function mutateFormDataBeforeCreate(array $data): array
{
    $user = auth()->user();

    if ($user->type !== 'admin') {
        $data['agency_id'] = $user->agency_id;
    }

    return $data;
}
}
