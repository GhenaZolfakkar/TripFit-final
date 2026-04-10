<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class Inquiry extends Model
{
     protected $fillable = [
        'user_id',
        'name',
        'email',
        'subject',
        'message',
        'reply',
        'replied_by',
        'status',
    ];

    const STATUS_OPEN = 'open';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_RESOLVED = 'resolved';

    
    protected static function booted()
    {
        static::updated(function ($inquiry) {

            if (
                $inquiry->wasChanged('status') &&
                $inquiry->status === self::STATUS_RESOLVED &&
                !empty(trim($inquiry->reply))
            ) {

                Mail::raw(
                    "Hello {$inquiry->name},\n\n" .

                    "Thank you for contacting TripFit.\n\n" .

                    "Your Message:\n{$inquiry->message}\n\n" .

                    "Our Reply:\n{$inquiry->reply}\n\n" .

                    "If you need more information, feel free to contact us:\n" .
                    "Phone: +20 2 1234 5678\n" .
                    "Email: tripfit.egypt@gmail.com\n\n" .

                    "Best regards,\nTripFit Team",

                    function ($message) use ($inquiry) {
                        $message->to($inquiry->email)
                                ->subject('Reply to your inquiry');
                    }
                );
            }
        });
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function repliedBy()
    {
        return $this->belongsTo(User::class, 'replied_by');
    }
}
