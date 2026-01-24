# 🛒 BuyNiger - AI-First Multi-Vendor E-Commerce Platform

A complete, production-ready multi-vendor e-commerce platform built with **Laravel 10** and powered by **AI (Groq/Gemini/OpenAI)**.

![Laravel](https://img.shields.io/badge/Laravel-10.x-red?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.1+-blue?style=flat-square&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-orange?style=flat-square&logo=mysql)
![AI](https://img.shields.io/badge/AI-Groq-green?style=flat-square)

## ✨ Features

### 🛍️ Customer Features
- Product browsing & search with filters
- Shopping cart & wishlist
- Order tracking & history
- Secure payments (Paystack, Flutterwave)
- AI shopping assistant chatbot

### 🏪 Vendor Features
- Store management & custom branding
- Product management (CRUD, variants, inventory)
- Order processing workflow
- Analytics dashboard
- Payout requests & bank management

### 👤 Admin Features
- User & vendor management
- Product moderation
- Dispute resolution
- Platform analytics

### ⚙️ Super Admin Features
- Full platform control
- AI configuration (Groq, Gemini, OpenAI)
- Payment gateway settings
- System settings & audit logs
- Emergency AI kill switch

### 🤖 AI-Powered Features
- Intelligent chatbot for all user roles
- Role-aware assistance (Customer/Vendor/Admin/SuperAdmin)
- Platform action execution via natural language
- Multi-provider support with automatic failover

## 🚀 Tech Stack

- **Backend**: Laravel 10, PHP 8.1+
- **Database**: MySQL 8.0
- **Frontend**: Blade, Bootstrap 5, jQuery
- **AI Providers**: Groq (Llama 3.3), Google Gemini, OpenAI
- **Payments**: Paystack, Flutterwave

## 📦 Installation

```bash
git clone https://github.com/shuaib192/buy-niger.git
cd buy-niger
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## ⚙️ Environment Variables

```env
# Database
DB_DATABASE=buyniger
DB_USERNAME=root
DB_PASSWORD=

# AI Provider (choose one or more)
GROQ_API_KEY=your_groq_key

# Payments
PAYSTACK_SECRET_KEY=your_key
FLUTTERWAVE_SECRET_KEY=your_key
```

## 👨‍💻 Developer

**Shuaibu Abdulmumin**
- 📞 08122598372, 07049906420
- Built with ❤️ in Nigeria

## 📄 License

This project is proprietary software. All rights reserved.
