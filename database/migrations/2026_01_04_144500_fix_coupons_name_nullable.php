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
        Schema::table('coupons', function (Blueprint $table) {
            if (Schema::hasColumn('coupons', 'name')) {
                $table->string('name')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            // We cannot easily revert to non-nullable without data loss if nulls were inserted
            // so we leave it or attempt to change back if we were sure.
            // For safety, we can leave it nullable or do nothing.
             if (Schema::hasColumn('coupons', 'name')) {
                // $table->string('name')->nullable(false)->change(); // Risky
             }
        });
    }
};
