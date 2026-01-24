<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'name',
        'type',
        'path',
        'status',
        'rejection_reason'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
