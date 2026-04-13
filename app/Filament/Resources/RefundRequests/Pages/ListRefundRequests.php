<?php

namespace App\Filament\Resources\RefundRequests\Pages;

use App\Filament\Resources\RefundRequests\RefundRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRefundRequests extends ListRecords
{
    protected static string $resource = RefundRequestResource::class;

    protected function getHeaderActions(): array
{
    $user = auth()->user();

    if ($user->type === 'admin' || $user->type === 'agency_owner') {
        return [];
    }

    return [
        CreateAction::make(),
    ];
}
}
