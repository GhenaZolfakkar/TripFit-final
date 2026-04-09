<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;


class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'middle_name',
        'last_name',
        'phone',
        'date_of_birth',
        'type',
        'agency_id',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    // Agency owner
    // 👑 owner
 public function ownedAgency() {
    return $this->hasOne(Agency::class, 'owner_id');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }
// User bookings
public function bookings()
{
    return $this->hasMany(Booking::class);
}

// User reviews
public function reviews()
{
    return $this->hasMany(Review::class);
}

    // User inquiries
    /*public function inquiries()
{
    return $this->hasMany(Inquiry::class);
}

// Chat messages
public function chats()
{
    return $this->hasMany(ChatMessage::class);
}
public function canAccessPanel(Panel $panel): bool
{
    return in_array($this->type, ['admin','agency']);
}*/
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
