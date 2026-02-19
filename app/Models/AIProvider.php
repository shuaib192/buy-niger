<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIProvider extends Model
{
    use HasFactory;
    
    protected $table = 'ai_providers';
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'credentials',
        'base_url',
        'model',
        'is_active',
        'is_default',
        'capabilities',
        'cost_per_1k_tokens',
        'rate_limit_per_minute',
        'priority'
    ];

    protected $casts = [
        'credentials' => 'array',
        'capabilities' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'cost_per_1k_tokens' => 'decimal:6'
    ];
}
