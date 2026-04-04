<?php

namespace App\Filament\Resources\AgencyInvitations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AgencyInvitationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->numeric()
                    ->sortable(),
            TextColumn::make('link')
                ->label('Invitation Link')
                ->getStateUsing(fn($record) => url('/admin/accept-invitation?token=' . $record->token))
                ->copyable() // copy-to-clipboard
                ->html()     // optional, allows clickable links
                ->wrap(),
        ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
