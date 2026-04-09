<?php

namespace App\Filament\Resources\TripCategories\Pages;

use App\Filament\Resources\TripCategories\TripCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;

class ListTripCategories extends ListRecords
{
    protected static string $resource = TripCategoryResource::class;
protected function getHeaderActions(): array
{
    $user = auth()->user();

    // ❌ owner → مفيش زرار
    if ($user->type === 'agency_owner') {
        return [];
    }

    return [
        CreateAction::make(),
    ];
}


}
