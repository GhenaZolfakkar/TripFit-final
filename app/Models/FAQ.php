<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FAQ extends Model
{
    protected $fillable = [
        'question',
        'answer',
        'category',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
