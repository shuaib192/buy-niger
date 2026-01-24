<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Seeder: AI Permissions and System Modes
 * CRITICAL: Sets up AI Shadow Mode and Permission Matrix
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AIPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // System Modes - Default to Shadow Mode for safety
        $modes = [
            [
                'mode_type' => 'ai_mode',
                'mode_value' => 'shadow', // live, shadow, simulation_only, off
                'description' => 'Controls AI execution mode. Shadow mode logs actions without executing.',
            ],
            [
                'mode_type' => 'maintenance_mode',
                'mode_value' => 'off',
                'description' => 'When enabled, platform shows maintenance page.',
            ],
        ];

        foreach ($modes as $mode) {
            $mode['created_at'] = now();
            $mode['updated_at'] = now();
            DB::table('system_modes')->insert($mode);
        }

        // AI Permissions Matrix - Hard limits per action
        $permissions = [
            // COO Permissions
            ['ai_role' => 'COO', 'action' => 'pause_product', 'requires_human_approval' => false, 'description' => 'Pause out-of-stock products'],
            ['ai_role' => 'COO', 'action' => 'restock_alert', 'requires_human_approval' => false, 'description' => 'Send restock notifications'],
            ['ai_role' => 'COO', 'action' => 'suspend_vendor', 'requires_human_approval' => true, 'description' => 'Suspend vendor account'],

            // CMO Permissions
            ['ai_role' => 'CMO', 'action' => 'price_change', 'max_percentage' => 5.00, 'requires_human_approval' => false, 'description' => 'Change prices up to 5%'],
            ['ai_role' => 'CMO', 'action' => 'price_change_major', 'max_percentage' => 20.00, 'requires_human_approval' => true, 'description' => 'Change prices 5-20%'],
            ['ai_role' => 'CMO', 'action' => 'create_promotion', 'max_value' => 5000.00, 'requires_human_approval' => false, 'description' => 'Create promotions up to ₦5,000 discount'],
            ['ai_role' => 'CMO', 'action' => 'create_promotion_major', 'max_value' => 50000.00, 'requires_human_approval' => true, 'description' => 'Create promotions ₦5,000-50,000'],
            ['ai_role' => 'CMO', 'action' => 'send_campaign', 'requires_human_approval' => true, 'description' => 'Send email campaigns'],

            // CRO Permissions
            ['ai_role' => 'CRO', 'action' => 'auto_reply', 'requires_human_approval' => false, 'description' => 'Auto-reply to customer messages'],
            ['ai_role' => 'CRO', 'action' => 'refund', 'max_value' => 10000.00, 'requires_human_approval' => false, 'description' => 'Refunds up to ₦10,000'],
            ['ai_role' => 'CRO', 'action' => 'refund_major', 'max_value' => 50000.00, 'requires_human_approval' => true, 'description' => 'Refunds ₦10,000-50,000'],
            ['ai_role' => 'CRO', 'action' => 'refund_large', 'requires_human_approval' => true, 'description' => 'Refunds over ₦50,000'],
            ['ai_role' => 'CRO', 'action' => 'send_notification', 'requires_human_approval' => false, 'description' => 'Send customer notifications'],

            // CFO Permissions
            ['ai_role' => 'CFO', 'action' => 'flag_fraud', 'requires_human_approval' => false, 'description' => 'Flag suspicious transactions'],
            ['ai_role' => 'CFO', 'action' => 'block_transaction', 'requires_human_approval' => true, 'description' => 'Block suspicious transactions'],
            ['ai_role' => 'CFO', 'action' => 'adjust_commission', 'requires_human_approval' => true, 'description' => 'Adjust vendor commission rates'],

            // Supply Chain Permissions
            ['ai_role' => 'SUPPLY_CHAIN', 'action' => 'reorder_suggestion', 'requires_human_approval' => false, 'description' => 'Suggest reorder for low stock'],
            ['ai_role' => 'SUPPLY_CHAIN', 'action' => 'auto_reorder', 'requires_human_approval' => true, 'description' => 'Auto-place reorder'],
        ];

        foreach ($permissions as $permission) {
            $permission['is_enabled'] = true;
            $permission['created_at'] = now();
            $permission['updated_at'] = now();
            DB::table('ai_permissions')->insert($permission);
        }

        // AI Action Limits - Daily/Monthly throttling
        $limits = [
            ['action' => 'price_change', 'daily_limit' => 50, 'monthly_limit' => 500],
            ['action' => 'create_promotion', 'daily_limit' => 10, 'monthly_limit' => 100],
            ['action' => 'refund', 'daily_limit' => 20, 'monthly_limit' => 200],
            ['action' => 'send_notification', 'daily_limit' => 500, 'monthly_limit' => 10000],
            ['action' => 'auto_reply', 'daily_limit' => 1000, 'monthly_limit' => 20000],
            ['action' => 'send_campaign', 'daily_limit' => 5, 'monthly_limit' => 30],
        ];

        foreach ($limits as $limit) {
            $limit['created_at'] = now();
            $limit['updated_at'] = now();
            $limit['hourly_reset_at'] = now()->addHour();
            $limit['daily_reset_at'] = now()->addDay();
            $limit['monthly_reset_at'] = now()->addMonth();
            DB::table('ai_action_limits')->insert($limit);
        }
    }
}
