<?php

namespace App\Filament\Resources\Trips;

use App\Filament\Resources\Trips\Pages\CreateTrip;
use App\Filament\Resources\Trips\Pages\EditTrip;
use App\Filament\Resources\Trips\Pages\ListTrips;
use App\Filament\Resources\Trips\Schemas\TripForm;
use App\Filament\Resources\Trips\Tables\TripsTable;
use App\Models\Trip;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class TripResource extends Resource
{
    protected static ?string $model = Trip::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Trip';


public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();
    $user = auth()->user();

    
    if ($user->type === 'admin') {
        return $query;
    }

    
    return $query->where('agency_id', $user->agency_id);
}

public static function canCreate(): bool
{
    $user = auth()->user();

    return in_array($user->type, ['agency_owner']);
}

public static function canEdit(Model $record): bool
{
    $user = auth()->user();

    if ($user->type === 'agency_owner') return true;


    return false;
}


public static function canDelete(Model $record): bool
{
    $user = auth()->user();

    if ($user->type === 'agency_owner') return true;


    return false;
}
    public static function form(Schema $schema): Schema
    {
        return TripForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TripsTable::configure($table);
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
            'index' => ListTrips::route('/'),
            'create' => CreateTrip::route('/create'),
            'edit' => EditTrip::route('/{record}/edit'),
        ];
    }
}
