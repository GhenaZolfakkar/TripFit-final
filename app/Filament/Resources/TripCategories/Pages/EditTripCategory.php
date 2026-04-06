<?php

namespace App\Filament\Resources\TripCategories\Pages;

use App\Filament\Resources\TripCategories\TripCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTripCategory extends EditRecord
{
    protected static string $resource = TripCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
