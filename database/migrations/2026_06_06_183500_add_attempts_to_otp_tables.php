<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('email_otps')) {
            Schema::table('email_otps', function (Blueprint $table) {
                if (!Schema::hasColumn('email_otps', 'attempts')) {
                    $table->integer('attempts')->default(0);
                }
            });
        }

        if (Schema::hasTable('password_reset_tokens')) {
            Schema::table('password_reset_tokens', function (Blueprint $table) {
                if (!Schema::hasColumn('password_reset_tokens', 'attempts')) {
                    $table->integer('attempts')->default(0);
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('email_otps')) {
            Schema::table('email_otps', function (Blueprint $table) {
                if (Schema::hasColumn('email_otps', 'attempts')) {
                    $table->dropColumn('attempts');
                }
            });
        }

        if (Schema::hasTable('password_reset_tokens')) {
            Schema::table('password_reset_tokens', function (Blueprint $table) {
                if (Schema::hasColumn('password_reset_tokens', 'attempts')) {
                    $table->dropColumn('attempts');
                }
            });
        }
    }
};
