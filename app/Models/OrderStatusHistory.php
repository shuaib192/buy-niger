<?php

/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 *
 * Model: OrderStatusHistory
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'order_status_history';

    protected $fillable = [
        'order_id',
        'status',
        'notes',
        'changed_by',
        'user_id',
    ];

    /**
     * Get the order this status change belongs to.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user who triggered the status change.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
