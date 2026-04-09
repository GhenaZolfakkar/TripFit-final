<?php

namespace App\Filament\Resources\AgencyRequests;

use App\Filament\Resources\AgencyRequests\Pages\CreateAgencyRequest;
use App\Filament\Resources\AgencyRequests\Pages\EditAgencyRequest;
use App\Filament\Resources\AgencyRequests\Pages\ListAgencyRequests;
use App\Filament\Resources\AgencyRequests\Schemas\AgencyRequestForm;
use App\Filament\Resources\AgencyRequests\Tables\AgencyRequestsTable;
use App\Models\AgencyRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AgencyRequestResource extends Resource
{
    protected static ?string $model = AgencyRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'AgencyRequest';

    public static function canViewAny(): bool
{
    return auth()->user()?->type === 'admin';
}

    public static function form(Schema $schema): Schema
    {
        return AgencyRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AgencyRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAgencyRequests::route('/'),
            'create' => CreateAgencyRequest::route('/create'),
            'edit' => EditAgencyRequest::route('/{record}/edit'),
        ];
    }
}
