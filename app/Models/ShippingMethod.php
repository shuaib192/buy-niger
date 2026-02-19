<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin
 * 
 * Model: ShippingMethod
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    protected $fillable = [
        'name',
        'description',
        'base_cost',
        'per_kg_cost',
        'estimated_days',
        'is_active',
    ];

    protected $casts = [
        'base_cost' => 'decimal:2',
        'per_kg_cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Scope to only active methods.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
