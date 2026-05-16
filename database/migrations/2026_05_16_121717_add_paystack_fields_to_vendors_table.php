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
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('paystack_subaccount_id')->nullable()->after('business_name');
            $table->string('bank_name')->nullable()->after('paystack_subaccount_id');
            $table->string('account_number')->nullable()->after('bank_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn(['paystack_subaccount_id', 'bank_name', 'account_number']);
        });
    }
};
