<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Model: SystemSetting
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever("setting.$key", function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            if (!$setting) return $default;

            switch ($setting->type) {
                case 'boolean': return filter_var($setting->value, FILTER_VALIDATE_BOOLEAN);
                case 'number': return (float) $setting->value;
                case 'json': return json_decode($setting->value, true);
                default: return $setting->value;
            }
        });
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, $value, string $group = 'general', string $type = 'string')
    {
        $settingValue = ($type === 'json' || is_array($value)) ? json_encode($value) : (string) $value;
        
        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $settingValue, 'group' => $group, 'type' => $type]
        );

        Cache::forget("setting.$key");
        return $setting;
    }
}
