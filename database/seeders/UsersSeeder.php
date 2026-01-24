<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Seeder: Test Users
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // Super Admin
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@buyniger.com',
                'email_verified_at' => now(),
                'password' => Hash::make('SuperAdmin@2026'),
                'role_id' => 1,
                'phone' => '08122598372',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Admin
            [
                'name' => 'Admin User',
                'email' => 'admin@buyniger.com',
                'email_verified_at' => now(),
                'password' => Hash::make('Admin@2026'),
                'role_id' => 2,
                'phone' => '07049906420',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Test Vendor User
            [
                'name' => 'Test Vendor',
                'email' => 'vendor@test.com',
                'email_verified_at' => now(),
                'password' => Hash::make('Vendor@2026'),
                'role_id' => 3,
                'phone' => '08012345678',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Test Customer
            [
                'name' => 'Test Customer',
                'email' => 'customer@test.com',
                'email_verified_at' => now(),
                'password' => Hash::make('Customer@2026'),
                'role_id' => 4,
                'phone' => '08087654321',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);

        // Create vendor profile for test vendor
        DB::table('vendors')->insert([
            'user_id' => 3,
            'store_name' => 'Test Store',
            'store_slug' => 'test-store',
            'store_description' => 'This is a test vendor store for demonstration purposes.',
            'business_email' => 'vendor@test.com',
            'business_phone' => '08012345678',
            'city' => 'Lagos',
            'state' => 'Lagos',
            'country' => 'Nigeria',
            'status' => 'approved',
            'commission_rate' => 10.00,
            'approved_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create wallet for test customer
        DB::table('wallets')->insert([
            'user_id' => 4,
            'balance' => 50000.00,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
