<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'size',
        'color',
        'price',
        'stock_quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get a formatted name for the variant.
     */
    public function getNameAttribute()
    {
        $parts = [];
        if ($this->size) {
            $parts[] = 'Size: '.$this->size;
        }
        if ($this->color) {
            $parts[] = 'Color: '.$this->color;
        }

        return count($parts) > 0 ? implode(', ', $parts) : 'Standard Option';
    }
}
