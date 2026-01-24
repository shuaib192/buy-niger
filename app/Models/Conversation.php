<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Model: Conversation
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vendor_id',
        'product_id',
        'subject',
        'last_message_at',
        'user_read_at',
        'vendor_read_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'user_read_at' => 'datetime',
        'vendor_read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    public function getUnreadCountForUserAttribute()
    {
        return $this->messages()
            ->where('sender_type', 'vendor')
            ->where('is_read', false)
            ->count();
    }

    public function getUnreadCountForVendorAttribute()
    {
        return $this->messages()
            ->where('sender_type', 'customer')
            ->where('is_read', false)
            ->count();
    }
}
