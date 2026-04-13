<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
     protected $fillable = [
        'booking_id',
        'amount',
        'currency',
        'transaction_ref',
        'method',
        'paid_at',
        'status',
        'refund_amount',
        'refund_status',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
