<?php

namespace App\Filament\Resources\Inquiries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;

class InquiriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
             ->columns([
            TextColumn::make('name')->searchable(),
            TextColumn::make('email'),
            TextColumn::make('subject')->limit(30),

            BadgeColumn::make('status')
                ->colors([
                    'danger' => 'open',
                    'warning' => 'in_progress',
                    'success' => 'resolved',
                ]),

            TextColumn::make('created_at')->dateTime(),
        ])

        ->actions([
            EditAction::make('reply')
                ->label('Reply')
                ->icon('heroicon-o-chat-bubble-left-right')

                ->form([
                    Textarea::make('reply')
                        ->label('Reply Message')
                        ->required()
                        ->rows(4),

                    Select::make('status')
                        ->options([
                            'open' => 'Open',
                            'in_progress' => 'In Progress',
                            'resolved' => 'Resolved',
                        ])
                        ->required(),
                ])

                ->action(function ($record, array $data) {

                    if (auth()->user()->type !== 'admin') {
                        abort(403);
                    }

                    $record->update([
                        'reply' => $data['reply'],
                        'status' => $data['status'],
                        'replied_by' => auth()->id(),
                    ]);
                })
        ]);
    }
}
