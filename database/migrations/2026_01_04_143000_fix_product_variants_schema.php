<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            // Drop old columns found in the original 2024 migration
            // We use checks to avoid errors if they are already gone
            if (Schema::hasColumn('product_variants', 'name')) $table->dropColumn('name');
            if (Schema::hasColumn('product_variants', 'value')) $table->dropColumn('value');
            if (Schema::hasColumn('product_variants', 'price_adjustment')) $table->dropColumn('price_adjustment');
            if (Schema::hasColumn('product_variants', 'quantity')) $table->dropColumn('quantity');
            if (Schema::hasColumn('product_variants', 'image')) $table->dropColumn('image');
            if (Schema::hasColumn('product_variants', 'is_active')) $table->dropColumn('is_active');

            // Add new required columns
            if (!Schema::hasColumn('product_variants', 'size')) $table->string('size')->nullable();
            if (!Schema::hasColumn('product_variants', 'color')) $table->string('color')->nullable();
            if (!Schema::hasColumn('product_variants', 'price')) $table->decimal('price', 10, 2)->nullable();
            if (!Schema::hasColumn('product_variants', 'stock_quantity')) $table->integer('stock_quantity')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            // We won't try to perfectly restore the old schema as it was incompatible
            // but we can drop the new columns to be clean.
            $table->dropColumn(['size', 'color', 'price', 'stock_quantity']);
        });
    }
};
