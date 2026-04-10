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
        'commission_rate',
        'rating',
        'contact_details',
        'business_license',
        'documentation_url',
        'verification_status',
    ];

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


    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

  
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }
}
