<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Seeder: System Settings and Categories
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSeeder extends Seeder
{
    public function run(): void
    {
        // System settings
        $settings = [
            // General
            ['group' => 'general', 'key' => 'site_name', 'value' => 'BuyNiger', 'type' => 'string', 'is_public' => true],
            ['group' => 'general', 'key' => 'site_tagline', 'value' => 'AI-Powered Multi-Vendor Marketplace', 'type' => 'string', 'is_public' => true],
            ['group' => 'general', 'key' => 'site_description', 'value' => 'The future of e-commerce powered by AI', 'type' => 'string', 'is_public' => true],
            ['group' => 'general', 'key' => 'site_logo', 'value' => null, 'type' => 'string', 'is_public' => true],
            ['group' => 'general', 'key' => 'site_favicon', 'value' => null, 'type' => 'string', 'is_public' => true],
            ['group' => 'general', 'key' => 'currency', 'value' => 'NGN', 'type' => 'string', 'is_public' => true],
            ['group' => 'general', 'key' => 'currency_symbol', 'value' => '₦', 'type' => 'string', 'is_public' => true],
            ['group' => 'general', 'key' => 'timezone', 'value' => 'Africa/Lagos', 'type' => 'string', 'is_public' => false],
            ['group' => 'general', 'key' => 'date_format', 'value' => 'd M, Y', 'type' => 'string', 'is_public' => false],
            ['group' => 'general', 'key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean', 'is_public' => false],
            
            // Commission
            ['group' => 'commission', 'key' => 'default_commission_rate', 'value' => '10', 'type' => 'number', 'is_public' => false],
            ['group' => 'commission', 'key' => 'min_payout_amount', 'value' => '5000', 'type' => 'number', 'is_public' => false],
            ['group' => 'commission', 'key' => 'payout_hold_days', 'value' => '7', 'type' => 'number', 'is_public' => false],
            
            // AI
            ['group' => 'ai', 'key' => 'default_provider', 'value' => 'grok', 'type' => 'string', 'is_public' => false],
            ['group' => 'ai', 'key' => 'ai_enabled', 'value' => '1', 'type' => 'boolean', 'is_public' => false],
            ['group' => 'ai', 'key' => 'auto_execute_enabled', 'value' => '0', 'type' => 'boolean', 'is_public' => false],
            ['group' => 'ai', 'key' => 'max_price_change_percent', 'value' => '20', 'type' => 'number', 'is_public' => false],
            
            // Email
            ['group' => 'email', 'key' => 'from_name', 'value' => 'BuyNiger', 'type' => 'string', 'is_public' => false],
            ['group' => 'email', 'key' => 'from_email', 'value' => 'noreply@buyniger.com', 'type' => 'string', 'is_public' => false],
            ['group' => 'email', 'key' => 'support_email', 'value' => 'support@buyniger.com', 'type' => 'string', 'is_public' => true],
            
            // Social
            ['group' => 'social', 'key' => 'facebook_url', 'value' => '', 'type' => 'string', 'is_public' => true],
            ['group' => 'social', 'key' => 'twitter_url', 'value' => '', 'type' => 'string', 'is_public' => true],
            ['group' => 'social', 'key' => 'instagram_url', 'value' => '', 'type' => 'string', 'is_public' => true],
            
            // Contact
            ['group' => 'contact', 'key' => 'phone', 'value' => '08122598372', 'type' => 'string', 'is_public' => true],
            ['group' => 'contact', 'key' => 'address', 'value' => 'Nigeria', 'type' => 'string', 'is_public' => true],
        ];

        foreach ($settings as $setting) {
            $setting['created_at'] = now();
            $setting['updated_at'] = now();
            DB::table('system_settings')->updateOrInsert(['key' => $setting['key']], $setting);
        }

        // Feature toggles
        $features = [
            ['feature' => 'vendor_registration', 'display_name' => 'Vendor Registration', 'is_enabled' => true],
            ['feature' => 'customer_reviews', 'display_name' => 'Customer Reviews', 'is_enabled' => true],
            ['feature' => 'wallet_system', 'display_name' => 'Wallet System', 'is_enabled' => true],
            ['feature' => 'coupons', 'display_name' => 'Coupon System', 'is_enabled' => true],
            ['feature' => 'wishlist', 'display_name' => 'Wishlist', 'is_enabled' => true],
            ['feature' => 'ai_assistant', 'display_name' => 'AI Assistant', 'is_enabled' => true],
            ['feature' => 'ai_auto_actions', 'display_name' => 'AI Auto Actions', 'is_enabled' => false],
            ['feature' => 'email_campaigns', 'display_name' => 'Email Campaigns', 'is_enabled' => true],
        ];

        foreach ($features as $feature) {
            $feature['created_at'] = now();
            $feature['updated_at'] = now();
            DB::table('feature_toggles')->updateOrInsert(['feature' => $feature['feature']], $feature);
        }

        // Default categories
        $categories = [
            ['name' => 'Electronics', 'slug' => 'electronics', 'icon' => 'fa-laptop', 'sort_order' => 1],
            ['name' => 'Fashion', 'slug' => 'fashion', 'icon' => 'fa-tshirt', 'sort_order' => 2],
            ['name' => 'Home & Garden', 'slug' => 'home-garden', 'icon' => 'fa-home', 'sort_order' => 3],
            ['name' => 'Health & Beauty', 'slug' => 'health-beauty', 'icon' => 'fa-heart', 'sort_order' => 4],
            ['name' => 'Sports & Outdoors', 'slug' => 'sports-outdoors', 'icon' => 'fa-futbol', 'sort_order' => 5],
            ['name' => 'Automotive', 'slug' => 'automotive', 'icon' => 'fa-car', 'sort_order' => 6],
            ['name' => 'Books & Media', 'slug' => 'books-media', 'icon' => 'fa-book', 'sort_order' => 7],
            ['name' => 'Food & Groceries', 'slug' => 'food-groceries', 'icon' => 'fa-utensils', 'sort_order' => 8],
            ['name' => 'Toys & Games', 'slug' => 'toys-games', 'icon' => 'fa-gamepad', 'sort_order' => 9],
            ['name' => 'Services', 'slug' => 'services', 'icon' => 'fa-concierge-bell', 'sort_order' => 10],
        ];

        foreach ($categories as $category) {
            $category['is_active'] = true;
            $category['created_at'] = now();
            $category['updated_at'] = now();
            DB::table('categories')->updateOrInsert(['slug' => $category['slug']], $category);
        }

        // Default shipping methods
        $shippingMethods = [
            ['name' => 'Pickup', 'description' => 'Pick up your order from the vendor\'s location for free', 'base_cost' => 0.00, 'estimated_days' => '1 day'],
            ['name' => 'Vendor Shipping', 'description' => 'Vendor ships/waybills your order to you. Delivery fee set by vendor.', 'base_cost' => 0.00, 'estimated_days' => '2-5 days'],
        ];

        foreach ($shippingMethods as $method) {
            $method['is_active'] = true;
            $method['created_at'] = now();
            $method['updated_at'] = now();
            DB::table('shipping_methods')->updateOrInsert(['name' => $method['name']], $method);
        }

        // AI Providers
        $aiProviders = [
            [
                'name' => 'grok',
                'display_name' => 'Grok (xAI)',
                'description' => 'Default AI provider - Grok by xAI',
                'base_url' => 'https://api.x.ai/v1',
                'model' => 'grok-2-latest',
                'is_active' => false,
                'is_default' => true,
                'capabilities' => json_encode(['chat', 'reasoning']),
                'priority' => 1,
            ],
            [
                'name' => 'openai',
                'display_name' => 'OpenAI GPT',
                'description' => 'OpenAI GPT-4 / GPT-3.5',
                'base_url' => 'https://api.openai.com/v1',
                'model' => 'gpt-4-turbo-preview',
                'is_active' => false,
                'is_default' => false,
                'capabilities' => json_encode(['chat', 'vision', 'embeddings']),
                'priority' => 2,
            ],
            [
                'name' => 'gemini',
                'display_name' => 'Google Gemini',
                'description' => 'Google Gemini Pro',
                'base_url' => 'https://generativelanguage.googleapis.com/v1beta',
                'model' => 'gemini-pro',
                'is_active' => false,
                'is_default' => false,
                'capabilities' => json_encode(['chat', 'vision']),
                'priority' => 3,
            ],
        ];

        foreach ($aiProviders as $provider) {
            $provider['created_at'] = now();
            $provider['updated_at'] = now();
            DB::table('ai_providers')->updateOrInsert(['name' => $provider['name']], $provider);
        }

        // Payment Gateways
        $gateways = [
            [
                'name' => 'paystack',
                'display_name' => 'Paystack',
                'description' => 'Pay with card, bank transfer, or mobile money',
                'supports_split' => true,
                'is_active' => false,
                'is_test_mode' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'flutterwave',
                'display_name' => 'Flutterwave',
                'description' => 'Pay with card, bank transfer, USSD, or mobile money',
                'supports_split' => true,
                'is_active' => false,
                'is_test_mode' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'stripe',
                'display_name' => 'Stripe',
                'description' => 'International payments with card',
                'supports_split' => true,
                'is_active' => false,
                'is_test_mode' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'wallet',
                'display_name' => 'Wallet',
                'description' => 'Pay from your BuyNiger wallet balance',
                'supports_split' => false,
                'is_active' => true,
                'is_test_mode' => false,
                'sort_order' => 4,
            ],
        ];

        foreach ($gateways as $gateway) {
            $gateway['created_at'] = now();
            $gateway['updated_at'] = now();
            DB::table('payment_gateways')->updateOrInsert(['name' => $gateway['name']], $gateway);
        }

        // Email templates
        $templates = [
            [
                'name' => 'welcome',
                'subject' => 'Welcome to BuyNiger! 🎉',
                'body' => '
<div style="max-width:600px;margin:0 auto;font-family:Inter,Arial,sans-serif;background:#f8fafc;">
    <div style="background:linear-gradient(135deg,#0f172a,#1e40af);padding:40px 32px;text-align:center;border-radius:0 0 24px 24px;">
        <h1 style="color:white;font-size:28px;margin:0 0 4px;">Buy<span style="color:#60a5fa;">Niger</span></h1>
        <p style="color:rgba(255,255,255,0.6);font-size:12px;margin:0;">AI-Powered Marketplace</p>
    </div>
    <div style="padding:36px 32px;">
        <div style="text-align:center;margin-bottom:28px;">
            <div style="width:64px;height:64px;background:linear-gradient(135deg,#10b981,#059669);border-radius:50%;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;">
                <span style="color:white;font-size:28px;">✓</span>
            </div>
            <h2 style="font-size:22px;color:#1e293b;margin:0 0 8px;">Welcome, {customer_name}!</h2>
            <p style="color:#64748b;font-size:15px;margin:0;">Your account has been created successfully.</p>
        </div>
        <div style="background:white;border-radius:16px;padding:24px;border:1px solid #e2e8f0;margin-bottom:24px;">
            <p style="color:#374151;font-size:14px;line-height:1.7;margin:0 0 16px;">You&rsquo;re now part of Nigeria&rsquo;s smartest marketplace. Here&rsquo;s what you can do:</p>
            <table width="100%" style="border-collapse:collapse;">
                <tr><td style="padding:10px 0;border-bottom:1px solid #f1f5f9;font-size:14px;color:#1e293b;">🛍️ <strong>Shop</strong> thousands of products from trusted vendors</td></tr>
                <tr><td style="padding:10px 0;border-bottom:1px solid #f1f5f9;font-size:14px;color:#1e293b;">📦 <strong>Track</strong> your orders in real-time</td></tr>
                <tr><td style="padding:10px 0;border-bottom:1px solid #f1f5f9;font-size:14px;color:#1e293b;">💬 <strong>Chat</strong> directly with vendors</td></tr>
                <tr><td style="padding:10px 0;font-size:14px;color:#1e293b;">⭐ <strong>Review</strong> products and help the community</td></tr>
            </table>
        </div>
        <div style="text-align:center;margin-bottom:24px;">
            <a href="http://127.0.0.1:8000/shop" style="display:inline-block;background:linear-gradient(135deg,#3b82f6,#1d4ed8);color:white;padding:14px 36px;border-radius:12px;text-decoration:none;font-weight:700;font-size:15px;">Start Shopping Now →</a>
        </div>
        <p style="text-align:center;color:#94a3b8;font-size:13px;margin:0;">Your login email: <strong>{email}</strong></p>
    </div>
    <div style="text-align:center;padding:24px;border-top:1px solid #e2e8f0;">
        <p style="color:#94a3b8;font-size:12px;margin:0;">© 2026 BuyNiger. Built by Shuaibu Abdulmumin | P3 Consulting Limited</p>
    </div>
</div>',
                'variables' => json_encode(['customer_name', 'email']),
            ],
            [
                'name' => 'order_confirmation',
                'subject' => 'Order Confirmed - #{order_number}',
                'body' => '<h1>Order Confirmed!</h1><p>Hi {customer_name}, your order #{order_number} has been confirmed.</p>',
                'variables' => json_encode(['customer_name', 'order_number', 'order_total', 'order_items']),
            ],
            [
                'name' => 'order_shipped',
                'subject' => 'Your Order is on the Way - #{order_number}',
                'body' => '<h1>Order Shipped!</h1><p>Your order #{order_number} is on its way. Track it here: {tracking_url}</p>',
                'variables' => json_encode(['customer_name', 'order_number', 'tracking_number', 'tracking_url']),
            ],
            [
                'name' => 'order_delivered',
                'subject' => 'Order Delivered - #{order_number}',
                'body' => '<h1>Order Delivered!</h1><p>Your order #{order_number} has been delivered. Enjoy!</p>',
                'variables' => json_encode(['customer_name', 'order_number']),
            ],
            [
                'name' => 'vendor_approved',
                'subject' => 'Your Vendor Application is Approved!',
                'body' => '<h1>Congratulations, {vendor_name}!</h1><p>Your vendor application has been approved. Start selling now!</p>',
                'variables' => json_encode(['vendor_name', 'store_name']),
            ],
            [
                'name' => 'password_reset',
                'subject' => 'Reset Your Password',
                'body' => '<h1>Password Reset</h1><p>Click the link below to reset your password: {reset_link}</p>',
                'variables' => json_encode(['customer_name', 'reset_link']),
            ],
        ];

        foreach ($templates as $template) {
            $template['is_active'] = true;
            $template['created_at'] = now();
            $template['updated_at'] = now();
            DB::table('email_templates')->updateOrInsert(['name' => $template['name']], $template);
        }

        // AI Emergency Status (default active)
        DB::table('ai_emergency_status')->insert([
            'is_active' => true,
            'kill_switch_enabled' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
