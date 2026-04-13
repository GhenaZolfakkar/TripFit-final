<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

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
