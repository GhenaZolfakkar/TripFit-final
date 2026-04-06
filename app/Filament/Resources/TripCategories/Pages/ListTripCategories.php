<?php

namespace App\Filament\Resources\TripCategories\Pages;

use App\Filament\Resources\TripCategories\TripCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTripCategories extends ListRecords
{
    protected static string $resource = TripCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
    protected function canCreate(): bool
{
    return in_array(auth()->user()->type, ['admin', 'agency_member']);
}
}
