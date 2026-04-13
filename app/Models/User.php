<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;


class User extends Authenticatable implements FilamentUser
{
    
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
        'tier',
        'status',
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
  
 public function ownedAgency() {
    return $this->hasOne(Agency::class, 'owner_id');
    }

   public function isAdmin()
{
    return $this->type === 'admin';
}

public function isAgencyOwner()
{
    return $this->type === 'agency_owner';
}

public function bookings()
{
    return $this->hasMany(Booking::class);
}


public function reviews()
{
    return $this->hasMany(Review::class);
}


public function inquiries()
{
    return $this->hasMany(Inquiry::class);
}

/*/ Chat messages
public function chats()
{
    return $this->hasMany(ChatMessage::class);
}*/
public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->type, [
            'admin',
            'agency_owner',
        ]);
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function isBasic()
    {
        return $this->tier === 'basic';
    }

    public function isPremium()
    {
        return $this->tier === 'premium';
    }

    public function isExclusive()
    {
        return $this->tier === 'exclusive';
    }

    public function tierLevel(): int
    {
        return match ($this->tier) {
            'basic' => 1,
            'premium' => 2,
            'exclusive' => 3,
            default => 1,
        };
    }


    public function agencyRate(): float
    {
        return config("tiers.{$this->tier}.agency_rate");
    }

    public function customerFee(): float
    {
        return config("tiers.{$this->tier}.customer_fee");
    }
}
