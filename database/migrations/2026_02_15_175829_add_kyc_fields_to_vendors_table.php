<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Migration: Add KYC fields to vendors table
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('id_type')->nullable()->after('country'); // national_id, drivers_license, international_passport, voters_card
            $table->string('id_number')->nullable()->after('id_type');
            $table->string('id_document_path')->nullable()->after('id_number'); // uploaded ID scan
            $table->string('nin')->nullable()->after('id_document_path'); // National Identity Number
            $table->string('bvn')->nullable()->after('nin'); // Bank Verification Number
            $table->string('cac_number')->nullable()->after('bvn'); // CAC registration number (optional)
            $table->string('cac_document_path')->nullable()->after('cac_number');
            $table->enum('kyc_status', ['not_submitted', 'pending', 'verified', 'rejected'])->default('not_submitted')->after('cac_document_path');
            $table->text('kyc_rejection_reason')->nullable()->after('kyc_status');
            $table->timestamp('kyc_verified_at')->nullable()->after('kyc_rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn([
                'id_type', 'id_number', 'id_document_path',
                'nin', 'bvn', 'cac_number', 'cac_document_path',
                'kyc_status', 'kyc_rejection_reason', 'kyc_verified_at',
            ]);
        });
    }
};
