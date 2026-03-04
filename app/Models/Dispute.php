<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Model: Dispute
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'vendor_id',
        'subject',
        'description',
        'status',
        'priority',
        'resolution_notes',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    // Status constants
    const STATUS_OPEN = 'open';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_ESCALATED = 'escalated';
    const STATUS_CLOSED = 'closed';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function messages()
    {
        return $this->hasMany(DisputeMessage::class)->orderBy('created_at', 'asc');
    }

    /**
     * Get the status badge color.
     */
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'open' => 'primary',
            'in_progress' => 'info',
            'resolved' => 'success',
            'escalated' => 'danger',
            'closed' => 'secondary',
        ];
        return $badges[$this->status] ?? 'secondary';
    }

    /**
     * Get the priority badge color.
     */
    public function getPriorityBadgeAttribute(): string
    {
        $badges = [
            'critical' => 'danger',
            'high' => 'warning',
            'medium' => 'info',
            'low' => 'secondary',
        ];
        return $badges[$this->priority] ?? 'secondary';
    }
}
