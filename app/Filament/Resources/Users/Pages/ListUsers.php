<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
{
    $user = auth()->user();

    if ($user->type === 'admin') {
        return [];
    }

    return [
        CreateAction::make(),
    ];
}
   
    }

