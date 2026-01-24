<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Model: Vendor
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'store_name',
        'store_slug',
        'store_description',
        'logo',
        'banner',
        'primary_color',
        'business_email',
        'business_phone',
        'business_address',
        'city',
        'state',
        'country',
        'status',
        'rejection_reason',
        'commission_rate',
        'total_sales',
        'balance',
        'total_products',
        'total_orders',
        'rating',
        'rating_count',
        'is_featured',
        'approved_at',
        'meta_title',
        'meta_description',
        'facebook',
        'twitter',
        'instagram',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'total_sales' => 'decimal:2',
        'balance' => 'decimal:2',
        'rating' => 'decimal:2',
        'is_featured' => 'boolean',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasManyThrough(Order::class, OrderItem::class, 'vendor_id', 'id', 'id', 'order_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function bankDetails()
    {
        return $this->hasMany(VendorBankDetail::class);
    }

    public function documents()
    {
        return $this->hasMany(VendorDocument::class);
    }

    public function payouts()
    {
        return $this->hasMany(VendorPayout::class);
    }

    public function commissions()
    {
        return $this->hasMany(VendorCommission::class);
    }

    public function reviews()
    {
        return $this->hasMany(VendorReview::class);
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Helpers
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function getLogoUrlAttribute(): string
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->store_name) . '&background=0066FF&color=fff&size=128';
    }

    public function getBannerUrlAttribute(): ?string
    {
        if ($this->banner) {
            return asset('storage/' . $this->banner);
        }
        return null;
    }

    public function getStoreUrlAttribute(): string
    {
        return url('/store/' . $this->store_slug);
    }
}
