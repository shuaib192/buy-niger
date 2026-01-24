<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Model: VendorBankDetail
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorBankDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'bank_name',
        'account_name',
        'account_number',
        'bank_code',
        'is_verified',
        'is_primary',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_primary' => 'boolean',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
