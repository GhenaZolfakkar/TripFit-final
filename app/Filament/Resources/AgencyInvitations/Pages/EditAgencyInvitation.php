<?php

namespace App\Filament\Resources\AgencyInvitations\Pages;

use App\Filament\Resources\AgencyInvitations\AgencyInvitationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAgencyInvitation extends EditRecord
{
    protected static string $resource = AgencyInvitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
