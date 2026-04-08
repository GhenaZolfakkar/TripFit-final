<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchHistory extends Model
{
     protected $fillable = [
        'user_id',
        'budget',
        'duration',
        'no_of_travelers',
        'trip_category',
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
