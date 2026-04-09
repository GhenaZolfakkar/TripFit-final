<?php

namespace App\Filament\Resources\AgencyRequests\Pages;

use App\Filament\Resources\AgencyRequests\AgencyRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAgencyRequest extends EditRecord
{
    protected static string $resource = AgencyRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
