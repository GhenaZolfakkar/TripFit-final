<?php

namespace App\Filament\Resources\TripCategories\Pages;

use App\Filament\Resources\TripCategories\TripCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTripCategory extends CreateRecord
{
    protected static string $resource = TripCategoryResource::class;
}
