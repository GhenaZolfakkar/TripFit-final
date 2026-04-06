<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Trip extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'destination',
        'price',
        'duration',
        'max_travelers',
        'start_date',
        'end_date',
        'rating',
        'trip_category_id',
        'agency_id',
        'status',
        'featured'
    ];
    protected $casts = [
        'price' => 'decimal:2',
        'rating' => 'decimal:1',
        'featured' => 'boolean',
        'images' => 'array',
        'videos' => 'array',
    ];
    public function agency()
{
    return $this->belongsTo(Agency::class);
}
    public function category()
    {
        return $this->belongsTo(TripCategory::class,'trip_category_id');
    }
 
    // Trip has many images
    public function images()
    {
        return $this->hasMany(TripImage::class);
    }
 
    // Trip has many services
    public function services()
    {
        return $this->hasMany(TripService::class);
    }
    public function reviews()
{
    return $this->hasMany(Review::class);
}

public function bookings()
{
    return $this->hasMany(Booking::class);
}


}
