<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Agency extends Model
{
    use HasFactory;
    protected $fillable = [
        'owned_id',
        'agency_name',
        'logo',
        'description',
        'website',
    ];
    public function owner()
{
    return $this->belongsTo(User::class, 'owner_id');
}

public function trips()
{
    return $this->hasMany(Trip::class);
}
};
