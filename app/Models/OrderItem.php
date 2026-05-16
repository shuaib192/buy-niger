<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Model: OrderItem
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'vendor_id',
        'product_variant_id',
        'product_name',
        'variant_name',
        'price',
        'quantity',
        'subtotal',
        'vendor_commission',
        'platform_commission',
        'status',
        'tracking_number',
        'notes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'vendor_commission' => 'decimal:2',
        'platform_commission' => 'decimal:2',
    ];

    /**
     * Get the order.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the vendor.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
