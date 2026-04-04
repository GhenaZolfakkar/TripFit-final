<?php

namespace App\Filament\Resources\AgencyInvitations;

use App\Filament\Resources\AgencyInvitations\Pages\CreateAgencyInvitation;
use App\Filament\Resources\AgencyInvitations\Pages\EditAgencyInvitation;
use App\Filament\Resources\AgencyInvitations\Pages\ListAgencyInvitations;
use App\Filament\Resources\AgencyInvitations\Schemas\AgencyInvitationForm;
use App\Filament\Resources\AgencyInvitations\Tables\AgencyInvitationsTable;
use Illuminate\Database\Eloquent\Builder;
use App\Models\AgencyInvitation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;


class AgencyInvitationResource extends Resource
{
    protected static ?string $model = AgencyInvitation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'AgencyInvitation';
    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();

        return in_array($user->type, ['agency_owner']);
    }
    public static function getEloquentQuery():Builder
{
    $user = auth()->user();

    if ($user->type !== 'agency_owner') {
        // Non-owners see nothing
        return parent::getEloquentQuery()->whereRaw('0 = 1');
    }

    // Only invitations for this owner
    return parent::getEloquentQuery()->where('agency_id', $user->agency_id);
}

    public static function form(Schema $schema): Schema
    {
        return AgencyInvitationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AgencyInvitationsTable::configure($table);
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
            'index' => ListAgencyInvitations::route('/'),
        ];
    }
}
