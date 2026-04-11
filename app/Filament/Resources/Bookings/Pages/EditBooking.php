<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Filament\Resources\Bookings\BookingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Models\Notification;

class EditBooking extends EditRecord
{
    protected static string $resource = BookingResource::class;
protected function afterSave(): void
{
    $booking = $this->record;
    $trip = $booking->trip;
 
    if ($booking->status === 'confirmed') {
 
        Notification::create([
            'user_id' => $booking->user_id,
            'title' => 'Booking Confirmed',
            'type' => 'booking',
            'message' => 'Your booking for '.$trip->title.' has been confirmed',
            'link' => '/bookings/'.$booking->id
        ]);
 
    }
 
    if ($booking->status === 'cancelled') {
 
        Notification::create([
            'user_id' => $booking->user_id,
            'title' => 'Booking Rejected',
            'type' => 'booking',
            'message' => 'Your booking for '.$trip->title.' has been rejected',
            'link' => '/bookings/'.$booking->id
        ]);
 
    }
}
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
