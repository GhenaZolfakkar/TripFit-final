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
   /* protected static function booted()
{
    static::creating(function ($category) {
        $user = auth()->user();

        if ($user && $user->type === 'agency_member') {
            $category->agency_id = $user->agency_id;
        }
    });
}*/
     public function trips()
    {
        return $this->hasMany(Trip::class);
    }
}
