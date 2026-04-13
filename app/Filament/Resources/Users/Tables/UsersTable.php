<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('middle_name')
                    ->searchable(),
                TextColumn::make('last_name')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge(),

                TextColumn::make('status')
                    ->badge()
                    ->colors([
                          'success' => 'active',
                          'warning' => 'suspended',
                        'danger' => 'blocked',
    ]),    
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('agency_id')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([

    Action::make('activate')
        ->label('Activate')
        ->color('success')
        ->visible(fn ($record) => $record->status !== 'active')
        ->action(function ($record) {
            $record->update([
                'status' => 'active',
            ]);
        }),

        Action::make('suspend')
        ->label('Suspend')
        ->color('warning')
        ->visible(fn ($record) => $record->status !== 'suspended')
        ->action(function ($record) {
            $record->update([
                'status' => 'suspended',
            ]);
        }),

    Action::make('block')
        ->label('Block')
        ->color('danger')
        ->visible(fn ($record) => $record->status !== 'blocked')
        ->requiresConfirmation() 
        ->action(function ($record) {
            $record->update([
                'status' => 'blocked',
            ]);
        }),
])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
