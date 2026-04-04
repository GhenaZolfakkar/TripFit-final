<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgencyInvitation extends Model
{
     protected $fillable = [
        'agency_id',
        'email',
        'token',
        'status',
        'expires_at' 
    ];
protected $casts = [
    'expires_at' => 'datetime',
];
public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
}
