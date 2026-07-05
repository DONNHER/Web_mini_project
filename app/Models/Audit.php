<?php

namespace App\Models;

use OwenIt\Auditing\Models\Audit as BaseAudit;
use Illuminate\Support\Str;
use App\Notifications\SecurityAlert;
use Illuminate\Support\Facades\Notification;

class Audit extends BaseAudit
{
    protected $table = 'audits';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    public function getKeyName()
    {
        return 'id';
    }

    public function getRouteKeyName()
    {
        return 'id';
    }

    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
    ];

    /**
     * Define a clear relationship for the user who performed the action.
     */
    public function performer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function booted()
    {
        static::creating(function ($audit) {
            if (empty($audit->id)) {
                $audit->id = (string) Str::uuid();
            }

            if (!$audit->created_at) {
                $audit->created_at = now();
            }

            $audit->checksum = static::generateChecksum($audit);
        });

        static::created(function ($audit) {
            if ($audit->isCriticalEvent()) {
                $admins = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->get();
                if ($admins->count() > 0) {
                    Notification::send($admins, new SecurityAlert($audit));
                }
            }
        });

        static::updating(function ($audit) {
            throw new \Exception('Audit logs are tamper-proof and cannot be updated.');
        });
    }

    public static function generateChecksum($audit)
    {
        $createdAt = $audit->created_at;
        if ($createdAt instanceof \DateTimeInterface) {
            $createdAt = $createdAt->format('Y-m-d H:i:s');
        }

        $data = [
            $audit->user_id,
            $audit->event,
            $audit->auditable_type,
            $audit->auditable_id,
            is_array($audit->old_values) ? json_encode($audit->old_values) : $audit->old_values,
            is_array($audit->new_values) ? json_encode($audit->new_values) : $audit->new_values,
            $audit->ip_address,
            $audit->user_agent,
            $createdAt,
        ];

        return hash('sha256', implode('|', $data) . config('app.key'));
    }

    public function isValid()
    {
        return $this->checksum === static::generateChecksum($this);
    }

    public function isCriticalEvent()
    {
        $criticalEvents = ['login_failed', 'deleted', 'password_reset', 'error_logged'];

        if (in_array($this->event, $criticalEvents)) {
            return true;
        }

        // Suspicious: Changing role
        if ($this->auditable_type === User::class && $this->event === 'updated') {
            if (isset($this->new_values['role_id'])) {
                return true;
            }
        }

        // Suspicious: Large interest rate jump
        if ($this->auditable_type === LoanProduct::class && $this->event === 'updated') {
            $oldRate = $this->old_values['interest_rate'] ?? 0;
            $newRate = $this->new_values['interest_rate'] ?? 0;
            if ($oldRate > 0 && $newRate > ($oldRate * 1.5)) {
                return true;
            }
        }

        return false;
    }
}
