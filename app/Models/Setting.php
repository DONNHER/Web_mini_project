<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        if (!$setting) return $default;

        return match ($setting->type) {
            'json' => json_decode($setting->value, true),
            'boolean' => (bool) $setting->value,
            default => $setting->value,
        };
    }

    public static function set($key, $value, $type = 'string')
    {
        $val = ($type === 'json') ? json_encode($value) : $value;
        return self::updateOrCreate(['key' => $key], ['value' => $val, 'type' => $type]);
    }
}
