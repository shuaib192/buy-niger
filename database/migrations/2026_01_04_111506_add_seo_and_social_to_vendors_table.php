<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Migration: Add SEO and Social to Vendors
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('rating_count');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('facebook')->nullable()->after('meta_description');
            $table->string('twitter')->nullable()->after('facebook');
            $table->string('instagram')->nullable()->after('twitter');
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'facebook', 'twitter', 'instagram']);
        });
    }
};
