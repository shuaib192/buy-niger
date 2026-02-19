<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Migration: Add delivery_fee to vendors table
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->decimal('delivery_fee', 15, 2)->default(0)->after('balance');
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn('delivery_fee');
        });
    }
};
