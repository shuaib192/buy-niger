<?php

/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 *
 * Model: DisputeMessage
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisputeMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'dispute_id',
        'user_id',
        'message',
        'is_admin',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
    ];

    /**
     * Get the dispute this message belongs to.
     */
    public function dispute()
    {
        return $this->belongsTo(Dispute::class);
    }

    /**
     * Get the user who sent the message.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
