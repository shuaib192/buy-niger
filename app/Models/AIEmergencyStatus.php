<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIEmergencyStatus extends Model
{
    use HasFactory;
    protected $table = 'ai_emergency_status';

    protected $fillable = [
        'is_active',
        'kill_switch_enabled',
        'kill_switch_reason',
        'triggered_by',
        'triggered_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'kill_switch_enabled' => 'boolean',
        'triggered_at' => 'datetime'
    ];

    public function triggerUser()
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }
}
