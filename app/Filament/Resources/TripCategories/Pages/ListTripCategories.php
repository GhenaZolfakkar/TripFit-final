<?php

namespace App\Filament\Resources\TripCategories\Pages;

use App\Filament\Resources\TripCategories\TripCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Tables\Actions\EditAction;

class ListTripCategories extends ListRecords
{
    protected static string $resource = TripCategoryResource::class;
protected function getHeaderActions(): array
{
    $user = auth()->user();

    if ($user->type === 'agency_owner') {
        return [];
    }

    return [
        CreateAction::make(),
    ];
}

protected function getTableActions(): array
{
    $user = auth()->user();

    if ($user->type !== 'admin') {
        return []; // no row actions at all for non-admins
    }

    return parent::getTableActions(); // default Edit/Delete for admin
}


}
