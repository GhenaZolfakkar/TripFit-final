<?php

namespace App\Filament\Resources\AgencyInvitations\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AgencyInvitationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('email')
                    ->label('Invite Email')
                    ->email()
                    ->required(),
            ]);
    }
}
