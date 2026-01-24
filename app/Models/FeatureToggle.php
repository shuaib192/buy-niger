<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Model: FeatureToggle
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class FeatureToggle extends Model
{
    use HasFactory;

    protected $fillable = [
        'feature',
        'display_name',
        'description',
        'is_enabled',
        'config',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'config' => 'json',
    ];

    /**
     * Check if a feature is enabled.
     */
    public static function isEnabled(string $feature): bool
    {
        return Cache::rememberForever("feature.$feature", function () use ($feature) {
            return self::where('feature', $feature)->where('is_enabled', true)->exists();
        });
    }
}
