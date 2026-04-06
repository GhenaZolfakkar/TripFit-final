<?php

namespace App\Filament\Resources\TripCategories;

use App\Filament\Resources\TripCategories\Pages\CreateTripCategory;
use App\Filament\Resources\TripCategories\Pages\EditTripCategory;
use App\Filament\Resources\TripCategories\Pages\ListTripCategories;
use App\Filament\Resources\TripCategories\Schemas\TripCategoryForm;
use App\Filament\Resources\TripCategories\Tables\TripCategoriesTable;
use App\Models\TripCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


use Filament\Tables;

class TripCategoryResource extends Resource
{
    protected static ?string $model = TripCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'TripCategory';

 public static function getActions(): array
    {
        // Only admin can see "Create" button
        if (auth()->user()->type !== 'admin') {
            return [];
        }
 
        return [
            ActionsCreateAction::make(),
        ];
    }

   public static function canCreate(): bool
{
    $user = auth()->user();

    return in_array($user->type, ['admin', 'agency_member']);
}

  public static function canEdit(Model $record): bool
{
    $user = auth()->user();

    if ($user->type === 'admin') return true;

    if ($user->type === 'agency_member') {
        return true;
    }

    return false; // owner ممنوع
}

 public static function canDelete(Model $record): bool
{
    $user = auth()->user();

    if ($user->type === 'admin') return true;

    if ($user->type === 'agency_member') {
        return true;
    }

    return false;
}

    // No restriction on listing categories
   public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery(); // everyone sees all categories
}



    public static function form(Schema $schema): Schema
    {
        return TripCategoryForm::configure($schema);
    }

    

    public static function table(Table $table): Table
    {
        return TripCategoriesTable::configure($table);
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
            'index' => ListTripCategories::route('/'),
            'create' => CreateTripCategory::route('/create'),
            'edit' => EditTripCategory::route('/{record}/edit'),
        ];
    }
}
