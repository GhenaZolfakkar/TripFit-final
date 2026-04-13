<?php

namespace App\Filament\Resources\RefundRequests\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables;
use App\Models\Notification;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class RefundRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('booking.id')->label('Booking'),
                TextColumn::make('amount')->money('EGP'),
                TextColumn::make('status')->badge(),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record) {

                        $record->update([
                            'status' => 'approved',
                            'admin_reason' => null,
                        ]);

                        $booking = $record->booking;

              
                        $booking->payment->update([
                            'refund_amount' => $record->amount,
                            'refund_status' => 'refunded',
                        ]);

                  
                        $booking->update([
                            'status' => 'cancelled',
                        ]);

                      
                        Notification::create([
                            'user_id' => $booking->user_id,
                            'title' => 'Refund Approved',
                            'type' => 'refund',
                            'message' => 'Your refund has been approved.',
                            'link' => '/bookings/' . $booking->id,
                        ]);
                    }),


                Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->form([
                        Textarea::make('admin_reason')
                            ->label('Reason')
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {

                        $record->update([
                            'status' => 'rejected',
                            'admin_reason' => $data['admin_reason'],
                        ]);

                        Notification::create([
                            'user_id' => $record->booking->user_id,
                            'title' => 'Refund Rejected',
                            'type' => 'refund',
                            'message' => 'Refund rejected: ' . $data['admin_reason'],
                            'link' => '/bookings/' . $record->booking_id,
                        ]);
                    }),
            ])
            ->filters([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
