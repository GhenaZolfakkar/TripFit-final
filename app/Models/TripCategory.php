<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class TripCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'agency_id',
    ];

     public function trips()
    {
        return $this->hasMany(Trip::class);
    }
}
