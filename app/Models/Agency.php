<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Agency extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'agency_name',
        'logo',
        'description',
        'website',

        // new fields
        'commission_rate',
        'rating',
        'contact_details',
        'business_license',
        'documentation_url',
        'verification_status',
    ];

    // 🔥 AUTO تحويل اليوزر لـ owner
    protected static function booted()
    {
        static::created(function ($agency) {

            $owner = $agency->owner;

            if ($owner) {
                $owner->update([
                    'agency_id' => $agency->id,
                    'type' => 'agency_owner'
                ]);
            }
        });
    }

    // 👤 owner
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // 👥 members
    public function members()
    {
        return $this->hasMany(User::class, 'agency_id');
    }

    // ✈️ trips
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }
}
