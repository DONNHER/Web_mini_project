<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;

use App\Traits\HasPermissions;

class User extends Authenticatable implements MustVerifyEmail, Auditable
{
    use HasFactory, Notifiable, HasPermissions, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role_id',
        'tier',
        'status',
        'avatar',
        'is_comaker',
        'notification_preferences',
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
            'is_comaker' => 'boolean',
            'notification_preferences' => 'array',
        ];
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
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
