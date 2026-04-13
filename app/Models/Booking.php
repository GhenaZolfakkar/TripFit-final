<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Notification;

class Booking extends Model
{
    use HasFactory;

   protected $fillable = [
    'user_id',
    'trip_id',
    'agency_id',
    'traveler_count',
    'price_per_person',
    'total_price',
    'agency_commission_rate',
    'agency_commission_amount',
    'customer_fee_rate',
    'customer_fee_amount',
    'final_price',
    'status',
    'payment_status',
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
    public function review()
    {
        return $this->hasOne(Review::class);
    }
    public function getAgencyEarningsAttribute()
{
    return $this->total_price - $this->agency_commission_amount;
}
public function payment()
{
    return $this->hasOne(Payment::class);
}

public function tryConfirm()
{
    if ($this->payment_status === 'paid') {

        $this->update([
            'status' => 'confirmed'
        ]);

        Notification::create([
            'user_id' => $this->user_id,
            'title' => 'Booking Confirmed',
            'type' => 'booking',
            'message' => 'Your booking has been confirmed successfully.',
            'link' => '/bookings/' . $this->id
        ]);
    }
}
}