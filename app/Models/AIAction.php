<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIAction extends Model
{
    use HasFactory;
    protected $fillable = [
        'vendor_id',
        'user_id',
        'ai_provider_id',
        'action_type',
        'module',
        'description',
        'input_data',
        'output_data',
        'status',
        'reasoning',
        'was_auto_executed',
        'requires_approval',
        'approved_by',
        'approved_at',
        'executed_at',
        'rolled_back_at',
        'rollback_reason',
        'tokens_used',
        'cost'
    ];

    protected $casts = [
        'input_data' => 'array',
        'output_data' => 'array',
        'was_auto_executed' => 'boolean',
        'requires_approval' => 'boolean',
        'approved_at' => 'datetime',
        'executed_at' => 'datetime',
        'rolled_back_at' => 'datetime',
        'cost' => 'decimal:6'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
    
    public function provider()
    {
        return $this->belongsTo(AIProvider::class, 'ai_provider_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
