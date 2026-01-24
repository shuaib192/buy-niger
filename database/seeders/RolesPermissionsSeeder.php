<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Seeder: Roles and Permissions
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $roles = [
            [
                'id' => 1,
                'name' => 'super_admin',
                'display_name' => 'Super Admin',
                'description' => 'Full system access with god mode privileges',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'admin',
                'display_name' => 'Admin',
                'description' => 'Platform administrator with operational authority',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'vendor',
                'display_name' => 'Vendor',
                'description' => 'Store owner with product and order management',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'customer',
                'display_name' => 'Customer',
                'description' => 'End user who shops on the platform',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('roles')->insert($roles);

        // Create permissions
        $permissions = [
            // User management
            ['name' => 'users.view', 'display_name' => 'View Users', 'module' => 'users'],
            ['name' => 'users.create', 'display_name' => 'Create Users', 'module' => 'users'],
            ['name' => 'users.edit', 'display_name' => 'Edit Users', 'module' => 'users'],
            ['name' => 'users.delete', 'display_name' => 'Delete Users', 'module' => 'users'],
            
            // Vendor management
            ['name' => 'vendors.view', 'display_name' => 'View Vendors', 'module' => 'vendors'],
            ['name' => 'vendors.approve', 'display_name' => 'Approve Vendors', 'module' => 'vendors'],
            ['name' => 'vendors.suspend', 'display_name' => 'Suspend Vendors', 'module' => 'vendors'],
            
            // Product management
            ['name' => 'products.view', 'display_name' => 'View Products', 'module' => 'products'],
            ['name' => 'products.create', 'display_name' => 'Create Products', 'module' => 'products'],
            ['name' => 'products.edit', 'display_name' => 'Edit Products', 'module' => 'products'],
            ['name' => 'products.delete', 'display_name' => 'Delete Products', 'module' => 'products'],
            ['name' => 'products.moderate', 'display_name' => 'Moderate Products', 'module' => 'products'],
            
            // Category management
            ['name' => 'categories.manage', 'display_name' => 'Manage Categories', 'module' => 'categories'],
            
            // Order management
            ['name' => 'orders.view', 'display_name' => 'View Orders', 'module' => 'orders'],
            ['name' => 'orders.view_all', 'display_name' => 'View All Orders', 'module' => 'orders'],
            ['name' => 'orders.update', 'display_name' => 'Update Orders', 'module' => 'orders'],
            ['name' => 'orders.cancel', 'display_name' => 'Cancel Orders', 'module' => 'orders'],
            ['name' => 'orders.refund', 'display_name' => 'Refund Orders', 'module' => 'orders'],
            
            // Payment management
            ['name' => 'payments.view', 'display_name' => 'View Payments', 'module' => 'payments'],
            ['name' => 'payments.configure', 'display_name' => 'Configure Payments', 'module' => 'payments'],
            ['name' => 'payouts.manage', 'display_name' => 'Manage Payouts', 'module' => 'payments'],
            
            // Analytics
            ['name' => 'analytics.view', 'display_name' => 'View Analytics', 'module' => 'analytics'],
            ['name' => 'analytics.view_all', 'display_name' => 'View All Analytics', 'module' => 'analytics'],
            
            // AI system
            ['name' => 'ai.configure', 'display_name' => 'Configure AI', 'module' => 'ai'],
            ['name' => 'ai.use', 'display_name' => 'Use AI Features', 'module' => 'ai'],
            ['name' => 'ai.kill_switch', 'display_name' => 'AI Kill Switch', 'module' => 'ai'],
            
            // System settings
            ['name' => 'settings.view', 'display_name' => 'View Settings', 'module' => 'settings'],
            ['name' => 'settings.manage', 'display_name' => 'Manage Settings', 'module' => 'settings'],
            
            // Email campaigns
            ['name' => 'campaigns.manage', 'display_name' => 'Manage Campaigns', 'module' => 'campaigns'],
            
            // Reviews
            ['name' => 'reviews.moderate', 'display_name' => 'Moderate Reviews', 'module' => 'reviews'],
            
            // Audit logs
            ['name' => 'audit.view', 'display_name' => 'View Audit Logs', 'module' => 'audit'],
        ];

        foreach ($permissions as $permission) {
            $permission['created_at'] = now();
            $permission['updated_at'] = now();
            DB::table('permissions')->insert($permission);
        }

        // Assign all permissions to super_admin
        $allPermissions = DB::table('permissions')->pluck('id');
        foreach ($allPermissions as $permissionId) {
            DB::table('role_permissions')->insert([
                'role_id' => 1, // super_admin
                'permission_id' => $permissionId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Assign admin permissions
        $adminPermissions = DB::table('permissions')
            ->whereNotIn('name', ['settings.manage', 'ai.configure', 'ai.kill_switch', 'payments.configure'])
            ->pluck('id');
        foreach ($adminPermissions as $permissionId) {
            DB::table('role_permissions')->insert([
                'role_id' => 2, // admin
                'permission_id' => $permissionId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Assign vendor permissions
        $vendorPermissions = DB::table('permissions')
            ->whereIn('name', [
                'products.view', 'products.create', 'products.edit', 'products.delete',
                'orders.view', 'orders.update',
                'analytics.view',
                'ai.use',
            ])
            ->pluck('id');
        foreach ($vendorPermissions as $permissionId) {
            DB::table('role_permissions')->insert([
                'role_id' => 3, // vendor
                'permission_id' => $permissionId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
