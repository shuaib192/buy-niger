<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change 'type' column from ENUM('percentage','fixed') to VARCHAR(255)
        // to allow 'percent' value sent by the controller.
        DB::statement("ALTER TABLE coupons MODIFY COLUMN type VARCHAR(255) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Attempt to revert back to ENUM, warning: this checks data compatibility
        // If 'percent' is in the DB, this will fail or truncate depending on SQL mode.
        // For safety in this fix-forward context, we might skip strict revert or just try.
        // DB::statement("ALTER TABLE coupons MODIFY COLUMN type ENUM('percentage', 'fixed') NOT NULL DEFAULT 'percentage'");
    }
};
