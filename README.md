# 🛒 BuyNiger — AI-First Multi-Vendor E-Commerce Platform

> A production-grade, AI-driven multi-vendor marketplace built with **Laravel 10** (PHP 8.1+), **MySQL**, and a pluggable **AI operating system** (Groq / Gemini / OpenAI). Designed and developed by **Shuaibu Abdulmumin**.

![Laravel](https://img.shields.io/badge/Laravel-10.x-red?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.1+-blue?style=flat-square&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-orange?style=flat-square&logo=mysql)
![AI](https://img.shields.io/badge/AI-Groq%20%7C%20Gemini%20%7C%20OpenAI-green?style=flat-square)
![Paystack](https://img.shields.io/badge/Payments-Paystack-blueviolet?style=flat-square)
![Sanctum](https://img.shields.io/badge/Auth-Sanctum-purple?style=flat-square)

---

## Table of Contents

1. [Product Vision](#-product-vision)
2. [Tech Stack](#-tech-stack)
3. [User Roles & Access Control](#-user-roles--access-control)
4. [Project Structure](#-project-structure)
5. [Getting Started](#-getting-started)
6. [Environment Variables](#-environment-variables)
7. [Database & Models](#-database--models)
8. [Routing Reference](#-routing-reference)
9. [Middleware](#-middleware)
10. [AI Commerce Operating System (AICOS)](#-ai-commerce-operating-system-aicos)
11. [Payment System](#-payment-system)
12. [Background Jobs & Queues](#-background-jobs--queues)
13. [Email & Notifications](#-email--notifications)
14. [Frontend & Views](#-frontend--views)
15. [Mobile App API](#-mobile-app-api)
16. [Testing](#-testing)
17. [Deployment Notes](#-deployment-notes)
18. [Contributing](#-contributing)
19. [License](#-license)

---

## 🎯 Product Vision

BuyNiger is **not just a marketplace** — it's an **AI-Driven Commerce Operating System** where:

- Multiple independent **vendors** operate stores on a single platform.
- Each vendor is assisted by a powerful **AI** acting as a full C-Suite (COO, CMO, CRO, CFO, Supply Chain Manager).
- **Platform owners** (Super Admin) maintain absolute governance, configurability, and control.
- **Customers** experience intelligent, conversational, personalized commerce.

The AI is always on, action-capable, policy-governed, auditable, and reversible.

---

## 🧱 Tech Stack

| Layer            | Technology                                      |
| ---------------- | ----------------------------------------------- |
| **Framework**    | Laravel 10.x                                    |
| **Language**     | PHP 8.1+                                        |
| **Database**     | MySQL 8.0+ (InnoDB, utf8mb4)                    |
| **Auth**         | Laravel Sanctum (token-based for API)            |
| **Frontend**     | Blade templates, Bootstrap 5, jQuery             |
| **Build Tool**   | Vite                                             |
| **AI Providers** | Groq (Llama 3.3 70B), Google Gemini, OpenAI      |
| **Payments**     | Paystack (with Paystack Transfer for payouts)     |
| **Email**        | SMTP (Titan Email via SSL on port 465)            |
| **Queue**        | Sync (default) — configurable to Redis/Database   |
| **Caching**      | File (default) — configurable to Redis            |
| **Server**       | XAMPP (local dev) / Any LEMP/LAMP stack            |

---

## 👥 User Roles & Access Control

The platform uses a numeric **role_id** system on the `users` table. All access control is enforced via the `role` middleware.

| role_id | Role           | Prefix       | Middleware                         | Description                                                |
| ------- | -------------- | ------------ | ---------------------------------- | ---------------------------------------------------------- |
| **1**   | Super Admin    | `/superadmin`| `auth`, `role:1`                   | God mode — full system control, AI config, payment gateways, audit logs, kill switch |
| **2**   | Admin          | `/admin`     | `auth`, `role:2`                   | Operational admin — vendor approvals, product moderation, disputes, orders |
| **3**   | Vendor         | `/vendor`    | `auth`, `role:3`, `vendor.approved`| Store owner — products, orders, analytics, finances, messaging |
| **4**   | Customer       | `/account`   | `auth`, `role:4`                   | End user — browse, buy, review, wishlist, track orders, messaging |

### Role Hierarchy

```
Super Admin (1)
  └── Can do everything Admin can, plus:
      ├── System settings & feature toggles
      ├── AI provider configuration & kill switch
      ├── Payment gateway configuration
      ├── Email settings & templates
      ├── Payout approvals
      └── Audit logs

Admin (2)
  └── Operational tasks:
      ├── Vendor approval / rejection / suspension
      ├── Product moderation & featuring
      ├── Dispute resolution
      ├── Order monitoring & status overrides
      └── User management (ban/unban)

Vendor (3)
  └── Requires approved vendor profile:
      ├── Store branding (logo, banner, colors)
      ├── Product CRUD with variants & images
      ├── Inventory management
      ├── Order processing & status updates
      ├── Analytics & export
      ├── Coupon management
      ├── Finance & payout requests
      └── Customer messaging

Customer (4)
  └── Standard e-commerce:
      ├── Browse catalog & vendor stores
      ├── Cart, wishlist, checkout
      ├── Order tracking & history
      ├── Product reviews
      ├── Address management
      ├── Vendor messaging
      └── Dispute filing
```

---

## 📁 Project Structure

```
buy-niger/
├── app/
│   ├── Console/                  # Artisan commands
│   ├── Events/                   # Event classes (e.g., AIActionExecuted)
│   ├── Exceptions/               # Exception handlers
│   ├── Http/
│   │   ├── Controllers/          # 18 controllers (see below)
│   │   ├── Kernel.php            # Middleware registration
│   │   └── Middleware/           # 12 middleware classes
│   ├── Jobs/                     # 9 queued jobs (AI, payments, email, etc.)
│   ├── Listeners/                # Event listeners
│   ├── Mail/                     # Mailable classes (OrderConfirmation, NewOrderNotification)
│   ├── Models/                   # 40 Eloquent models
│   ├── Providers/                # Service providers
│   └── Services/
│       ├── AI/
│       │   ├── AIService.php            # Core AI service (Groq API)
│       │   ├── AIProviderInterface.php  # Contract for all AI providers
│       │   ├── AIDataHelper.php         # Context builder for AI prompts
│       │   ├── AIActionHelper.php       # AI action execution logic (71KB!)
│       │   ├── Modules/
│       │   │   ├── COOModule.php        # Chief Operating Officer
│       │   │   ├── CMOModule.php        # Chief Marketing Officer
│       │   │   ├── CROModule.php        # Chief Revenue/Relations Officer
│       │   │   ├── CFOModule.php        # Chief Financial Officer
│       │   │   └── SupplyChainModule.php
│       │   └── Providers/
│       │       ├── GeminiProvider.php    # Google Gemini integration
│       │       ├── GroqProvider.php      # Groq (Llama) integration
│       │       └── OpenAIProvider.php    # OpenAI integration
│       ├── MetricsService.php           # System health & observability
│       └── PaystackTransferService.php  # Paystack payout transfers
├── bootstrap/
├── config/                       # Laravel config files + sanctum.php
├── database/
│   ├── factories/
│   ├── migrations/               # 33 migration files
│   └── seeders/
├── public/
│   ├── css/                      # Compiled CSS
│   ├── js/                       # Compiled JS
│   ├── images/                   # Static images
│   └── index.php                 # Application entry point
├── resources/
│   ├── css/                      # Source CSS
│   ├── js/                       # Source JS
│   └── views/
│       ├── admin/                # Admin dashboard view
│       ├── auth/                 # Login, register, forgot/reset password, verify email, vendor register
│       ├── components/           # Reusable Blade components
│       ├── customer/             # Customer dashboard, profile, addresses, messages, reviews
│       ├── emails/               # Email templates (order confirmation, new order notification)
│       ├── layouts/              # 4 layout files: app, auth, shop, vendor
│       ├── notifications/        # Notification views
│       ├── partials/             # Shared partials
│       ├── shop/                 # 20 public shop views (home, catalog, product, cart, checkout, etc.)
│       ├── superadmin/           # 14 subdirectories (dashboard, AI, analytics, vendors, users, etc.)
│       ├── vendor/               # 8 views + 6 subdirectories (dashboard, products, orders, etc.)
│       └── welcome.blade.php     # Landing page
├── routes/
│   ├── web.php                   # 395 lines — all web routes
│   ├── api.php                   # API routes (Sanctum)
│   ├── channels.php              # Broadcast channels
│   └── console.php               # Console routes
├── storage/                      # Logs, cache, uploads
├── tests/                        # PHPUnit tests
├── .env                          # Environment configuration
├── .htaccess                     # Apache URL rewriting
├── artisan                       # Laravel CLI
├── buyniger.sql                  # Full database dump (141KB)
├── composer.json                 # PHP dependencies
├── database_updates.sql          # Incremental DB patches
├── implementation_plan.txt       # Full 9-phase development roadmap
├── MOBILE_APP_API_DOCUMENTATION.txt  # Complete mobile API spec (55 screens, 90+ endpoints)
├── package.json                  # Node dependencies
├── PRD.txt                       # Product Requirements Document
└── vite.config.js                # Vite build config
```

---

## 🚀 Getting Started

### Prerequisites

- **PHP** 8.1 or higher
- **Composer** 2.x
- **MySQL** 8.0+
- **Node.js** 18+ & npm (for Vite asset compilation)
- **XAMPP** or any LAMP/LEMP stack (Apache/Nginx + PHP + MySQL)

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/shuaib192/buy-niger.git
cd buy-niger

# 2. Install PHP dependencies
composer install

# 3. Copy environment file
cp .env.example .env
# (or create .env manually — see Environment Variables below)

# 4. Generate application key
php artisan key:generate

# 5. Create the database
mysql -u root -e "CREATE DATABASE buyniger CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 6. Option A: Run migrations
php artisan migrate

# 6. Option B: Import the full dump (includes seed data)
mysql -u root buyniger < buyniger.sql

# 7. Create the storage symlink
php artisan storage:link

# 8. Install Node dependencies & build assets
npm install
npm run build

# 9. Start the development server
php artisan serve
# App available at http://127.0.0.1:8000
```

### Default Test Accounts

| Role        | Email                   | Password    |
| ----------- | ----------------------- | ----------- |
| Customer    | test@example.com        | password    |
| Vendor      | vendor@example.com      | password    |
| Admin       | admin@buyniger.com      | admin001    |

---

## ⚙️ Environment Variables

Create a `.env` file in the project root. Key variables:

```env
# ── Application ──────────────────────────
APP_NAME=BuyNiger
APP_ENV=local              # local | production
APP_DEBUG=true             # false in production
APP_URL=https://buyniger.com

# ── Database ─────────────────────────────
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=buyniger
DB_USERNAME=root
DB_PASSWORD=

# ── Cache & Queue ────────────────────────
CACHE_DRIVER=file          # file | redis
QUEUE_CONNECTION=sync      # sync | database | redis
SESSION_DRIVER=file

# ── Mail (SMTP) ──────────────────────────
MAIL_MAILER=smtp
MAIL_HOST=smtp.titan.email
MAIL_PORT=465
MAIL_USERNAME=noreply@buyniger.com
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@buyniger.com
MAIL_FROM_NAME=BuyNiger

# ── AI Provider (at least one required for AI features) ──
GROQ_API_KEY=your_groq_api_key

# ── Payments ─────────────────────────────
PAYSTACK_PUBLIC_KEY=pk_test_...
PAYSTACK_SECRET_KEY=sk_test_...

# ── Redis (if using redis driver) ────────
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

> **Note:** AI providers can also be configured dynamically through the Super Admin UI at `/superadmin/ai/settings`. The `AIProvider` model stores per-provider credentials in the database.

---

## 🗄️ Database & Models

### Entity-Relationship Overview

```
┌─────────┐    ┌──────────┐    ┌──────────┐    ┌───────────┐
│  User   │───→│  Vendor  │───→│ Product  │───→│ OrderItem │
│(role_id)│    │(store)   │    │(catalog) │    │           │
└────┬────┘    └────┬─────┘    └────┬─────┘    └─────┬─────┘
     │              │               │                │
     │              │               ├── ProductImage  │
     │              │               ├── ProductVariant │
     │              │               ├── PriceHistory   │
     │              │               └── Review         │
     │              │                                  │
     │              ├── VendorBankDetail               │
     │              ├── VendorDocument                 │
     │              ├── VendorPayout                   │
     │              └── Coupon                         │
     │                                                 │
     ├── Order ─────────────────────────────────────────┘
     │   ├── OrderStatusHistory
     │   └── Dispute ── DisputeMessage
     │
     ├── Cart ── CartItem
     ├── Wishlist
     ├── Address
     ├── Review
     ├── Notification
     └── AIChatSession ── AIChatMessage
```

### All 40 Models

| Category         | Model               | Table                    | Key Fields                                                                       |
| ---------------- | -------------------- | ------------------------ | -------------------------------------------------------------------------------- |
| **Users**        | `User`               | `users`                  | name, email, password, role_id, phone, avatar, is_active, email_verified_at       |
|                  | `Role`               | `roles`                  | name, slug                                                                        |
|                  | `Permission`         | `permissions`            | name, slug, group                                                                 |
|                  | `Address`            | `addresses`              | user_id, name, phone, address, city, state, is_default                            |
| **Vendors**      | `Vendor`             | `vendors`                | user_id, store_name, store_slug, status, commission_rate, balance, kyc_status      |
|                  | `VendorBankDetail`   | `vendor_bank_details`    | vendor_id, bank_name, account_number, account_name                                |
|                  | `VendorDocument`     | `vendor_documents`       | vendor_id, type, file_path, status                                                |
|                  | `VendorPayout`       | `vendor_payouts`         | vendor_id, amount, status, reference, bank_name, processed_at                     |
| **Products**     | `Product`            | `products`               | vendor_id, category_id, name, slug, price, sale_price, quantity, status            |
|                  | `ProductImage`       | `product_images`         | product_id, image_path, is_primary, sort_order                                    |
|                  | `ProductVariant`     | `product_variants`       | product_id, name, value, price_modifier, stock                                    |
|                  | `Category`           | `categories`             | name, slug, icon, image, parent_id                                                |
|                  | `Tag`                | `tags`                   | name, slug                                                                        |
|                  | `PriceHistory`       | `price_history`          | product_id, old_price, new_price, changed_by, reason                              |
|                  | `StockHistory`       | `stock_histories`        | product_id, old_stock, new_stock, reason                                          |
| **Orders**       | `Order`              | `orders`                 | order_number, user_id, subtotal, shipping_cost, total, status, payment_status      |
|                  | `OrderItem`          | `order_items`            | order_id, product_id, vendor_id, quantity, price, status                           |
|                  | `OrderStatusHistory` | `order_status_histories` | order_id, status, notes, changed_by, user_id                                      |
|                  | `ShippingMethod`     | `shipping_methods`       | name, description, price, estimated_days                                          |
| **Cart**         | `Cart`               | `carts`                  | user_id, session_id                                                               |
|                  | `CartItem`           | `cart_items`             | cart_id, product_id, quantity, variant_id, price                                   |
| **Commerce**     | `Coupon`             | `coupons`                | vendor_id, code, type, value, min_order_amount, max_uses, expires_at               |
|                  | `Review`             | `reviews`                | user_id, product_id, rating, comment                                              |
|                  | `Wishlist`           | `wishlists`              | user_id, product_id                                                               |
|                  | `Dispute`            | `disputes`               | order_id, user_id, vendor_id, type, description, status                            |
|                  | `DisputeMessage`     | `dispute_messages`       | dispute_id, user_id, message, is_admin                                            |
| **Messaging**    | `Conversation`       | `conversations`          | user_id, vendor_id                                                                |
|                  | `Message`            | `messages`               | conversation_id, sender_id, body, read_at                                         |
|                  | `Notification`       | `notifications`          | user_id, type, title, message, action_url, read_at                                |
| **Email**        | `EmailTemplate`      | `email_templates`        | name, slug, subject, body                                                         |
|                  | `EmailCampaign`      | `email_campaigns`        | name, subject, body, recipient_type, status                                       |
|                  | `ContactMessage`     | `contact_messages`       | name, email, subject, message, status                                             |
| **Platform**     | `SystemSetting`      | `system_settings`        | group, key, value, type, is_public                                                |
|                  | `FeatureToggle`      | `feature_toggles`        | key, enabled, description                                                         |
|                  | `AuditLog`           | `audit_logs`             | user_id, action, model_type, model_id, details                                    |
| **AI**           | `AIProvider`         | `ai_providers`           | name, model, credentials (JSON), is_active                                        |
|                  | `AIAction`           | `ai_actions`             | vendor_id, action_type, module, description, input_data, output_data, status       |
|                  | `AIChatSession`      | `ai_chat_sessions`       | user_id, title                                                                    |
|                  | `AIChatMessage`      | `ai_chat_messages`       | session_id, role, content                                                         |
|                  | `AIEmergencyStatus`  | `ai_emergency_status`    | kill_switch_enabled, killed_at                                                    |

### Key Model Behaviors

- **Soft Deletes**: `User`, `Vendor`, `Product`, `Order` all use `SoftDeletes`.
- **Auto Order Number**: Orders auto-generate `BN-{UNIQID}` on creation.
- **Computed Attributes**: `Product->current_price` (returns sale price if applicable), `Product->discount_percentage`, `Vendor->whatsapp_number` (auto-formats Nigerian numbers to international).
- **Caching**: `SystemSetting::get()` uses `Cache::rememberForever()` with automatic cache-busting on `set()`.

---

## 🛣️ Routing Reference

All routes are defined in `routes/web.php` (395 lines). Below is the complete reference organized by role.

### Public Routes (No Auth)

| Method       | URI                    | Controller@Method              | Name               | Description                |
| ------------ | ---------------------- | ------------------------------ | ------------------- | -------------------------- |
| GET          | `/`                    | ShopController@index           | home                | Homepage                   |
| GET          | `/products`            | ShopController@catalog         | catalog             | Product catalog with filters|
| GET          | `/product/{slug}`      | ShopController@product         | product.detail      | Product detail page         |
| GET          | `/category/{category}` | ShopController@catalog         | category            | Category filter             |
| GET          | `/stores`              | StoreController@index          | stores              | All vendor stores           |
| GET          | `/store/{slug}`        | StoreController@show           | store.show          | Vendor storefront           |
| GET          | `/search/suggestions`  | ShopController@suggestions     | search.suggestions  | AJAX search autocomplete    |
| GET          | `/about`               | ShopController@about           | about               | About page                  |
| GET/POST     | `/contact`             | ShopController@contact/send    | contact / contact.send | Contact form              |
| GET/POST     | `/track-order`         | ShopController@trackOrder      | track.order         | Public order tracking       |
| GET          | `/privacy`             | ShopController@privacy         | privacy             | Privacy policy              |
| GET          | `/terms`               | ShopController@terms           | terms               | Terms of service            |
| GET          | `/refund-policy`       | ShopController@refundPolicy    | refund.policy       | Refund policy               |
| GET          | `/vendor-policy`       | ShopController@vendorPolicy    | vendor.policy       | Vendor policy               |
| GET          | `/cart`                | CartController@index           | cart.index          | View cart                   |
| POST         | `/cart/add`            | CartController@add             | cart.add            | Add to cart                 |
| PATCH        | `/cart/update/{item}`  | CartController@update          | cart.update         | Update cart item qty        |
| DELETE       | `/cart/remove/{item}`  | CartController@remove          | cart.remove         | Remove from cart            |
| GET          | `/wishlist`            | WishlistController@index       | wishlist.index      | View wishlist               |
| POST         | `/wishlist/add`        | WishlistController@add         | wishlist.add        | Add to wishlist             |
| DELETE       | `/wishlist/{id}`       | WishlistController@remove      | wishlist.remove     | Remove from wishlist        |

### Auth Routes (Guest Only)

| Method | URI                  | Name              | Description           |
| ------ | -------------------- | ----------------- | --------------------- |
| GET    | `/login`             | login             | Login form             |
| POST   | `/login`             | —                 | Process login          |
| GET    | `/register`          | register          | Customer registration  |
| POST   | `/register`          | —                 | Process registration   |
| GET    | `/vendor/register`   | vendor.register   | Vendor registration    |
| POST   | `/vendor/register`   | vendor.register.submit | Process vendor reg |
| GET    | `/forgot-password`   | password.request  | Forgot password form   |
| POST   | `/forgot-password`   | password.email    | Send reset email       |
| GET    | `/reset-password`    | password.reset    | Reset password form    |
| POST   | `/reset-password`    | password.update   | Process password reset |
| GET    | `/verify-email`      | verification.show | Email OTP verification |
| POST   | `/verify-email`      | verification.verify | Verify OTP           |

### Customer Routes (`/account/*`, role:4)

Products, orders, reviews, messaging, addresses, disputes — all under `customer.*` named routes.

### Vendor Routes (`/vendor/*`, role:3 + vendor.approved)

Dashboard, products CRUD, orders, inventory, analytics, coupons, finances, payouts, messaging, settings, profile — all under `vendor.*` named routes.

### Admin Routes (`/admin/*`, role:2)

Dashboard, vendors, users, orders, products, disputes, messages, transactions — all under `admin.*` named routes.

### Super Admin Routes (`/superadmin/*`, role:1)

Everything Admin has **plus**: system settings, AI control & kill switch, payment settings, email settings, email templates (CRUD), email campaigns (CRUD), audit logs, payout approvals, platform analytics — all under `superadmin.*` named routes.

### Special Routes

| Route                          | Description                                         |
| ------------------------------ | --------------------------------------------------- |
| `POST /webhook/paystack`       | Paystack payment webhook (no auth, CSRF exempted)   |
| `GET /run-migration-secret-777`| Emergency migration route (⚠️ remove in production) |
| `GET /debug-mail-config`       | Debug mail configuration (⚠️ remove in production)  |

---

## 🔐 Middleware

### Custom Middleware (registered in `Kernel.php`)

| Alias              | Class                        | Purpose                                                        |
| ------------------ | ---------------------------- | -------------------------------------------------------------- |
| `role`             | `RoleMiddleware`             | Checks `user.role_id` against allowed roles (supports numeric IDs and role names) |
| `permission`       | `PermissionMiddleware`       | Checks user permissions via the Role → Permission relationship  |
| `vendor.approved`  | `VendorApproved`             | Ensures vendor exists, is not rejected/suspended. Rejected vendors are logged out with reason. |

### Middleware Flow for Vendors

```
Request → auth → role:3 → vendor.approved → Controller
                              │
                              ├── No vendor profile → redirect to setup
                              ├── status = 'rejected' → logout + error
                              ├── status = 'suspended' → logout + error
                              └── status = 'pending' / 'approved' → proceed
```

---

## 🤖 AI Commerce Operating System (AICOS)

The AI system is the defining feature of BuyNiger. It's structured in multiple layers:

### Architecture

```
┌─────────────────────────────────────────────┐
│              AI Provider Layer              │
│  ┌──────────┐ ┌────────┐ ┌──────────────┐  │
│  │  Groq    │ │ Gemini │ │   OpenAI     │  │
│  │(default) │ │        │ │              │  │
│  └──────────┘ └────────┘ └──────────────┘  │
├─────────────────────────────────────────────┤
│         AIService (Core Orchestrator)       │
│         AIProviderInterface (Contract)      │
├─────────────────────────────────────────────┤
│              AI Module Layer                │
│  ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐      │
│  │ COO  │ │ CMO  │ │ CRO  │ │ CFO  │      │
│  └──────┘ └──────┘ └──────┘ └──────┘      │
│            ┌────────────────┐               │
│            │  Supply Chain  │               │
│            └────────────────┘               │
├─────────────────────────────────────────────┤
│  AIDataHelper (Context)  │  AIActionHelper  │
│  (builds role-aware      │  (71KB of action │
│   data for prompts)      │   execution logic)│
├─────────────────────────────────────────────┤
│  ExecuteAIAction (Job)                      │
│  - Simulation → Approval → Execution       │
│  - Rollback support                         │
│  - Kill switch checks                       │
│  - Rate limiting                            │
└─────────────────────────────────────────────┘
```

### AI Provider Interface

All providers implement `AIProviderInterface`:

```php
interface AIProviderInterface {
    public function generateText(string $prompt, array $config = []): array;
    public function generateChat(array $messages, array $config = []): array;
    public function analyzeImage(string $imagePath, string $prompt): array;
    public function calculateCost(int $inputTokens, int $outputTokens): float;
}
```

### Implemented Providers

| Provider  | File                  | Default Model             | Features                              |
| --------- | --------------------- | ------------------------- | ------------------------------------- |
| **Groq**  | `GroqProvider.php`    | `llama-3.3-70b-versatile` | Text generation, chat (default)       |
| **Gemini**| `GeminiProvider.php`  | `gemini-pro`              | Text, chat, **image analysis**        |
| **OpenAI**| `OpenAIProvider.php`  | GPT models                | Text, chat                            |

### AI Modules

| Module          | File                    | Capabilities                                                |
| --------------- | ----------------------- | ----------------------------------------------------------- |
| **COO**         | `COOModule.php`         | Daily performance analysis, inventory monitoring, optimization suggestions |
| **CMO**         | `CMOModule.php`         | Promotion suggestions, campaign drafting, pricing analysis   |
| **CRO**         | `CROModule.php`         | Customer inquiry handling, dispute resolution, churn analysis|
| **CFO**         | `CFOModule.php`         | Revenue analysis, fraud detection, cash flow forecasting     |
| **Supply Chain**| `SupplyChainModule.php` | Stock prediction, logistics optimization                     |

### AI Action Execution Pipeline

```
1. AI proposes an action (stored as ai_simulation)
2. Check AI mode: simulation → log only, live → execute
3. Check kill switch (ai_emergency_status)
4. Capture original state (for rollback)
5. Execute action (price_change, pause_product, send_notification, create_promotion)
6. Log to ai_actions table
7. Create rollback_record
8. Increment rate limit counter
9. Fire AIActionExecuted event
```

### AI Data Context

`AIDataHelper` builds comprehensive context based on user role:
- **Vendor**: store info, product list with prices/stock, recent orders, financials
- **Admin/SuperAdmin**: platform-wide stats (users, vendors, orders, revenue), pending vendors, pending payouts
- **Customer**: order history, total spending

### AI Safety Controls

- **Kill Switch**: Super Admin can disable all AI globally via `POST /superadmin/ai/kill-switch`
- **Simulation Mode**: AI actions are proposed and logged without executing
- **Rollback System**: Every executed action stores pre-execution state
- **Rate Limiting**: Per-action daily limits via `ai_action_limits` table
- **Audit Trail**: All AI decisions logged in `ai_actions` table with reasoning

---

## 💳 Payment System

### Paystack Integration

| Component                  | File                           | Description                          |
| -------------------------- | ------------------------------ | ------------------------------------ |
| `PaymentController`        | Controllers/PaymentController  | Initialize payment, callback, webhook|
| `PaystackTransferService`  | Services/PaystackTransferService| Vendor payouts via Paystack Transfer API |

### Payment Flow

```
Customer                        Server                         Paystack
   │                               │                              │
   ├── POST /checkout ────────────→│                              │
   │                               ├── Create Order               │
   │                               ├── POST /transaction/initialize ──→│
   │   ←── redirect to payment_url │                              │
   │                               │                              │
   ├── Pay on Paystack ───────────────────────────────────────────→│
   │                               │                              │
   │   ←── GET /payment/callback?reference=xxx                    │
   │                               ├── GET /transaction/verify ───→│
   │                               ├── Verify payment             │
   │                               ├── Update order status        │
   │                               ├── Send confirmation email    │
   │                               │                              │
   │                               │←── POST /webhook/paystack    │
   │                               ├── Double-verify via webhook  │
```

### Vendor Payout Flow

```
Vendor requests payout → SuperAdmin approves →
PaystackTransferService creates recipient →
Initiates transfer → Updates payout status
```

### Order Statuses

```
pending → paid → processing → shipped → delivered
                                      → cancelled
                                      → refunded
```

---

## ⚡ Background Jobs & Queues

| Job                        | Queue           | Description                                    |
| -------------------------- | --------------- | ---------------------------------------------- |
| `ExecuteAIAction`          | `ai`            | Executes approved AI simulation actions         |
| `ProcessAIAnalysis`        | `ai`            | Runs AI analysis tasks                          |
| `ProcessPaymentVerification`| `payments`     | Async payment verification                      |
| `SendEmailNotification`    | `emails`        | Send transactional emails                       |
| `SendPushNotification`     | `notifications` | Push notification delivery                      |
| `IndexProductForSearch`    | `search`        | Product search indexing                         |
| `AggregateAnalytics`       | `analytics`     | Aggregate analytics data                        |
| `ProcessImageUpload`       | `images`        | Image processing and optimization               |
| `CleanupTempFiles`         | `maintenance`   | Temporary file cleanup                          |

### Queue Configuration

Default is `sync` (processes inline). For production, switch to `database` or `redis`:

```env
QUEUE_CONNECTION=database
```

Then run the worker:

```bash
php artisan queue:work --queue=payments,ai,emails,notifications,search,analytics,images,maintenance
```

### System Health Metrics

`MetricsService` tracks:
- Job success/failure rates
- Queue health (pending/failed counts)
- AI latency per provider
- Payment failures per gateway
- Overall system status: `healthy` / `warning` / `critical`

---

## 📧 Email & Notifications

### Transactional Emails

| Mailable                | Trigger                    | Recipients      |
| ----------------------- | -------------------------- | --------------- |
| `OrderConfirmation`     | Order placed successfully  | Customer         |
| `NewOrderNotification`  | New order received          | Vendor           |

### Email Configuration

Uses SMTP via Titan Email (SSL, port 465). Configurable via `.env` or Super Admin settings.

### In-App Notifications

The `Notification` model stores in-app notifications with:
- `type` — e.g., `order_update`, `ai_notification`
- `title`, `message`, `action_url`
- `read_at` — nullable timestamp for read tracking

### Email Templates & Campaigns

Super Admin can manage email templates and campaigns via the web UI at:
- `/superadmin/email-templates` — CRUD for reusable templates
- `/superadmin/email-campaigns` — Campaign creation, segmentation, and management

---

## 🎨 Frontend & Views

### Layout System

| Layout               | File                         | Used By                                          |
| -------------------- | ---------------------------- | ------------------------------------------------ |
| `shop`               | `layouts/shop.blade.php`     | All public shop pages (21KB — includes header, nav, footer, cart widget) |
| `vendor`             | `layouts/vendor.blade.php`   | Vendor dashboard pages (sidebar nav)              |
| `app`                | `layouts/app.blade.php`      | Super Admin & Admin pages                         |
| `auth`               | `layouts/auth.blade.php`     | Login, register, password reset pages             |

### View Directories

| Directory       | Views                                                                        |
| --------------- | ---------------------------------------------------------------------------- |
| `shop/`         | Homepage, catalog, product detail, cart, checkout, payment, orders, wishlist, stores, store, policies |
| `vendor/`       | Dashboard, products (CRUD), orders, order detail, analytics, inventory, finances, settings, coupons, messages |
| `superadmin/`   | Dashboard, AI control, analytics, audit logs, disputes, email, messages, orders, payments, payouts, products, settings, transactions, users, vendors |
| `admin/`        | Dashboard (shared controller with SuperAdmin)                                  |
| `customer/`     | Dashboard, profile, addresses, messages, reviews                               |
| `auth/`         | Login, register, vendor register, forgot/reset password, email verification    |
| `emails/`       | Order confirmation, vendor new order notification                               |

### Frontend Libraries

- **Bootstrap 5** — grid, components, utilities
- **jQuery** — AJAX requests, DOM manipulation
- **Font Awesome** — icons
- **Chart.js** (used in analytics views)
- **Vite** — asset bundling

---

## 📱 Mobile App API

A comprehensive **Mobile App API Documentation** exists at `MOBILE_APP_API_DOCUMENTATION.txt` (1082 lines, 44KB). It covers:

- **90+ API endpoints** organized by module
- **55 mobile screens** across Customer and Vendor roles
- **Screen-to-API mapping** for every mobile view
- **Request/response examples** for every endpoint
- **Data models** reference
- **Deep linking** scheme: `buyniger://product/{slug}`

### API Authentication

Uses **Laravel Sanctum** with bearer tokens:

```
Authorization: Bearer {token}
```

### Recommended API Prefix

All mobile API routes should use `/api/v1/` prefix (currently routes are web-only; API routes need to be created from the existing web routes).

---

## 🧪 Testing

### PHPUnit

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage
```

### Manual Testing

Test accounts are listed in [Getting Started](#-getting-started).

### Testing Checklist

- [ ] Customer: Register → Verify email → Browse → Add to cart → Checkout → Pay → Track order
- [ ] Vendor: Register → Wait for approval → Add products → Process orders → View analytics → Request payout
- [ ] Admin: Login → Approve vendors → Moderate products → Handle disputes
- [ ] Super Admin: Configure AI → Set payment keys → Manage system settings → View audit logs

---

## 🚢 Deployment Notes

### Production Checklist

```bash
# 1. Set environment
APP_ENV=production
APP_DEBUG=false

# 2. Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# 3. Run migrations
php artisan migrate --force

# 4. Set proper file permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 5. Create storage symlink
php artisan storage:link

# 6. Build assets
npm run build
```

### Security Reminders

- ⚠️ **Remove** `/run-migration-secret-777` route
- ⚠️ **Remove** `/debug-mail-config` route
- ⚠️ **Rotate** all API keys and passwords from `.env`
- ⚠️ **Set** `APP_DEBUG=false`
- ⚠️ **Configure** CSRF exceptions only for webhook routes
- ⚠️ **Enable** HTTPS and update `APP_URL`

### Recommended Production Stack

| Component     | Recommendation                                |
| ------------- | --------------------------------------------- |
| Web Server    | Nginx + PHP-FPM                               |
| Database      | MySQL 8.0 (dedicated server)                  |
| Cache         | Redis                                         |
| Queue         | Redis + Supervisor (for queue workers)         |
| File Storage  | S3 or DigitalOcean Spaces                     |
| SSL           | Let's Encrypt via Certbot                     |

---

## 🤝 Contributing

### Code Style

- Follow PSR-12 for PHP
- Use Laravel conventions (resourceful controllers, Eloquent relationships)
- All controllers and models have author attribution headers
- Keep Blade views organized by role (`shop/`, `vendor/`, `superadmin/`, etc.)

### Development Phases

The project follows a 9-phase implementation plan (see `implementation_plan.txt`):

| Phase | Focus                   | Status         |
| ----- | ----------------------- | -------------- |
| A     | Critical shop fixes     | ✅ Complete    |
| B     | Vendor panel rebuild    | ✅ Complete    |
| C     | Customer features       | ✅ Complete    |
| D     | Messaging system        | ✅ Complete    |
| E     | Admin panel             | ✅ Complete    |
| F     | Super Admin panel       | ✅ Complete    |
| G     | AI modules              | 🟡 In Progress |
| H     | Notifications & email   | ✅ Complete    |
| I     | Additional features     | 🟡 In Progress |

### Branch Strategy

```
main          ← production-ready code
develop       ← active development
feature/*     ← new features
fix/*         ← bug fixes
```

---

## 👨‍💻 Developer

**Shuaibu Abdulmumin**
- 📞 08122598372 / 07049906420
- 🌍 Built with ❤️ in Nigeria

---

## 📄 License

This project is **proprietary software**. All rights reserved.
Unauthorized copying, distribution, or modification is strictly prohibited.
