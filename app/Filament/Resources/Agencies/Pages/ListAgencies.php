<?php

namespace App\Filament\Resources\Agencies\Pages;

use App\Filament\Resources\Agencies\AgencyResource;
use Filament\Actions\CreateAction as ActionsCreateAction;
use Filament\Actions\DeleteAction as ActionsDeleteAction;
use Filament\Actions\DeleteBulkAction as ActionsDeleteBulkAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;

class ListAgencies extends ListRecords
{
    protected static string $resource = AgencyResource::class;

    // Top-right header actions (Create button)
    protected function getHeaderActions(): array
    {
        if (auth()->user()->type !== 'admin') {
            return []; // hide Create button for non-admins
        }

        return [
            ActionsCreateAction::make(), // admin sees the Create button
        ];
    }

    // Row-level actions (Edit/Delete per record)
    protected function getTableActions(): array
    {
        $actions = parent::getTableActions();

        if (auth()->user()->type !== 'admin') {
            // Remove Delete button for non-admins
            $actions = collect($actions)
                ->reject(fn($action) => $action instanceof ActionsDeleteAction)
                ->toArray();
        }

        return $actions;
    }

    // Bulk actions
    protected function getTableBulkActions(): array
    {
        $bulkActions = parent::getTableBulkActions();

        if (auth()->user()->type !== 'admin') {
            // Remove bulk delete for non-admins
            $bulkActions = collect($bulkActions)
                ->reject(fn($action) => $action instanceof ActionsDeleteBulkAction)
                ->toArray();
        }

        return $bulkActions;
    }
}

