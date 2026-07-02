<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;

class User extends Authenticatable implements MustVerifyEmail, Auditable
{
    use HasFactory, Notifiable;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'tier',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attributes excluded from the Audit.
     *
     * @var array
     */
    protected $auditExclude = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Helper method
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isVerified()
    {
        return !is_null($this->email_verified_at);
    }

    public function isPremium()
    {
        return $this->tier === 'premium';
    }

    // Add relationship
    public function twoFactorSecret()
    {
        return $this->hasOne(TwoFactorSecret::class);
    }

    // Check if 2FA is enabled
    public function hasTwoFactorEnabled()
    {
        return $this->twoFactorSecret && $this->twoFactorSecret->enabled;
    }
}
