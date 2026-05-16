<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Model: Product
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'category_id',
        'name',
        'slug',
        'short_description',
        'description',
        'sku',
        'price',
        'sale_price',
        'cost_price',
        'quantity',
        'low_stock_threshold',
        'unit',
        'weight',
        'dimensions',
        'status',
        'is_featured',
        'is_digital',
        'digital_file',
        'view_count',
        'order_count',
        'rating',
        'rating_count',
        'meta_data',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'dimensions' => 'array',
        'meta_data' => 'array',
        'is_featured' => 'boolean',
        'is_digital' => 'boolean',
        'rating' => 'decimal:2',
    ];

    // Relationships
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tags');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function priceHistory()
    {
        return $this->hasMany(PriceHistory::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInStock($query)
    {
        return $query->where('quantity', '>', 0);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOnSale($query)
    {
        return $query->whereNotNull('sale_price')
                     ->whereColumn('sale_price', '<', 'price');
    }

    // Helpers
    public function getCurrentPriceAttribute(): float
    {
        if ($this->sale_price && $this->sale_price < $this->price) {
            return $this->sale_price;
        }
        return $this->price;
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        if ($this->sale_price && $this->sale_price < $this->price) {
            return (int) round((($this->price - $this->sale_price) / $this->price) * 100);
        }
        return null;
    }

    public function isInStock(): bool
    {
        return $this->quantity > 0;
    }

    public function isLowStock(): bool
    {
        return $this->quantity > 0 && $this->quantity <= $this->low_stock_threshold;
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        $image = $this->images()->where('is_primary', true)->first() 
                 ?? $this->images()->first();
        
        $path = $image ? $image->image_path : $this->image_path;

        if (empty($path)) {
            return asset('images/no-product.svg');
        }

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return asset('storage/' . $path);
    }

    public function getFormattedPriceAttribute(): string
    {
        return '₦' . number_format($this->current_price, 2);
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }
}
