-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 24, 2026 at 02:39 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.1.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `buyniger`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `label` varchar(255) NOT NULL DEFAULT 'Home',
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address_line_1` text NOT NULL,
  `address_line_2` text DEFAULT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL DEFAULT 'Nigeria',
  `postal_code` varchar(255) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `label`, `first_name`, `last_name`, `phone`, `address_line_1`, `address_line_2`, `city`, `state`, `country`, `postal_code`, `is_default`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 4, 'Home', 'Test Customer', 'ABDOOL', '08087654321', 'bOSSO NGIGERIA NIGER STATE', 'Bosso', 'kANO', 'Abia', 'Nigeria', NULL, 1, '2026-01-03 17:25:53', '2026-01-04 09:02:44', '2026-01-04 09:02:44'),
(3, 4, 'Home', 'Customer', 'ABDOOL', '08087654321', 'bOSSO NGIGERIA NIGER STATE', 'Bosso', 'nigiihi', 'Lagos', 'Nigeria', NULL, 1, '2026-01-24 12:18:36', '2026-01-24 12:18:36', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ai_actions`
--

CREATE TABLE `ai_actions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ai_provider_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action_type` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `input_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`input_data`)),
  `output_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`output_data`)),
  `status` enum('pending','executed','rolled_back','failed') NOT NULL DEFAULT 'pending',
  `reasoning` text DEFAULT NULL,
  `was_auto_executed` tinyint(1) NOT NULL DEFAULT 0,
  `requires_approval` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `executed_at` timestamp NULL DEFAULT NULL,
  `rolled_back_at` timestamp NULL DEFAULT NULL,
  `rollback_reason` text DEFAULT NULL,
  `tokens_used` int(11) NOT NULL DEFAULT 0,
  `cost` decimal(10,6) NOT NULL DEFAULT 0.000000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_action_limits`
--

CREATE TABLE `ai_action_limits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(255) NOT NULL,
  `vendor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `hourly_limit` int(11) DEFAULT NULL,
  `daily_limit` int(11) DEFAULT NULL,
  `monthly_limit` int(11) DEFAULT NULL,
  `current_hourly_count` int(11) NOT NULL DEFAULT 0,
  `current_daily_count` int(11) NOT NULL DEFAULT 0,
  `current_monthly_count` int(11) NOT NULL DEFAULT 0,
  `hourly_reset_at` timestamp NULL DEFAULT NULL,
  `daily_reset_at` timestamp NULL DEFAULT NULL,
  `monthly_reset_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ai_action_limits`
--

INSERT INTO `ai_action_limits` (`id`, `action`, `vendor_id`, `hourly_limit`, `daily_limit`, `monthly_limit`, `current_hourly_count`, `current_daily_count`, `current_monthly_count`, `hourly_reset_at`, `daily_reset_at`, `monthly_reset_at`, `created_at`, `updated_at`) VALUES
(1, 'price_change', NULL, NULL, 50, 500, 0, 0, 0, '2026-01-03 08:59:53', '2026-01-04 07:59:53', '2026-02-03 07:59:53', '2026-01-03 07:59:53', '2026-01-03 07:59:53'),
(2, 'create_promotion', NULL, NULL, 10, 100, 0, 0, 0, '2026-01-03 08:59:53', '2026-01-04 07:59:53', '2026-02-03 07:59:53', '2026-01-03 07:59:53', '2026-01-03 07:59:53'),
(3, 'refund', NULL, NULL, 20, 200, 0, 0, 0, '2026-01-03 08:59:53', '2026-01-04 07:59:53', '2026-02-03 07:59:53', '2026-01-03 07:59:53', '2026-01-03 07:59:53'),
(4, 'send_notification', NULL, NULL, 500, 10000, 0, 0, 0, '2026-01-03 08:59:53', '2026-01-04 07:59:53', '2026-02-03 07:59:53', '2026-01-03 07:59:53', '2026-01-03 07:59:53'),
(5, 'auto_reply', NULL, NULL, 1000, 20000, 0, 0, 0, '2026-01-03 08:59:53', '2026-01-04 07:59:53', '2026-02-03 07:59:53', '2026-01-03 07:59:53', '2026-01-03 07:59:53'),
(6, 'send_campaign', NULL, NULL, 5, 30, 0, 0, 0, '2026-01-03 08:59:53', '2026-01-04 07:59:53', '2026-02-03 07:59:53', '2026-01-03 07:59:53', '2026-01-03 07:59:53');

-- --------------------------------------------------------

--
-- Table structure for table `ai_chat_messages`
--

CREATE TABLE `ai_chat_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `session_id` bigint(20) UNSIGNED NOT NULL,
  `role` enum('user','assistant','system') NOT NULL,
  `content` longtext NOT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `tokens` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_chat_sessions`
--

CREATE TABLE `ai_chat_sessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `vendor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `session_type` varchar(255) NOT NULL,
  `status` enum('active','closed') NOT NULL DEFAULT 'active',
  `context` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`context`)),
  `message_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_emergency_status`
--

CREATE TABLE `ai_emergency_status` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `kill_switch_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `kill_switch_reason` text DEFAULT NULL,
  `triggered_by` bigint(20) UNSIGNED DEFAULT NULL,
  `triggered_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ai_emergency_status`
--

INSERT INTO `ai_emergency_status` (`id`, `is_active`, `kill_switch_enabled`, `kill_switch_reason`, `triggered_by`, `triggered_at`, `created_at`, `updated_at`) VALUES
(1, 1, 0, NULL, NULL, NULL, '2026-01-03 07:50:20', '2026-01-03 07:50:20'),
(2, 1, 0, NULL, NULL, NULL, '2026-01-03 11:01:16', '2026-01-03 11:01:16');

-- --------------------------------------------------------

--
-- Table structure for table `ai_liability_logs`
--

CREATE TABLE `ai_liability_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ai_simulation_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ai_action_id` bigint(20) UNSIGNED DEFAULT NULL,
  `affected_entity_type` varchar(255) NOT NULL,
  `affected_entity_id` bigint(20) UNSIGNED NOT NULL,
  `affected_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `consent_status` enum('granted','pending','denied','not_required') NOT NULL DEFAULT 'not_required',
  `legal_context` text DEFAULT NULL,
  `disclosure_text` text DEFAULT NULL,
  `consent_given_at` timestamp NULL DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_memory`
--

CREATE TABLE `ai_memory` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `memory_type` varchar(255) NOT NULL,
  `entity_type` varchar(255) DEFAULT NULL,
  `entity_id` bigint(20) UNSIGNED DEFAULT NULL,
  `key` varchar(255) NOT NULL,
  `value` longtext NOT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `confidence` decimal(5,2) NOT NULL DEFAULT 1.00,
  `usage_count` int(11) NOT NULL DEFAULT 0,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_permissions`
--

CREATE TABLE `ai_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ai_role` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `resource` varchar(255) DEFAULT NULL,
  `max_value` decimal(15,2) DEFAULT NULL,
  `max_percentage` decimal(5,2) DEFAULT NULL,
  `requires_human_approval` tinyint(1) NOT NULL DEFAULT 1,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ai_permissions`
--

INSERT INTO `ai_permissions` (`id`, `ai_role`, `action`, `resource`, `max_value`, `max_percentage`, `requires_human_approval`, `is_enabled`, `description`, `created_at`, `updated_at`) VALUES
(1, 'COO', 'pause_product', NULL, NULL, NULL, 0, 1, 'Pause out-of-stock products', '2026-01-03 07:59:53', '2026-01-03 07:59:53'),
(2, 'COO', 'restock_alert', NULL, NULL, NULL, 0, 1, 'Send restock notifications', '2026-01-03 07:59:53', '2026-01-03 07:59:53'),
(3, 'COO', 'suspend_vendor', NULL, NULL, NULL, 1, 1, 'Suspend vendor account', '2026-01-03 07:59:53', '2026-01-03 07:59:53'),
(4, 'CMO', 'price_change', NULL, NULL, 5.00, 0, 1, 'Change prices up to 5%', '2026-01-03 07:59:53', '2026-01-03 07:59:53'),
(5, 'CMO', 'price_change_major', NULL, NULL, 20.00, 1, 1, 'Change prices 5-20%', '2026-01-03 07:59:53', '2026-01-03 07:59:53'),
(6, 'CMO', 'create_promotion', NULL, 5000.00, NULL, 0, 1, 'Create promotions up to ₦5,000 discount', '2026-01-03 07:59:53', '2026-01-03 07:59:53'),
(7, 'CMO', 'create_promotion_major', NULL, 50000.00, NULL, 1, 1, 'Create promotions ₦5,000-50,000', '2026-01-03 07:59:53', '2026-01-03 07:59:53'),
(8, 'CMO', 'send_campaign', NULL, NULL, NULL, 1, 1, 'Send email campaigns', '2026-01-03 07:59:53', '2026-01-03 07:59:53'),
(9, 'CRO', 'auto_reply', NULL, NULL, NULL, 0, 1, 'Auto-reply to customer messages', '2026-01-03 07:59:53', '2026-01-03 07:59:53'),
(10, 'CRO', 'refund', NULL, 10000.00, NULL, 0, 1, 'Refunds up to ₦10,000', '2026-01-03 07:59:53', '2026-01-03 07:59:53'),
(11, 'CRO', 'refund_major', NULL, 50000.00, NULL, 1, 1, 'Refunds ₦10,000-50,000', '2026-01-03 07:59:53', '2026-01-03 07:59:53'),
(12, 'CRO', 'refund_large', NULL, NULL, NULL, 1, 1, 'Refunds over ₦50,000', '2026-01-03 07:59:53', '2026-01-03 07:59:53'),
(13, 'CRO', 'send_notification', NULL, NULL, NULL, 0, 1, 'Send customer notifications', '2026-01-03 07:59:53', '2026-01-03 07:59:53'),
(14, 'CFO', 'flag_fraud', NULL, NULL, NULL, 0, 1, 'Flag suspicious transactions', '2026-01-03 07:59:53', '2026-01-03 07:59:53'),
(15, 'CFO', 'block_transaction', NULL, NULL, NULL, 1, 1, 'Block suspicious transactions', '2026-01-03 07:59:53', '2026-01-03 07:59:53'),
(16, 'CFO', 'adjust_commission', NULL, NULL, NULL, 1, 1, 'Adjust vendor commission rates', '2026-01-03 07:59:53', '2026-01-03 07:59:53'),
(17, 'SUPPLY_CHAIN', 'reorder_suggestion', NULL, NULL, NULL, 0, 1, 'Suggest reorder for low stock', '2026-01-03 07:59:53', '2026-01-03 07:59:53'),
(18, 'SUPPLY_CHAIN', 'auto_reorder', NULL, NULL, NULL, 1, 1, 'Auto-place reorder', '2026-01-03 07:59:53', '2026-01-03 07:59:53');

-- --------------------------------------------------------

--
-- Table structure for table `ai_policies`
--

CREATE TABLE `ai_policies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `policy_type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `rules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`rules`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `priority` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_providers`
--

CREATE TABLE `ai_providers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `credentials` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`credentials`)),
  `base_url` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `capabilities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`capabilities`)),
  `cost_per_1k_tokens` decimal(10,6) NOT NULL DEFAULT 0.000000,
  `rate_limit_per_minute` int(11) NOT NULL DEFAULT 60,
  `priority` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ai_providers`
--

INSERT INTO `ai_providers` (`id`, `name`, `display_name`, `description`, `credentials`, `base_url`, `model`, `is_active`, `is_default`, `capabilities`, `cost_per_1k_tokens`, `rate_limit_per_minute`, `priority`, `created_at`, `updated_at`) VALUES
(1, 'grok', 'Grok (xAI)', 'Default AI provider - Grok by xAI', NULL, 'https://api.x.ai/v1', 'grok-2-latest', 0, 1, '[\"chat\",\"reasoning\"]', 0.000000, 60, 1, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(2, 'openai', 'OpenAI GPT', 'OpenAI GPT-4 / GPT-3.5', NULL, 'https://api.openai.com/v1', 'gpt-4-turbo-preview', 0, 0, '[\"chat\",\"vision\",\"embeddings\"]', 0.000000, 60, 2, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(3, 'gemini', 'Google Gemini', 'Google Gemini Pro', NULL, 'https://generativelanguage.googleapis.com/v1beta', 'gemini-pro', 0, 0, '[\"chat\",\"vision\"]', 0.000000, 60, 3, '2026-01-03 11:01:16', '2026-01-03 11:01:16');

-- --------------------------------------------------------

--
-- Table structure for table `ai_simulations`
--

CREATE TABLE `ai_simulations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ai_role` varchar(255) NOT NULL,
  `proposed_action` varchar(255) NOT NULL,
  `action_description` text NOT NULL,
  `action_parameters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`action_parameters`)),
  `impact_estimate` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`impact_estimate`)),
  `risk_level` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `auto_executable` tinyint(1) NOT NULL DEFAULT 0,
  `approved` tinyint(1) NOT NULL DEFAULT 0,
  `executed` tinyint(1) NOT NULL DEFAULT 0,
  `rolled_back` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `executed_at` timestamp NULL DEFAULT NULL,
  `rolled_back_at` timestamp NULL DEFAULT NULL,
  `rollback_reason` text DEFAULT NULL,
  `execution_result` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`execution_result`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `analytics_daily`
--

CREATE TABLE `analytics_daily` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `vendor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `page_views` int(11) NOT NULL DEFAULT 0,
  `unique_visitors` int(11) NOT NULL DEFAULT 0,
  `product_views` int(11) NOT NULL DEFAULT 0,
  `add_to_carts` int(11) NOT NULL DEFAULT 0,
  `orders` int(11) NOT NULL DEFAULT 0,
  `revenue` decimal(15,2) NOT NULL DEFAULT 0.00,
  `conversion_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `analytics_events`
--

CREATE TABLE `analytics_events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `event_type` varchar(255) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `event_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`event_data`)),
  `page_url` varchar(255) DEFAULT NULL,
  `referrer` varchar(255) DEFAULT NULL,
  `device_type` varchar(255) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `model_type` varchar(255) DEFAULT NULL,
  `model_id` bigint(20) UNSIGNED DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `coupon_code` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`id`, `user_id`, `session_id`, `coupon_code`, `created_at`, `updated_at`) VALUES
(1, NULL, 'wWSop412x0L88bTWCjTsaQlxVBzAllOtySyGnQd4', NULL, '2026-01-03 16:12:09', '2026-01-03 16:12:09'),
(2, NULL, 'gq00X1O4eSMwl3Jod8LMJrVaU5rcAyI4IFKdlGYr', NULL, '2026-01-03 16:26:38', '2026-01-03 16:26:38'),
(3, 4, NULL, NULL, '2026-01-03 16:35:27', '2026-01-03 16:35:27'),
(4, NULL, '7EAStc6XrvZUcwbK31bUbtO9N6o5UPL0fQYufZyj', NULL, '2026-01-03 16:35:52', '2026-01-03 16:35:52'),
(5, NULL, 'Ak6bFLLeKoYwtlvS77qTzxpRiOM7Uezyg71cuqPU', NULL, '2026-01-03 17:09:37', '2026-01-03 17:09:37'),
(6, NULL, 'NYHN05yGepOfDsGsd3tfopt2CxBIbLJ0mYFwc96Y', NULL, '2026-01-03 17:20:43', '2026-01-03 17:20:43'),
(7, NULL, 'kmPZBYnG21VURLWwbGXZA2OOIIXTS2ZWvfXk8wZw', NULL, '2026-01-03 17:28:15', '2026-01-03 17:28:15'),
(8, NULL, 'eOwSgZtkTOFKeC6CxuwiIllAFlpJmG3z4JfaPovc', NULL, '2026-01-03 17:28:51', '2026-01-03 17:28:51'),
(9, NULL, 'lAJr57Q6QoodGXHAbBIwSuNElwfkZOL4IVEBa56Q', NULL, '2026-01-03 17:30:29', '2026-01-03 17:30:29'),
(10, 3, NULL, NULL, '2026-01-03 17:56:54', '2026-01-03 17:56:54'),
(11, NULL, '2krl83xvr79Uo3w1DkMg6MtIDs6F1uiiWzvCDc1y', NULL, '2026-01-03 17:57:29', '2026-01-03 17:57:29'),
(12, NULL, 'Y59PrD97ES7dw9YqyOj4kQMD99WO6PmGZ5cr0QbQ', NULL, '2026-01-03 17:58:20', '2026-01-03 17:58:20'),
(13, NULL, 'TCfjSDcAeb92ChO7uWwtjxzib0doVbU5Zm9phlbO', NULL, '2026-01-03 18:06:48', '2026-01-03 18:06:48'),
(14, NULL, 'aEnBhnGxC0YvShKdLiRLNUgcMxroZNzDxNGKZaii', NULL, '2026-01-03 18:11:27', '2026-01-03 18:11:27'),
(15, NULL, 'RwGMqlwu1Ax37sdBsH3MKmtqsFROqHgNAYlptwVh', NULL, '2026-01-03 18:13:42', '2026-01-03 18:13:42'),
(16, NULL, 'InDRlUGafIegaIZcDIwHeFdBuCSRqsvU341jN3xA', NULL, '2026-01-03 18:18:27', '2026-01-03 18:18:27'),
(17, NULL, 'hnuGgD1sL0xQeIaiua6f5TsDNXeQfFUI3ac9SmY2', NULL, '2026-01-03 18:18:43', '2026-01-03 18:18:43'),
(18, NULL, '45Ru3In2Ro2XCwczhAz2nPAHIFxTxt53H3fgK5t5', NULL, '2026-01-03 18:28:10', '2026-01-03 18:28:10'),
(19, NULL, 'YgSldjaEDvlehtLo5bUKRfQPbEz7f1k7ZBFERsDZ', NULL, '2026-01-03 18:51:43', '2026-01-03 18:51:43'),
(20, NULL, 'QC4D6gBidWf8R3lZae8NgvzSXeoMK8u7ko1ZlKrZ', NULL, '2026-01-03 18:52:42', '2026-01-03 18:52:42'),
(21, NULL, 'tef1wQxlyLx7TNsrgR1bNWLxdeEL0Xm25wVHjx38', NULL, '2026-01-03 19:05:15', '2026-01-03 19:05:15'),
(22, NULL, 'Ij7eGRIjw9aoE1NRJqSULGUQtCPaTc9Jw09qH30y', NULL, '2026-01-03 19:58:54', '2026-01-03 19:58:54'),
(23, NULL, '9ySEzkAPEYSpVEQSWVu5bCjkwSnsIuZp3a1a69C1', NULL, '2026-01-04 06:51:34', '2026-01-04 06:51:34'),
(24, NULL, 'mHWFGHgfkUfyb9qnyNYbIRx0PYtlZQC1hO7NP26u', NULL, '2026-01-04 06:53:45', '2026-01-04 06:53:45'),
(25, NULL, '4BXFaBKf4Ur1S0kxJixmbRFScPgz3Rk0SFCNl21b', NULL, '2026-01-04 06:54:41', '2026-01-04 06:54:41'),
(26, NULL, 'aM14GETWDnLKnCQsBdI0ZcxWRrbgxjGTeIwI6G3S', NULL, '2026-01-04 06:57:55', '2026-01-04 06:57:55'),
(27, NULL, 'EZup378dAUGKxXcKylhV8vBDdtm4sxEw5zcmkVwU', NULL, '2026-01-04 06:58:34', '2026-01-04 06:58:34'),
(28, NULL, 'MuZYswKRzFw6DcuwJJF1BLAJsQ3vEVVkrVncragB', NULL, '2026-01-04 09:02:52', '2026-01-04 09:02:52'),
(29, NULL, 'BUZrNYAODHMNo2uuhM9qqCGUsBdCPfcCj2mj9bYr', NULL, '2026-01-04 09:05:35', '2026-01-04 09:05:35'),
(30, NULL, 'bBDsdSSxqiVBU6QdhCAbvbdhc4O5WAU9IZkZLa7R', NULL, '2026-01-04 09:07:04', '2026-01-04 09:07:04'),
(31, NULL, 'HtD2hBmQ1AEXOVsueaWXNd6TJzWt3MLgcnXEVqp5', NULL, '2026-01-04 09:38:22', '2026-01-04 09:38:22'),
(32, NULL, 'eGmaO4V7pmTM2PhOaWX2da6Md66njBmTzELKXgXo', NULL, '2026-01-04 10:57:42', '2026-01-04 10:57:42'),
(33, NULL, '90dRkPG7tKcTAOSsOo1RgXhCr907lrfrp8KfPuFz', NULL, '2026-01-04 10:58:32', '2026-01-04 10:58:32'),
(34, NULL, 'test', NULL, '2026-01-04 11:03:14', '2026-01-04 11:03:14'),
(35, NULL, 'qaD7D7AIIw0r0L55h8xSxa8IIqveEYurInrCOJev', NULL, '2026-01-04 11:17:35', '2026-01-04 11:17:35'),
(36, NULL, 'etzBf3ku3YZod6CDdo1FTPaLjlfOuziXctHSVxSY', NULL, '2026-01-04 11:17:57', '2026-01-04 11:17:57'),
(37, NULL, 'WYcJHq1oEp7xF7GRNf8ldo0Oe3DRDLsXLGSKcCW6', NULL, '2026-01-04 11:18:21', '2026-01-04 11:18:21'),
(38, NULL, 'VBIR8DjCVa1BBUrshcnaHvl1zV7gSOSb0l1FlyGL', NULL, '2026-01-04 11:18:52', '2026-01-04 11:18:52'),
(39, NULL, 'hTuVOlqsVQJ20KsBiGx2TH3o54BVBLIYTTFfBgre', NULL, '2026-01-04 11:44:59', '2026-01-04 11:44:59'),
(40, NULL, 'le8hYKXe4QB2tFApmIGWRRqSiHwq4s8fndzwmN7y', NULL, '2026-01-04 12:11:45', '2026-01-04 12:11:45'),
(41, NULL, '5q9c7PeQYtOCkbs77hbYdnFXRsXqnOIvIASUYRNo', NULL, '2026-01-04 12:36:02', '2026-01-04 12:36:02'),
(42, NULL, 'z8g9gNfjCcf40yOXdRia6Z2cX7qij4T8Lm8pwWGe', NULL, '2026-01-04 16:33:03', '2026-01-04 16:33:03'),
(43, NULL, 'Iai8Oiipx2cWrFJ9JYMHHZc1tuytFjhzYng238k1', NULL, '2026-01-04 16:33:12', '2026-01-04 16:33:12'),
(44, NULL, 'PVEPs6Gb7Nkzyen1NTmTqtymsKuehBwnP8TbFK34', NULL, '2026-01-04 17:29:48', '2026-01-04 17:29:48'),
(45, NULL, 'fJiFxlQrQiNoEwKErAo5UqUJKjQUHgMCwN04NK2F', NULL, '2026-01-04 17:59:02', '2026-01-04 17:59:02'),
(46, NULL, 'AsWvJwv2bjimsxMNQ3ygbFWLHBn7gWnpxI5sCTKr', NULL, '2026-01-04 17:59:46', '2026-01-04 17:59:46'),
(47, NULL, 'QuKnwPMtZufIhRFyxvCQQAlAWuILD6B0MCFIRqY4', NULL, '2026-01-04 18:05:34', '2026-01-04 18:05:34'),
(48, NULL, '2XZ3M7AiQKopnVGNtB94PEM2ZbaiVpNEmg5HOOae', NULL, '2026-01-04 18:08:00', '2026-01-04 18:08:00'),
(49, NULL, 'qtpSGDYMPpT4CJYid7V289F6P3FBF2MY2jkwh3sH', NULL, '2026-01-04 18:08:40', '2026-01-04 18:08:40'),
(50, NULL, '7PUNFStS7hg7lGsBPqAV3dT46aadjGw9Yze05aOl', NULL, '2026-01-04 18:10:56', '2026-01-04 18:10:56'),
(51, 2, NULL, NULL, '2026-01-04 18:14:35', '2026-01-04 18:14:35'),
(52, NULL, '7VQFnsqM37XD4wrFY8pvJeInhNZknqH2ueGsCDsT', NULL, '2026-01-04 18:15:02', '2026-01-04 18:15:02'),
(53, NULL, 'lYIDxc7ZJpl1bvFwVpovZxBzyE6id4cFPgkwv6el', NULL, '2026-01-04 18:16:27', '2026-01-04 18:16:27'),
(54, NULL, 'Jjr5DSQ35AXyYzkjxRajw75lXjeUQJu7zrD83glT', NULL, '2026-01-04 18:22:41', '2026-01-04 18:22:41'),
(55, NULL, 'ER8m9zlHEHce4Ofnm4iZkRfnqpvhLd0WAPHEZXpH', NULL, '2026-01-04 18:44:11', '2026-01-04 18:44:11'),
(56, NULL, 'wkMVKIuZWWqHZEePtaSm1DO7gmPX1MYObWfY1ZrM', NULL, '2026-01-04 18:45:15', '2026-01-04 18:45:15'),
(57, NULL, 'Hi2XJBXlXIpWlwZLYq9SQfkFZPJHPTKEbY4cGuGo', NULL, '2026-01-04 18:46:46', '2026-01-04 18:46:46'),
(58, NULL, 'yJgK7AB9kvcpT6KEhRW95IZqS72E84j4f9F3h5Dk', NULL, '2026-01-04 18:47:17', '2026-01-04 18:47:17'),
(59, NULL, 'VNuNixpjgznsAFqRGZdWPrRKGaMDDc2MMw3ZMK6Y', NULL, '2026-01-04 18:47:36', '2026-01-04 18:47:36'),
(60, NULL, 'FKbMS0cW4RchEeNUo4wzJSl1o1qGyPPtZ7qXFK4W', NULL, '2026-01-04 18:48:33', '2026-01-04 18:48:33'),
(61, NULL, 'fRliwitvKkOL8ZLZSLdSnMdOnoyoqMrWvvGcZXjX', NULL, '2026-01-04 18:57:58', '2026-01-04 18:57:58'),
(62, 1, NULL, NULL, '2026-01-04 18:58:40', '2026-01-04 18:58:40'),
(63, NULL, 'TrA8EZ7VDT5vUcufuFS0nHlzc7sauCopbeldPImS', NULL, '2026-01-04 18:58:49', '2026-01-04 18:58:49'),
(64, NULL, 'oKMSrlZom1QNK1fZA4ra35zsyrrsEPO4RWWNTFHv', NULL, '2026-01-04 18:59:32', '2026-01-04 18:59:32'),
(65, NULL, 'HLyivb3VC0Nmn3zA628vSnpCauzkT8PW9SgK8Ygs', NULL, '2026-01-04 19:05:04', '2026-01-04 19:05:04'),
(66, NULL, 'DgRv5J6XyWCH3JppLg92jDL574g0EniufE4p9Ka2', NULL, '2026-01-04 19:09:16', '2026-01-04 19:09:16'),
(67, NULL, 'v6tVNUHgySKXCnqSPG4rTPTIlpmQXfnCDSwNJSVI', NULL, '2026-01-04 19:10:52', '2026-01-04 19:10:52'),
(68, NULL, 'pk0dV2al0NC5BjtLj0ieymedavpJqaEgQSndfImp', NULL, '2026-01-04 19:11:35', '2026-01-04 19:11:35'),
(69, NULL, 'EfYisGQ4aFEyWYocmkcz7vIWkBGW1yN5Tgq8eFe1', NULL, '2026-01-04 19:12:56', '2026-01-04 19:12:56'),
(70, NULL, 'cNJg4aFuGVbHVnJgArzLkjkP4r84dKFDyzILzQgd', NULL, '2026-01-04 19:13:57', '2026-01-04 19:13:57'),
(71, NULL, 'EJLbON4pF82VoSiOBv5u7eAqBuAPhL4wEwv7gQ5r', NULL, '2026-01-04 19:14:53', '2026-01-04 19:14:53'),
(72, NULL, 'E3KovBSjNHYZIuJptljaYXpE74FeglklGvyHkFr1', NULL, '2026-01-04 19:24:42', '2026-01-04 19:24:42'),
(73, NULL, 'EkQJDgFGXNTi4WNQqTzntlMo0HrR5akj8t5JUO76', NULL, '2026-01-05 07:57:15', '2026-01-05 07:57:15'),
(74, NULL, 'Uhprjg3TFseMgnj6t7MyZurUXFwh2CKWtGxB6Zxe', NULL, '2026-01-05 07:58:32', '2026-01-05 07:58:32'),
(75, NULL, 'SlxFDwkJNrd03z19VG3eYbh1q2N16IWacEU29Iss', NULL, '2026-01-05 07:59:34', '2026-01-05 07:59:34'),
(76, NULL, 'LIOhNKlp7ZmPUsv5tlDDinRTxTavgCuvnx7gVHot', NULL, '2026-01-24 12:11:13', '2026-01-24 12:11:13'),
(77, NULL, 'SPUO8VPWWczCpf0nlmemLKUs1MeluhaQCFNzloy6', NULL, '2026-01-24 12:12:45', '2026-01-24 12:12:45'),
(78, NULL, '3iIgq7OxBge0xu3QphmqaAYA0EWo7g0az1o5B3Jd', NULL, '2026-01-24 12:14:57', '2026-01-24 12:14:57'),
(79, NULL, '2O3kHF4d7q494aXURpapZfeWy68HTiB5Sc5guajk', NULL, '2026-01-24 12:19:34', '2026-01-24 12:19:34'),
(80, NULL, 'NLr9xVCulSVs2d7IAKLPLCkIyIsnPJMBRWwEZKjG', NULL, '2026-01-24 12:36:29', '2026-01-24 12:36:29');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cart_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `product_variant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `cart_id`, `product_id`, `product_variant_id`, `quantity`, `price`, `created_at`, `updated_at`) VALUES
(4, 2, 1, NULL, 1, NULL, '2026-01-03 16:32:31', '2026-01-03 16:32:31'),
(6, 5, 1, NULL, 1, NULL, '2026-01-03 17:09:44', '2026-01-03 17:09:44'),
(7, 6, 3, NULL, 2, NULL, '2026-01-03 17:21:16', '2026-01-03 17:21:18'),
(13, 34, 1, NULL, 1, 5000.00, '2026-01-04 11:03:14', '2026-01-04 11:03:14');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image`, `icon`, `parent_id`, `sort_order`, `is_active`, `is_featured`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Electronics', 'electronics', NULL, NULL, 'fa-laptop', NULL, 1, 1, 1, '2026-01-03 11:01:16', '2026-01-03 13:19:52', NULL),
(2, 'Fashion', 'fashion', NULL, NULL, 'fa-tshirt', NULL, 2, 1, 1, '2026-01-03 11:01:16', '2026-01-03 13:19:52', NULL),
(3, 'Home & Garden', 'home-garden', NULL, NULL, 'fa-home', NULL, 3, 1, 1, '2026-01-03 11:01:16', '2026-01-03 13:19:52', NULL),
(4, 'Health & Beauty', 'health-beauty', NULL, NULL, 'fa-heart', NULL, 4, 1, 1, '2026-01-03 11:01:16', '2026-01-03 13:19:52', NULL),
(5, 'Sports & Outdoors', 'sports-outdoors', NULL, NULL, 'fa-futbol', NULL, 5, 1, 1, '2026-01-03 11:01:16', '2026-01-03 13:19:52', NULL),
(6, 'Automotive', 'automotive', NULL, NULL, 'fa-car', NULL, 6, 1, 1, '2026-01-03 11:01:16', '2026-01-03 13:19:52', NULL),
(7, 'Books & Media', 'books-media', NULL, NULL, 'fa-book', NULL, 7, 1, 0, '2026-01-03 11:01:16', '2026-01-03 11:01:16', NULL),
(8, 'Food & Groceries', 'food-groceries', NULL, NULL, 'fa-utensils', NULL, 8, 1, 0, '2026-01-03 11:01:16', '2026-01-03 11:01:16', NULL),
(9, 'Toys & Games', 'toys-games', NULL, NULL, 'fa-gamepad', NULL, 9, 1, 0, '2026-01-03 11:01:16', '2026-01-03 11:01:16', NULL),
(10, 'Services', 'services', NULL, NULL, 'fa-concierge-bell', NULL, 10, 1, 0, '2026-01-03 11:01:16', '2026-01-03 11:01:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','replied','closed') NOT NULL DEFAULT 'new',
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `last_message_at` timestamp NULL DEFAULT NULL,
  `user_read_at` timestamp NULL DEFAULT NULL,
  `vendor_read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `value` decimal(15,2) NOT NULL,
  `min_spend` decimal(10,2) DEFAULT NULL,
  `min_order` decimal(15,2) DEFAULT NULL,
  `max_discount` decimal(15,2) DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) NOT NULL DEFAULT 0,
  `usage_count` int(11) NOT NULL DEFAULT 0,
  `per_user_limit` int(11) NOT NULL DEFAULT 1,
  `starts_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `vendor_id`, `code`, `name`, `description`, `type`, `value`, `min_spend`, `min_order`, `max_discount`, `usage_limit`, `used_count`, `usage_count`, `per_user_limit`, `starts_at`, `expires_at`, `is_active`, `created_at`, `updated_at`) VALUES
(2, 1, 'F5BF7901B089', NULL, NULL, 'fixed', 10.00, 20000.00, NULL, NULL, 1, 0, 0, 1, NULL, '2026-01-24 23:00:00', 1, '2026-01-24 12:13:59', '2026-01-24 12:13:59');

-- --------------------------------------------------------

--
-- Table structure for table `coupon_usages`
--

CREATE TABLE `coupon_usages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `coupon_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `discount_amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_tracking`
--

CREATE TABLE `delivery_tracking` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `order_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tracking_number` varchar(255) DEFAULT NULL,
  `carrier` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `location` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `event_time` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `disputes`
--

CREATE TABLE `disputes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('open','resolved','closed','escalated') NOT NULL DEFAULT 'open',
  `priority` enum('low','medium','high','critical') NOT NULL DEFAULT 'low',
  `resolution_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_campaigns`
--

CREATE TABLE `email_campaigns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` longtext NOT NULL,
  `status` enum('draft','scheduled','sending','sent','cancelled') NOT NULL DEFAULT 'draft',
  `target_audience` enum('all','customers','vendors','custom') NOT NULL DEFAULT 'all',
  `target_filters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`target_filters`)),
  `total_recipients` int(11) NOT NULL DEFAULT 0,
  `sent_count` int(11) NOT NULL DEFAULT 0,
  `open_count` int(11) NOT NULL DEFAULT 0,
  `click_count` int(11) NOT NULL DEFAULT 0,
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_logs`
--

CREATE TABLE `email_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `template` varchar(255) DEFAULT NULL,
  `campaign_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('pending','sent','failed','opened','clicked') NOT NULL DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `opened_at` timestamp NULL DEFAULT NULL,
  `clicked_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

CREATE TABLE `email_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` longtext NOT NULL,
  `variables` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`variables`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_templates`
--

INSERT INTO `email_templates` (`id`, `name`, `subject`, `body`, `variables`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'welcome', 'Welcome to BuyNiger!', '<h1>Welcome, {customer_name}!</h1><p>Thank you for joining BuyNiger. Start shopping now!</p>', '[\"customer_name\",\"email\"]', 1, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(2, 'order_confirmation', 'Order Confirmed - #{order_number}', '<h1>Order Confirmed!</h1><p>Hi {customer_name}, your order #{order_number} has been confirmed.</p>', '[\"customer_name\",\"order_number\",\"order_total\",\"order_items\"]', 1, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(3, 'order_shipped', 'Your Order is on the Way - #{order_number}', '<h1>Order Shipped!</h1><p>Your order #{order_number} is on its way. Track it here: {tracking_url}</p>', '[\"customer_name\",\"order_number\",\"tracking_number\",\"tracking_url\"]', 1, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(4, 'order_delivered', 'Order Delivered - #{order_number}', '<h1>Order Delivered!</h1><p>Your order #{order_number} has been delivered. Enjoy!</p>', '[\"customer_name\",\"order_number\"]', 1, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(5, 'vendor_approved', 'Your Vendor Application is Approved!', '<h1>Congratulations, {vendor_name}!</h1><p>Your vendor application has been approved. Start selling now!</p>', '[\"vendor_name\",\"store_name\"]', 1, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(6, 'password_reset', 'Reset Your Password', '<h1>Password Reset</h1><p>Click the link below to reset your password: {reset_link}</p>', '[\"customer_name\",\"reset_link\"]', 1, '2026-01-03 11:01:16', '2026-01-03 11:01:16');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feature_toggles`
--

CREATE TABLE `feature_toggles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `feature` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`config`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `feature_toggles`
--

INSERT INTO `feature_toggles` (`id`, `feature`, `display_name`, `description`, `is_enabled`, `config`, `created_at`, `updated_at`) VALUES
(1, 'vendor_registration', 'Vendor Registration', NULL, 1, NULL, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(2, 'customer_reviews', 'Customer Reviews', NULL, 1, NULL, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(3, 'wallet_system', 'Wallet System', NULL, 1, NULL, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(4, 'coupons', 'Coupon System', NULL, 1, NULL, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(5, 'wishlist', 'Wishlist', NULL, 1, NULL, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(6, 'ai_assistant', 'AI Assistant', NULL, 1, NULL, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(7, 'ai_auto_actions', 'AI Auto Actions', NULL, 0, NULL, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(8, 'email_campaigns', 'Email Campaigns', NULL, 1, NULL, '2026-01-03 11:01:16', '2026-01-03 11:01:16');

-- --------------------------------------------------------

--
-- Table structure for table `job_metrics`
--

CREATE TABLE `job_metrics` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_class` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL DEFAULT 'default',
  `processed_count` int(11) NOT NULL DEFAULT 0,
  `failed_count` int(11) NOT NULL DEFAULT 0,
  `pending_count` int(11) NOT NULL DEFAULT 0,
  `avg_processing_time` decimal(10,2) NOT NULL DEFAULT 0.00,
  `last_processed_at` timestamp NULL DEFAULT NULL,
  `last_failed_at` timestamp NULL DEFAULT NULL,
  `metric_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `conversation_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `sender_type` enum('customer','vendor') NOT NULL,
  `body` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2024_01_01_000001_create_roles_permissions_tables', 1),
(6, '2024_01_01_000002_create_vendors_tables', 1),
(7, '2024_01_01_000003_create_products_tables', 1),
(8, '2024_01_01_000004_create_orders_tables', 1),
(9, '2024_01_01_000005_create_payments_tables', 1),
(10, '2024_01_01_000006_create_reviews_tables', 1),
(11, '2024_01_01_000007_create_notifications_tables', 1),
(12, '2024_01_01_000008_create_ai_tables', 1),
(13, '2024_01_01_000009_create_system_tables', 1),
(14, '2024_01_01_000010_create_ai_simulation_and_safety_tables', 2),
(15, '2026_01_04_104018_create_messaging_tables', 3),
(16, '2026_01_04_110944_create_stock_histories_table', 4),
(17, '2026_01_04_111114_add_seo_and_ordering_to_products_tables', 5),
(18, '2026_01_04_111506_add_seo_and_social_to_vendors_table', 6),
(19, '2026_01_04_111114_create_product_variants_table', 7),
(20, '2026_01_04_115943_add_price_to_cart_items_table', 8),
(21, '2026_01_04_123057_create_stock_history_table', 9),
(22, '2026_01_04_110101_create_coupons_table', 10),
(23, '2026_01_04_135340_add_min_spend_to_coupons_table', 11),
(24, '2026_01_04_143000_fix_product_variants_schema', 11),
(25, '2026_01_04_144500_fix_coupons_name_nullable', 12),
(26, '2026_01_04_153000_fix_coupons_type_enum', 13),
(27, '2026_01_04_190211_create_disputes_table', 14);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `action_url` varchar(255) DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_number` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `address_id` bigint(20) UNSIGNED DEFAULT NULL,
  `shipping_method_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `shipping_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `coupon_code` varchar(255) DEFAULT NULL,
  `total` decimal(15,2) NOT NULL,
  `status` enum('pending','paid','processing','shipped','delivered','cancelled','refunded') NOT NULL DEFAULT 'pending',
  `payment_status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_reference` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `shipping_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`shipping_address`)),
  `paid_at` timestamp NULL DEFAULT NULL,
  `shipped_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `user_id`, `address_id`, `shipping_method_id`, `subtotal`, `shipping_cost`, `tax`, `discount`, `coupon_code`, `total`, `status`, `payment_status`, `payment_method`, `payment_reference`, `notes`, `shipping_address`, `paid_at`, `shipped_at`, `delivered_at`, `cancelled_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 'BN-FPG43UYY', 4, 2, NULL, 80000.00, 0.00, 0.00, 0.00, NULL, 80000.00, 'pending', 'pending', NULL, NULL, 'Kdkjdskdjkdkjdf', '{\"name\":\"Test Customer ABDOOL\",\"phone\":\"08087654321\",\"address\":\"bOSSO NGIGERIA NIGER STATE\",\"city\":\"kANO\",\"state\":\"Abia\"}', NULL, NULL, NULL, NULL, '2026-01-03 17:25:53', '2026-01-03 17:25:53', NULL),
(3, 'BN-PH3G1C1C', 4, 2, NULL, 100000.00, 0.00, 0.00, 0.00, NULL, 100000.00, 'paid', 'paid', NULL, 'BN_1767468422_3', NULL, '{\"name\":\"Test Customer ABDOOL\",\"phone\":\"08087654321\",\"address\":\"bOSSO NGIGERIA NIGER STATE\",\"city\":\"kANO\",\"state\":\"Abia\",\"tracking_id\":\"TRK-LM5NRTZTV8\"}', '2026-01-03 18:27:27', NULL, NULL, NULL, '2026-01-03 18:26:51', '2026-01-03 18:27:27', NULL),
(4, 'BN-SH18TLEQ', 4, 3, NULL, 250000.00, 0.00, 0.00, 0.00, NULL, 250000.00, 'paid', 'paid', NULL, 'BN_1769260731_4', 'please diliver', '{\"name\":\"Customer ABDOOL\",\"phone\":\"08087654321\",\"address\":\"bOSSO NGIGERIA NIGER STATE\",\"city\":\"nigiihi\",\"state\":\"Lagos\",\"tracking_id\":\"TRK-OQXUJ7JY9H\"}', '2026-01-24 12:19:08', NULL, NULL, NULL, '2026-01-24 12:18:36', '2026-01-24 12:19:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `product_variant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `variant_name` varchar(255) DEFAULT NULL,
  `price` decimal(15,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `vendor_commission` decimal(15,2) NOT NULL DEFAULT 0.00,
  `platform_commission` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','processing','shipped','delivered','cancelled','refunded') NOT NULL DEFAULT 'pending',
  `tracking_number` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `vendor_id`, `product_variant_id`, `product_name`, `variant_name`, `price`, `quantity`, `subtotal`, `vendor_commission`, `platform_commission`, `status`, `tracking_number`, `notes`, `created_at`, `updated_at`) VALUES
(1, 2, 2, 1, NULL, 'Electronics Item 2', NULL, 40000.00, 2, 80000.00, 0.00, 0.00, 'pending', NULL, NULL, '2026-01-03 17:25:53', '2026-01-03 17:55:49'),
(2, 3, 2, 1, NULL, 'Electronics Item 2', NULL, 40000.00, 2, 80000.00, 0.00, 0.00, 'delivered', 'TRK-LM5NRTZTV8', NULL, '2026-01-03 18:26:51', '2026-01-24 12:22:28'),
(3, 3, 4, 1, NULL, 'Fashion Item 2', NULL, 20000.00, 1, 20000.00, 0.00, 0.00, 'processing', 'TRK-LM5NRTZTV8', NULL, '2026-01-03 18:26:51', '2026-01-03 18:27:27'),
(4, 4, 20, 1, NULL, 'Services Item 2', NULL, 50000.00, 5, 250000.00, 0.00, 0.00, 'delivered', 'TRK-OQXUJ7JY9H', NULL, '2026-01-24 12:18:36', '2026-01-24 12:22:13');

-- --------------------------------------------------------

--
-- Table structure for table `order_status_history`
--

CREATE TABLE `order_status_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  `changed_by` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_gateways`
--

CREATE TABLE `payment_gateways` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `credentials` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`credentials`)),
  `webhook_secret` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`webhook_secret`)),
  `supports_split` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `is_test_mode` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_gateways`
--

INSERT INTO `payment_gateways` (`id`, `name`, `display_name`, `description`, `logo`, `credentials`, `webhook_secret`, `supports_split`, `is_active`, `is_test_mode`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'paystack', 'Paystack', 'Pay with card, bank transfer, or mobile money', NULL, NULL, NULL, 1, 0, 1, 1, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(2, 'flutterwave', 'Flutterwave', 'Pay with card, bank transfer, USSD, or mobile money', NULL, NULL, NULL, 1, 0, 1, 2, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(3, 'stripe', 'Stripe', 'International payments with card', NULL, NULL, NULL, 1, 0, 1, 3, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(4, 'wallet', 'Wallet', 'Pay from your BuyNiger wallet balance', NULL, NULL, NULL, 0, 1, 0, 4, '2026-01-03 11:01:16', '2026-01-03 11:01:16');

-- --------------------------------------------------------

--
-- Table structure for table `payment_transactions`
--

CREATE TABLE `payment_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reference` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payment_gateway_id` bigint(20) UNSIGNED DEFAULT NULL,
  `gateway_reference` varchar(255) DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `gateway_fee` decimal(15,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(255) NOT NULL DEFAULT 'NGN',
  `type` enum('payment','refund','wallet_credit','wallet_debit','payout') NOT NULL DEFAULT 'payment',
  `status` enum('pending','success','failed','cancelled') NOT NULL DEFAULT 'pending',
  `gateway_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gateway_response`)),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `display_name`, `module`, `description`, `created_at`, `updated_at`) VALUES
(1, 'users.view', 'View Users', 'users', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(2, 'users.create', 'Create Users', 'users', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(3, 'users.edit', 'Edit Users', 'users', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(4, 'users.delete', 'Delete Users', 'users', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(5, 'vendors.view', 'View Vendors', 'vendors', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(6, 'vendors.approve', 'Approve Vendors', 'vendors', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(7, 'vendors.suspend', 'Suspend Vendors', 'vendors', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(8, 'products.view', 'View Products', 'products', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(9, 'products.create', 'Create Products', 'products', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(10, 'products.edit', 'Edit Products', 'products', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(11, 'products.delete', 'Delete Products', 'products', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(12, 'products.moderate', 'Moderate Products', 'products', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(13, 'categories.manage', 'Manage Categories', 'categories', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(14, 'orders.view', 'View Orders', 'orders', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(15, 'orders.view_all', 'View All Orders', 'orders', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(16, 'orders.update', 'Update Orders', 'orders', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(17, 'orders.cancel', 'Cancel Orders', 'orders', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(18, 'orders.refund', 'Refund Orders', 'orders', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(19, 'payments.view', 'View Payments', 'payments', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(20, 'payments.configure', 'Configure Payments', 'payments', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(21, 'payouts.manage', 'Manage Payouts', 'payments', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(22, 'analytics.view', 'View Analytics', 'analytics', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(23, 'analytics.view_all', 'View All Analytics', 'analytics', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(24, 'ai.configure', 'Configure AI', 'ai', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(25, 'ai.use', 'Use AI Features', 'ai', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(26, 'ai.kill_switch', 'AI Kill Switch', 'ai', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(27, 'settings.view', 'View Settings', 'settings', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(28, 'settings.manage', 'Manage Settings', 'settings', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(29, 'campaigns.manage', 'Manage Campaigns', 'campaigns', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(30, 'reviews.moderate', 'Moderate Reviews', 'reviews', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(31, 'audit.view', 'View Audit Logs', 'audit', NULL, '2026-01-03 07:50:18', '2026-01-03 07:50:18');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `price_history`
--

CREATE TABLE `price_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `old_price` decimal(15,2) NOT NULL,
  `new_price` decimal(15,2) NOT NULL,
  `changed_by` varchar(255) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `short_description` text DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `price` decimal(15,2) NOT NULL,
  `sale_price` decimal(15,2) DEFAULT NULL,
  `cost_price` decimal(15,2) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `low_stock_threshold` int(11) NOT NULL DEFAULT 5,
  `unit` varchar(255) NOT NULL DEFAULT 'piece',
  `weight` decimal(10,2) DEFAULT NULL,
  `dimensions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dimensions`)),
  `status` enum('draft','active','inactive','out_of_stock') NOT NULL DEFAULT 'draft',
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_digital` tinyint(1) NOT NULL DEFAULT 0,
  `digital_file` varchar(255) DEFAULT NULL,
  `view_count` int(11) NOT NULL DEFAULT 0,
  `order_count` int(11) NOT NULL DEFAULT 0,
  `rating` decimal(3,2) NOT NULL DEFAULT 0.00,
  `rating_count` int(11) NOT NULL DEFAULT 0,
  `meta_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta_data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `vendor_id`, `category_id`, `name`, `slug`, `short_description`, `description`, `sku`, `price`, `sale_price`, `cost_price`, `quantity`, `low_stock_threshold`, `unit`, `weight`, `dimensions`, `status`, `meta_title`, `meta_description`, `is_featured`, `is_digital`, `digital_file`, `view_count`, `order_count`, `rating`, `rating_count`, `meta_data`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'Electronics Item 1', 'electronics-item-1', 'Quality Electronics item.', 'Premium Electronics product designed for the Nigerian market. Quality guaranteed.', 'BN-17VKM5EZ', 5000.00, NULL, NULL, 20, 5, 'piece', NULL, NULL, 'active', NULL, NULL, 1, 0, NULL, 0, 0, 0.00, 0, NULL, '2026-01-03 13:30:12', '2026-01-24 12:12:38', NULL),
(2, 1, 1, 'Electronics Item 2', 'electronics-item-2', 'Quality Electronics item.', 'Premium Electronics product designed for the Nigerian market. Quality guaranteed.', 'BN-ALMUC4D7', 40000.00, NULL, NULL, 83, 5, 'piece', NULL, NULL, 'active', NULL, NULL, 1, 0, NULL, 0, 0, 0.00, 0, NULL, '2026-01-03 13:30:12', '2026-01-03 18:26:51', NULL),
(3, 1, 2, 'Fashion Item 1', 'fashion-item-1', 'Quality Fashion item.', 'Premium Fashion product designed for the Nigerian market. Quality guaranteed.', 'BN-GYBYMWD5', 15000.00, NULL, NULL, 53, 5, 'piece', NULL, NULL, 'active', NULL, NULL, 1, 0, NULL, 0, 0, 0.00, 0, NULL, '2026-01-03 13:30:12', '2026-01-03 13:30:12', NULL),
(4, 1, 2, 'Fashion Item 2', 'fashion-item-2', 'Quality Fashion item.', 'Premium Fashion product designed for the Nigerian market. Quality guaranteed.', 'BN-B308R1NR', 20000.00, NULL, NULL, 49, 5, 'piece', NULL, NULL, 'active', NULL, NULL, 1, 0, NULL, 0, 0, 0.00, 0, NULL, '2026-01-03 13:30:12', '2026-01-03 18:26:51', NULL),
(5, 1, 3, 'Home & Garden Item 1', 'home-garden-item-1', 'Quality Home & Garden item.', 'Premium Home & Garden product designed for the Nigerian market. Quality guaranteed.', 'BN-M0101ONH', 25000.00, NULL, NULL, 22, 5, 'piece', NULL, NULL, 'active', NULL, NULL, 1, 0, NULL, 0, 0, 0.00, 0, NULL, '2026-01-03 13:30:12', '2026-01-03 13:30:12', NULL),
(6, 1, 3, 'Home & Garden Item 2', 'home-garden-item-2', 'Quality Home & Garden item.', 'Premium Home & Garden product designed for the Nigerian market. Quality guaranteed.', 'BN-QGWVXCXB', 40000.00, NULL, NULL, 63, 5, 'piece', NULL, NULL, 'active', NULL, NULL, 1, 0, NULL, 0, 0, 0.00, 0, NULL, '2026-01-03 13:30:12', '2026-01-03 13:30:12', NULL),
(7, 1, 4, 'Health & Beauty Item 1', 'health-beauty-item-1', 'Quality Health & Beauty item.', 'Premium Health & Beauty product designed for the Nigerian market. Quality guaranteed.', 'BN-HHPSIFO9', 10000.00, NULL, NULL, 90, 5, 'piece', NULL, NULL, 'active', NULL, NULL, 1, 0, NULL, 0, 0, 0.00, 0, NULL, '2026-01-03 13:30:12', '2026-01-03 13:30:12', NULL),
(8, 1, 4, 'Health & Beauty Item 2', 'health-beauty-item-2', 'Quality Health & Beauty item.', 'Premium Health & Beauty product designed for the Nigerian market. Quality guaranteed.', 'BN-CVOJTXYL', 10000.00, NULL, NULL, 38, 5, 'piece', NULL, NULL, 'active', NULL, NULL, 1, 0, NULL, 0, 0, 0.00, 0, NULL, '2026-01-03 13:30:12', '2026-01-03 13:30:12', NULL),
(9, 1, 5, 'Sports & Outdoors Item 1', 'sports-outdoors-item-1', 'Quality Sports & Outdoors item.', 'Premium Sports & Outdoors product designed for the Nigerian market. Quality guaranteed.', 'BN-G9CDSL5F', 10000.00, NULL, NULL, 67, 5, 'piece', NULL, NULL, 'active', NULL, NULL, 1, 0, NULL, 0, 0, 0.00, 0, NULL, '2026-01-03 13:30:12', '2026-01-03 13:30:12', NULL),
(10, 1, 5, 'Sports & Outdoors Item 2', 'sports-outdoors-item-2', 'Quality Sports & Outdoors item.', 'Premium Sports & Outdoors product designed for the Nigerian market. Quality guaranteed.', 'BN-AKBER54M', 50000.00, NULL, NULL, 45, 5, 'piece', NULL, NULL, 'active', NULL, NULL, 1, 0, NULL, 0, 0, 0.00, 0, NULL, '2026-01-03 13:30:12', '2026-01-03 13:30:12', NULL),
(11, 1, 6, 'Automotive Item 1', 'automotive-item-1', 'Quality Automotive item.', 'Premium Automotive product designed for the Nigerian market. Quality guaranteed.', 'BN-NSBSMD03', 5000.00, NULL, NULL, 37, 5, 'piece', NULL, NULL, 'active', NULL, NULL, 1, 0, NULL, 0, 0, 0.00, 0, NULL, '2026-01-03 13:30:12', '2026-01-03 13:30:12', NULL),
(12, 1, 6, 'Automotive Item 2', 'automotive-item-2', 'Quality Automotive item.', 'Premium Automotive product designed for the Nigerian market. Quality guaranteed.', 'BN-IAEXKVHD', 50000.00, NULL, NULL, 41, 5, 'piece', NULL, NULL, 'active', NULL, NULL, 1, 0, NULL, 0, 0, 0.00, 0, NULL, '2026-01-03 13:30:12', '2026-01-03 13:30:12', NULL),
(13, 1, 7, 'Books & Media Item 1', 'books-media-item-1', 'Quality Books & Media item.', 'Premium Books & Media product designed for the Nigerian market. Quality guaranteed.', 'BN-U9VH9GOP', 25000.00, NULL, NULL, 77, 5, 'piece', NULL, NULL, 'active', NULL, NULL, 1, 0, NULL, 0, 0, 0.00, 0, NULL, '2026-01-03 13:30:12', '2026-01-03 13:30:12', NULL),
(14, 1, 7, 'Books & Media Item 2', 'books-media-item-2', 'Quality Books & Media item.', 'Premium Books & Media product designed for the Nigerian market. Quality guaranteed.', 'BN-3G8Q38VU', 30000.00, NULL, NULL, 79, 5, 'piece', NULL, NULL, 'active', NULL, NULL, 1, 0, NULL, 0, 0, 0.00, 0, NULL, '2026-01-03 13:30:12', '2026-01-03 13:30:12', NULL),
(15, 1, 8, 'Food & Groceries Item 1', 'food-groceries-item-1', 'Quality Food & Groceries item.', 'Premium Food & Groceries product designed for the Nigerian market. Quality guaranteed.', 'BN-6LRJRZCM', 25000.00, NULL, NULL, 78, 5, 'piece', NULL, NULL, 'active', NULL, NULL, 1, 0, NULL, 0, 0, 0.00, 0, NULL, '2026-01-03 13:30:12', '2026-01-03 13:30:12', NULL),
(16, 1, 8, 'Food & Groceries Item 2', 'food-groceries-item-2', 'Quality Food & Groceries item.', 'Premium Food & Groceries product designed for the Nigerian market. Quality guaranteed.', 'BN-HUA3NO8Y', 10000.00, NULL, NULL, 82, 5, 'piece', NULL, NULL, 'active', NULL, NULL, 1, 0, NULL, 0, 0, 0.00, 0, NULL, '2026-01-03 13:30:12', '2026-01-03 13:30:12', NULL),
(17, 1, 9, 'Toys & Games Item 1', 'toys-games-item-1', 'Quality Toys & Games item.', 'Premium Toys & Games product designed for the Nigerian market. Quality guaranteed.', 'BN-ZRWPZWVP', 10000.00, NULL, NULL, 56, 5, 'piece', NULL, NULL, 'active', NULL, NULL, 1, 0, NULL, 0, 0, 0.00, 0, NULL, '2026-01-03 13:30:12', '2026-01-03 13:30:12', NULL),
(18, 1, 9, 'Toys & Games Item 2', 'toys-games-item-2', 'Quality Toys & Games item.', 'Premium Toys & Games product designed for the Nigerian market. Quality guaranteed.', 'BN-VLJA9KGH', 10000.00, NULL, NULL, 47, 5, 'piece', NULL, NULL, 'active', NULL, NULL, 1, 0, NULL, 0, 0, 0.00, 0, NULL, '2026-01-03 13:30:12', '2026-01-03 13:30:12', NULL),
(19, 1, 10, 'Services Item 1', 'services-item-1', 'Quality Services item.', 'Premium Services product designed for the Nigerian market. Quality guaranteed.', 'BN-W2OXXTGE', 10000.00, NULL, NULL, 50, 5, 'piece', NULL, NULL, 'active', NULL, NULL, 1, 0, NULL, 0, 0, 0.00, 0, NULL, '2026-01-03 13:30:12', '2026-01-04 06:57:13', '2026-01-04 06:57:13'),
(20, 1, 10, 'Services Item 2', 'services-item-2', 'Premium Services product designed for the Nigerian market. Quality guaranteed.', 'Premium Services product designed for the Nigerian market. Quality guaranteed.', 'BN-KCGJP7VV', 50000.00, NULL, NULL, 91, 5, 'piece', NULL, NULL, 'active', NULL, NULL, 1, 0, NULL, 0, 5, 0.00, 0, NULL, '2026-01-03 13:30:13', '2026-01-24 12:19:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `alt_text`, `sort_order`, `is_primary`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 20, 'products/twmu5cXNMLd9Pr6jSoU46AJobD2QcPV7QxWH47J1.jpg', NULL, 0, 1, 0, '2026-01-03 17:29:48', '2026-01-04 13:19:43'),
(2, 20, 'products/IBeskWS7OURHMIK7D3hFfVrGsBOmvWBo10BU25N5.jpg', NULL, 2, 0, 0, '2026-01-03 17:52:17', '2026-01-04 13:19:43'),
(3, 20, 'products/OCPGA7l4WNlGy2IhBM7ad56J229afywh71W3ZAhQ.jpg', NULL, 1, 0, 0, '2026-01-04 10:40:40', '2026-01-04 13:19:43'),
(5, 20, 'products/WHzMPY4C0Pbntz4YxEILEoN8EMLIpRfF0d5v7GsN.webp', NULL, 3, 0, 0, '2026-01-04 13:13:36', '2026-01-04 13:19:43'),
(6, 20, 'products/vITAXpzaFrthA5VokYI9uRrMTkOiu8LvhhQFcQHc.webp', NULL, 4, 0, 0, '2026-01-04 13:14:09', '2026-01-04 13:19:43');

-- --------------------------------------------------------

--
-- Table structure for table `product_tags`
--

CREATE TABLE `product_tags` (
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `tag_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `size` varchar(255) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `is_verified_purchase` tinyint(1) NOT NULL DEFAULT 0,
  `is_approved` tinyint(1) NOT NULL DEFAULT 1,
  `admin_notes` text DEFAULT NULL,
  `helpful_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `review_votes`
--

CREATE TABLE `review_votes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `review_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `is_helpful` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `display_name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'super_admin', 'Super Admin', 'Full system access with god mode privileges', '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(2, 'admin', 'Admin', 'Platform administrator with operational authority', '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(3, 'vendor', 'Vendor', 'Store owner with product and order management', '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(4, 'customer', 'Customer', 'End user who shops on the platform', '2026-01-03 07:50:18', '2026-01-03 07:50:18');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `permission_id`, `created_at`, `updated_at`) VALUES
(1, 1, 24, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(2, 1, 26, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(3, 1, 25, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(4, 1, 22, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(5, 1, 23, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(6, 1, 31, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(7, 1, 29, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(8, 1, 13, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(9, 1, 17, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(10, 1, 18, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(11, 1, 16, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(12, 1, 14, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(13, 1, 15, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(14, 1, 20, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(15, 1, 19, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(16, 1, 21, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(17, 1, 9, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(18, 1, 11, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(19, 1, 10, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(20, 1, 12, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(21, 1, 8, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(22, 1, 30, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(23, 1, 28, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(24, 1, 27, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(25, 1, 2, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(26, 1, 4, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(27, 1, 3, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(28, 1, 1, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(29, 1, 6, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(30, 1, 7, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(31, 1, 5, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(32, 2, 25, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(33, 2, 22, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(34, 2, 23, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(35, 2, 31, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(36, 2, 29, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(37, 2, 13, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(38, 2, 17, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(39, 2, 18, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(40, 2, 16, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(41, 2, 14, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(42, 2, 15, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(43, 2, 19, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(44, 2, 21, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(45, 2, 9, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(46, 2, 11, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(47, 2, 10, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(48, 2, 12, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(49, 2, 8, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(50, 2, 30, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(51, 2, 27, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(52, 2, 2, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(53, 2, 4, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(54, 2, 3, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(55, 2, 1, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(56, 2, 6, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(57, 2, 7, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(58, 2, 5, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(59, 3, 25, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(60, 3, 22, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(61, 3, 16, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(62, 3, 14, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(63, 3, 9, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(64, 3, 11, '2026-01-03 07:50:18', '2026-01-03 07:50:18'),
(65, 3, 10, '2026-01-03 07:50:19', '2026-01-03 07:50:19'),
(66, 3, 8, '2026-01-03 07:50:19', '2026-01-03 07:50:19');

-- --------------------------------------------------------

--
-- Table structure for table `rollback_records`
--

CREATE TABLE `rollback_records` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rollback_type` varchar(255) NOT NULL,
  `entity_type` varchar(255) NOT NULL,
  `entity_id` bigint(20) UNSIGNED NOT NULL,
  `original_state` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`original_state`)),
  `changed_state` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`changed_state`)),
  `rollback_state` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`rollback_state`)),
  `status` enum('pending','completed','failed') NOT NULL DEFAULT 'pending',
  `reason` text DEFAULT NULL,
  `initiated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `search_index_queue`
--

CREATE TABLE `search_index_queue` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `indexable_type` varchar(255) NOT NULL,
  `indexable_id` bigint(20) UNSIGNED NOT NULL,
  `action` enum('index','update','delete') NOT NULL DEFAULT 'index',
  `status` enum('pending','processing','completed','failed') NOT NULL DEFAULT 'pending',
  `attempts` int(11) NOT NULL DEFAULT 0,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shipping_methods`
--

CREATE TABLE `shipping_methods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `base_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `per_kg_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `estimated_days` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shipping_methods`
--

INSERT INTO `shipping_methods` (`id`, `name`, `description`, `base_cost`, `per_kg_cost`, `estimated_days`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Standard Delivery', 'Delivery within 3-5 business days', 1500.00, 0.00, '3-5 days', 1, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(2, 'Express Delivery', 'Delivery within 1-2 business days', 3500.00, 0.00, '1-2 days', 1, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(3, 'Same Day Delivery', 'Delivery within hours (Lagos only)', 5000.00, 0.00, 'Same day', 1, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(4, 'Pickup Station', 'Pick up from nearest station', 500.00, 0.00, '2-3 days', 1, '2026-01-03 11:01:16', '2026-01-03 11:01:16');

-- --------------------------------------------------------

--
-- Table structure for table `stock_histories`
--

CREATE TABLE `stock_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `old_quantity` int(11) NOT NULL,
  `new_quantity` int(11) NOT NULL,
  `type` enum('restock','sale','correction','return') NOT NULL,
  `reason` text DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_history`
--

CREATE TABLE `stock_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `product_variant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `change_amount` int(11) NOT NULL,
  `new_stock_level` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_health_metrics`
--

CREATE TABLE `system_health_metrics` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `metric_type` varchar(255) NOT NULL,
  `metric_name` varchar(255) NOT NULL,
  `value` decimal(15,4) NOT NULL,
  `unit` varchar(255) DEFAULT NULL,
  `status` enum('normal','warning','critical') NOT NULL DEFAULT 'normal',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_modes`
--

CREATE TABLE `system_modes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mode_type` varchar(255) NOT NULL,
  `mode_value` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `changed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `changed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_modes`
--

INSERT INTO `system_modes` (`id`, `mode_type`, `mode_value`, `description`, `changed_by`, `changed_at`, `created_at`, `updated_at`) VALUES
(1, 'ai_mode', 'shadow', 'Controls AI execution mode. Shadow mode logs actions without executing.', NULL, NULL, '2026-01-03 07:59:53', '2026-01-03 07:59:53'),
(2, 'maintenance_mode', 'off', 'When enabled, platform shows maintenance page.', NULL, NULL, '2026-01-03 07:59:53', '2026-01-03 07:59:53');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `group` varchar(255) NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` longtext DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'string',
  `description` text DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `group`, `key`, `value`, `type`, `description`, `is_public`, `created_at`, `updated_at`) VALUES
(1, 'general', 'site_name', 'BuyNiger', 'string', NULL, 1, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(2, 'general', 'site_tagline', 'AI-Powered Multi-Vendor Marketplace', 'string', NULL, 1, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(3, 'general', 'site_description', 'The future of e-commerce powered by AI', 'string', NULL, 1, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(4, 'general', 'site_logo', NULL, 'string', NULL, 1, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(5, 'general', 'site_favicon', NULL, 'string', NULL, 1, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(6, 'general', 'currency', 'NGN', 'string', NULL, 1, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(7, 'general', 'currency_symbol', '$', 'string', NULL, 1, '2026-01-03 11:01:16', '2026-01-04 18:45:10'),
(8, 'general', 'timezone', 'Africa/Lagos', 'string', NULL, 0, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(9, 'general', 'date_format', 'd M, Y', 'string', NULL, 0, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(10, 'general', 'maintenance_mode', '0', 'boolean', NULL, 0, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(11, 'commission', 'default_commission_rate', '10', 'number', NULL, 0, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(12, 'commission', 'min_payout_amount', '5000', 'number', NULL, 0, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(13, 'commission', 'payout_hold_days', '7', 'number', NULL, 0, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(14, 'ai', 'default_provider', 'grok', 'string', NULL, 0, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(15, 'ai', 'ai_enabled', '1', 'boolean', NULL, 0, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(16, 'ai', 'auto_execute_enabled', '0', 'boolean', NULL, 0, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(17, 'ai', 'max_price_change_percent', '20', 'number', NULL, 0, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(18, 'email', 'from_name', 'BuyNiger', 'string', NULL, 0, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(19, 'email', 'from_email', 'noreply@buyniger.com', 'string', NULL, 0, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(20, 'email', 'support_email', 'support@buyniger.com', 'string', NULL, 1, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(21, 'social', 'facebook_url', '', 'string', NULL, 1, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(22, 'social', 'twitter_url', '', 'string', NULL, 1, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(23, 'social', 'instagram_url', '', 'string', NULL, 1, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(24, 'general', 'phone', '08122598372', 'string', NULL, 1, '2026-01-03 11:01:16', '2026-01-03 11:37:20'),
(25, 'contact', 'address', 'Nigeria', 'string', NULL, 1, '2026-01-03 11:01:16', '2026-01-03 11:01:16'),
(27, 'general', 'app_name', 'BuyNiger', 'string', NULL, 0, '2026-01-03 11:37:20', '2026-01-03 11:37:20'),
(28, 'general', 'currency_code', 'NGN', 'string', NULL, 0, '2026-01-03 11:37:20', '2026-01-05 07:59:31'),
(29, 'general', 'contact_email', 'support@buyniger.com', 'string', NULL, 0, '2026-01-03 11:37:20', '2026-01-03 11:37:20'),
(30, 'general', 'ai_gemini_key', 'AIzaSy...', 'string', NULL, 0, '2026-01-04 19:08:44', '2026-01-04 19:08:44'),
(31, 'general', 'ai_gemini_model', 'gemini-pro', 'string', NULL, 0, '2026-01-04 19:08:44', '2026-01-04 19:08:44'),
(32, 'general', 'ai_openai_key', '', 'string', NULL, 0, '2026-01-04 19:08:44', '2026-01-04 19:08:44'),
(33, 'general', 'language', 'en', 'string', NULL, 0, '2026-01-05 07:59:31', '2026-01-05 07:59:31');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `temp_files`
--

CREATE TABLE `temp_files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `processed` tinyint(1) NOT NULL DEFAULT 0,
  `moved_to_permanent` tinyint(1) NOT NULL DEFAULT 0,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL DEFAULT 4,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `address` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `phone`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `address`, `avatar`, `is_active`, `last_login_at`, `deleted_at`) VALUES
(1, 1, 'Super Admin', 'superadmin@buyniger.com', '08122598372', '2026-01-03 07:50:19', '$2y$12$G980/mdgs59lQmK5mi3pyuRAFxfJlUkxQLcijaPjvlVeWztvsrDaq', 'A6yGlIwesf1b8ynCLPumDWIk2EGsjGJdTUos4373WTiCfrsnH2Z82xRGfEuk', '2026-01-03 07:50:19', '2026-01-05 08:27:58', NULL, NULL, 1, '2026-01-05 08:27:58', NULL),
(2, 2, 'Admin User', 'admin@buyniger.com', '07049906420', '2026-01-03 07:50:19', '$2y$12$xC.0Yx0Ic.kpTOSjHG2zzel.GWqhV.x1kCjBjihxEOU.4.z6t8R6W', 'gi9R3RtkT2doaLTtMRdBdZdM8AbrUPM70R3jPoIY6ZsawHBzVsz6RcGtKyVV', '2026-01-03 07:50:19', '2026-01-24 12:11:51', NULL, NULL, 1, '2026-01-24 12:11:51', NULL),
(3, 3, 'Test Vendor', 'vendor@test.com', '08012345678', '2026-01-03 07:50:19', '$2y$12$L8cpFLUq1Ta4Cv3b0yn5ZeYeCkO3qA8FDwjoToXDiwkmxIVqeeWgy', 'xvfja7itJr907Vb4RbjJWfKDOudCiiKc7q22PjX5dA2DZchsy1mIlsfqXuE2', '2026-01-03 07:50:20', '2026-01-24 12:20:20', NULL, NULL, 1, '2026-01-24 12:20:20', NULL),
(4, 4, 'Customer', 'customer@test.com', '08087654321', '2026-01-03 07:50:20', '$2y$12$Spe7tRNftL7udLBC1YVuBOEsEqbl/7/IDr1V45nUlCtQTfMnKb8YC', 'Da9rKXsdK82PXT4rg9DiGj0cAeEowTBzVUlRd4JaTQfB9J9EOW3M0JQlwt6I', '2026-01-03 07:50:20', '2026-01-24 12:36:41', NULL, NULL, 1, '2026-01-24 12:36:41', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `store_name` varchar(255) NOT NULL,
  `store_slug` varchar(255) NOT NULL,
  `store_description` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `banner` varchar(255) DEFAULT NULL,
  `primary_color` varchar(255) NOT NULL DEFAULT '#0066FF',
  `business_email` varchar(255) DEFAULT NULL,
  `business_phone` varchar(255) DEFAULT NULL,
  `business_address` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) NOT NULL DEFAULT 'Nigeria',
  `status` enum('pending','approved','suspended','rejected') NOT NULL DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `commission_rate` decimal(5,2) NOT NULL DEFAULT 10.00,
  `total_sales` decimal(15,2) NOT NULL DEFAULT 0.00,
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_products` int(11) NOT NULL DEFAULT 0,
  `total_orders` int(11) NOT NULL DEFAULT 0,
  `rating` decimal(3,2) NOT NULL DEFAULT 0.00,
  `rating_count` int(11) NOT NULL DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`id`, `user_id`, `store_name`, `store_slug`, `store_description`, `logo`, `banner`, `primary_color`, `business_email`, `business_phone`, `business_address`, `city`, `state`, `country`, `status`, `rejection_reason`, `commission_rate`, `total_sales`, `balance`, `total_products`, `total_orders`, `rating`, `rating_count`, `meta_title`, `meta_description`, `facebook`, `twitter`, `instagram`, `is_featured`, `approved_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, 'Test Store', 'test-store', 'This is a test vendor store for demonstration purposes.', 'vendors/logos/8UpIYnsP3L8cVDyqVQ9ip2AWdVA3tiTdBkCfwHKB.jpg', 'vendors/banners/i1KpuWqhkea7gUgKid9OrTNlwJTzi93LKjBmKnp1.jpg', '#0066FF', 'vendor@test.com', '08012345678', NULL, 'Lagos', 'Lagos', 'Nigeria', 'approved', NULL, 10.00, 250000.00, 225000.00, 0, 0, 0.00, 0, 'STORE1', 'test test test', 'https://facebook.com/techgate', 'https://twitter.com/techgate', 'https://instagram.com/techgate', 0, '2026-01-03 07:50:20', '2026-01-03 07:50:20', '2026-01-24 12:19:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vendor_bank_details`
--

CREATE TABLE `vendor_bank_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `account_name` varchar(255) NOT NULL,
  `account_number` varchar(255) NOT NULL,
  `bank_code` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `is_primary` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendor_bank_details`
--

INSERT INTO `vendor_bank_details` (`id`, `vendor_id`, `bank_name`, `account_name`, `account_number`, `bank_code`, `is_verified`, `is_primary`, `created_at`, `updated_at`) VALUES
(1, 1, 'Palmpay', 'Shuaib Abdools', '8122598372', NULL, 0, 1, '2026-01-04 12:35:20', '2026-01-04 12:35:20');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_commissions`
--

CREATE TABLE `vendor_commissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `order_item_id` bigint(20) UNSIGNED NOT NULL,
  `order_amount` decimal(15,2) NOT NULL,
  `commission_rate` decimal(5,2) NOT NULL,
  `platform_commission` decimal(15,2) NOT NULL,
  `vendor_amount` decimal(15,2) NOT NULL,
  `status` enum('pending','available','paid') NOT NULL DEFAULT 'pending',
  `available_at` timestamp NULL DEFAULT NULL,
  `payout_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_documents`
--

CREATE TABLE `vendor_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `document_type` varchar(255) NOT NULL,
  `document_path` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_payouts`
--

CREATE TABLE `vendor_payouts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `reference` varchar(255) NOT NULL,
  `status` enum('pending','processing','completed','failed') NOT NULL DEFAULT 'pending',
  `payment_method` varchar(255) NOT NULL,
  `payment_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payment_details`)),
  `notes` text DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_reviews`
--

CREATE TABLE `vendor_reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `rating` tinyint(4) NOT NULL,
  `comment` text DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `pending_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`id`, `user_id`, `balance`, `pending_balance`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 4, 50000.00, 0.00, 1, '2026-01-03 07:50:20', '2026-01-03 07:50:20');

-- --------------------------------------------------------

--
-- Table structure for table `wallet_transactions`
--

CREATE TABLE `wallet_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `wallet_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `reference` varchar(255) NOT NULL,
  `type` enum('credit','debit') NOT NULL DEFAULT 'credit',
  `amount` decimal(15,2) NOT NULL,
  `balance_before` decimal(15,2) NOT NULL,
  `balance_after` decimal(15,2) NOT NULL,
  `source` varchar(255) NOT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `addresses_user_id_foreign` (`user_id`);

--
-- Indexes for table `ai_actions`
--
ALTER TABLE `ai_actions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ai_actions_user_id_foreign` (`user_id`),
  ADD KEY `ai_actions_ai_provider_id_foreign` (`ai_provider_id`),
  ADD KEY `ai_actions_approved_by_foreign` (`approved_by`),
  ADD KEY `ai_actions_vendor_id_action_type_index` (`vendor_id`,`action_type`),
  ADD KEY `ai_actions_module_created_at_index` (`module`,`created_at`);

--
-- Indexes for table `ai_action_limits`
--
ALTER TABLE `ai_action_limits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ai_action_limits_action_vendor_id_unique` (`action`,`vendor_id`),
  ADD KEY `ai_action_limits_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `ai_chat_messages`
--
ALTER TABLE `ai_chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ai_chat_messages_session_id_foreign` (`session_id`);

--
-- Indexes for table `ai_chat_sessions`
--
ALTER TABLE `ai_chat_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ai_chat_sessions_user_id_foreign` (`user_id`),
  ADD KEY `ai_chat_sessions_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `ai_emergency_status`
--
ALTER TABLE `ai_emergency_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ai_emergency_status_triggered_by_foreign` (`triggered_by`);

--
-- Indexes for table `ai_liability_logs`
--
ALTER TABLE `ai_liability_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ai_liability_logs_ai_simulation_id_foreign` (`ai_simulation_id`),
  ADD KEY `ai_liability_logs_ai_action_id_foreign` (`ai_action_id`),
  ADD KEY `ai_liability_logs_affected_user_id_foreign` (`affected_user_id`),
  ADD KEY `ai_liability_logs_affected_entity_type_affected_entity_id_index` (`affected_entity_type`,`affected_entity_id`);

--
-- Indexes for table `ai_memory`
--
ALTER TABLE `ai_memory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ai_memory_vendor_id_memory_type_index` (`vendor_id`,`memory_type`),
  ADD KEY `ai_memory_entity_type_entity_id_index` (`entity_type`,`entity_id`);

--
-- Indexes for table `ai_permissions`
--
ALTER TABLE `ai_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ai_permissions_ai_role_action_resource_unique` (`ai_role`,`action`,`resource`);

--
-- Indexes for table `ai_policies`
--
ALTER TABLE `ai_policies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ai_policies_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `ai_providers`
--
ALTER TABLE `ai_providers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ai_simulations`
--
ALTER TABLE `ai_simulations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ai_simulations_approved_by_foreign` (`approved_by`),
  ADD KEY `ai_simulations_vendor_id_ai_role_executed_index` (`vendor_id`,`ai_role`,`executed`),
  ADD KEY `ai_simulations_risk_level_approved_index` (`risk_level`,`approved`);

--
-- Indexes for table `analytics_daily`
--
ALTER TABLE `analytics_daily`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `analytics_daily_date_vendor_id_unique` (`date`,`vendor_id`),
  ADD KEY `analytics_daily_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `analytics_events`
--
ALTER TABLE `analytics_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `analytics_events_event_type_created_at_index` (`event_type`,`created_at`),
  ADD KEY `analytics_events_user_id_event_type_index` (`user_id`,`event_type`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_model_type_model_id_index` (`model_type`,`model_id`),
  ADD KEY `audit_logs_user_id_created_at_index` (`user_id`,`created_at`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `carts_user_id_index` (`user_id`),
  ADD KEY `carts_session_id_index` (`session_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cart_items_cart_id_product_id_product_variant_id_unique` (`cart_id`,`product_id`,`product_variant_id`),
  ADD KEY `cart_items_product_id_foreign` (`product_id`),
  ADD KEY `cart_items_product_variant_id_foreign` (`product_variant_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`),
  ADD KEY `categories_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `conversations_user_id_vendor_id_unique` (`user_id`,`vendor_id`),
  ADD KEY `conversations_product_id_foreign` (`product_id`),
  ADD KEY `conversations_vendor_id_last_message_at_index` (`vendor_id`,`last_message_at`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `coupons_code_unique` (`code`),
  ADD KEY `coupons_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `coupon_usages`
--
ALTER TABLE `coupon_usages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `coupon_usages_coupon_id_foreign` (`coupon_id`),
  ADD KEY `coupon_usages_user_id_foreign` (`user_id`),
  ADD KEY `coupon_usages_order_id_foreign` (`order_id`);

--
-- Indexes for table `delivery_tracking`
--
ALTER TABLE `delivery_tracking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `delivery_tracking_order_item_id_foreign` (`order_item_id`),
  ADD KEY `delivery_tracking_order_id_created_at_index` (`order_id`,`created_at`);

--
-- Indexes for table `disputes`
--
ALTER TABLE `disputes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `disputes_user_id_foreign` (`user_id`),
  ADD KEY `disputes_order_id_foreign` (`order_id`);

--
-- Indexes for table `email_campaigns`
--
ALTER TABLE `email_campaigns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email_campaigns_created_by_foreign` (`created_by`);

--
-- Indexes for table `email_logs`
--
ALTER TABLE `email_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email_logs_user_id_foreign` (`user_id`),
  ADD KEY `email_logs_campaign_id_foreign` (`campaign_id`),
  ADD KEY `email_logs_email_status_index` (`email`,`status`);

--
-- Indexes for table `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_templates_name_unique` (`name`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `feature_toggles`
--
ALTER TABLE `feature_toggles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `feature_toggles_feature_unique` (`feature`);

--
-- Indexes for table `job_metrics`
--
ALTER TABLE `job_metrics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `job_metrics_job_class_queue_metric_date_unique` (`job_class`,`queue`,`metric_date`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_sender_id_foreign` (`sender_id`),
  ADD KEY `messages_conversation_id_created_at_index` (`conversation_id`,`created_at`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_read_at_index` (`user_id`,`read_at`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_number_unique` (`order_number`),
  ADD KEY `orders_address_id_foreign` (`address_id`),
  ADD KEY `orders_shipping_method_id_foreign` (`shipping_method_id`),
  ADD KEY `orders_user_id_status_index` (`user_id`,`status`),
  ADD KEY `orders_status_created_at_index` (`status`,`created_at`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_product_id_foreign` (`product_id`),
  ADD KEY `order_items_product_variant_id_foreign` (`product_variant_id`),
  ADD KEY `order_items_vendor_id_status_index` (`vendor_id`,`status`);

--
-- Indexes for table `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_status_history_order_id_foreign` (`order_id`),
  ADD KEY `order_status_history_user_id_foreign` (`user_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payment_gateways`
--
ALTER TABLE `payment_gateways`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_transactions_reference_unique` (`reference`),
  ADD KEY `payment_transactions_order_id_foreign` (`order_id`),
  ADD KEY `payment_transactions_payment_gateway_id_foreign` (`payment_gateway_id`),
  ADD KEY `payment_transactions_user_id_status_index` (`user_id`,`status`),
  ADD KEY `payment_transactions_reference_index` (`reference`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_unique` (`name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `price_history`
--
ALTER TABLE `price_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `price_history_product_id_foreign` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_slug_unique` (`slug`),
  ADD KEY `products_vendor_id_status_index` (`vendor_id`,`status`),
  ADD KEY `products_category_id_status_index` (`category_id`,`status`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_images_product_id_foreign` (`product_id`);

--
-- Indexes for table `product_tags`
--
ALTER TABLE `product_tags`
  ADD PRIMARY KEY (`product_id`,`tag_id`),
  ADD KEY `product_tags_tag_id_foreign` (`tag_id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_variants_product_id_foreign` (`product_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reviews_user_id_product_id_order_id_unique` (`user_id`,`product_id`,`order_id`),
  ADD KEY `reviews_order_id_foreign` (`order_id`),
  ADD KEY `reviews_vendor_id_foreign` (`vendor_id`),
  ADD KEY `reviews_product_id_is_approved_index` (`product_id`,`is_approved`);

--
-- Indexes for table `review_votes`
--
ALTER TABLE `review_votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `review_votes_review_id_user_id_unique` (`review_id`,`user_id`),
  ADD KEY `review_votes_user_id_foreign` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_permissions_role_id_permission_id_unique` (`role_id`,`permission_id`),
  ADD KEY `role_permissions_permission_id_foreign` (`permission_id`);

--
-- Indexes for table `rollback_records`
--
ALTER TABLE `rollback_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rollback_records_initiated_by_foreign` (`initiated_by`),
  ADD KEY `rollback_records_entity_type_entity_id_index` (`entity_type`,`entity_id`);

--
-- Indexes for table `search_index_queue`
--
ALTER TABLE `search_index_queue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `search_index_queue_status_created_at_index` (`status`,`created_at`);

--
-- Indexes for table `shipping_methods`
--
ALTER TABLE `shipping_methods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_histories`
--
ALTER TABLE `stock_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_histories_vendor_id_foreign` (`vendor_id`),
  ADD KEY `stock_histories_product_id_foreign` (`product_id`),
  ADD KEY `stock_histories_user_id_foreign` (`user_id`);

--
-- Indexes for table `stock_history`
--
ALTER TABLE `stock_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_history_product_id_foreign` (`product_id`),
  ADD KEY `stock_history_product_variant_id_foreign` (`product_variant_id`),
  ADD KEY `stock_history_user_id_foreign` (`user_id`);

--
-- Indexes for table `system_health_metrics`
--
ALTER TABLE `system_health_metrics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `system_health_metrics_metric_type_created_at_index` (`metric_type`,`created_at`);

--
-- Indexes for table `system_modes`
--
ALTER TABLE `system_modes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `system_modes_mode_type_unique` (`mode_type`),
  ADD KEY `system_modes_changed_by_foreign` (`changed_by`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `system_settings_key_unique` (`key`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tags_slug_unique` (`slug`);

--
-- Indexes for table `temp_files`
--
ALTER TABLE `temp_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `temp_files_user_id_foreign` (`user_id`),
  ADD KEY `temp_files_expires_at_processed_index` (`expires_at`,`processed`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vendors_store_slug_unique` (`store_slug`),
  ADD KEY `vendors_user_id_foreign` (`user_id`);

--
-- Indexes for table `vendor_bank_details`
--
ALTER TABLE `vendor_bank_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_bank_details_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `vendor_commissions`
--
ALTER TABLE `vendor_commissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_commissions_order_id_foreign` (`order_id`),
  ADD KEY `vendor_commissions_order_item_id_foreign` (`order_item_id`),
  ADD KEY `vendor_commissions_vendor_id_status_index` (`vendor_id`,`status`);

--
-- Indexes for table `vendor_documents`
--
ALTER TABLE `vendor_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_documents_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `vendor_payouts`
--
ALTER TABLE `vendor_payouts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vendor_payouts_reference_unique` (`reference`),
  ADD KEY `vendor_payouts_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `vendor_reviews`
--
ALTER TABLE `vendor_reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vendor_reviews_user_id_vendor_id_order_id_unique` (`user_id`,`vendor_id`,`order_id`),
  ADD KEY `vendor_reviews_vendor_id_foreign` (`vendor_id`),
  ADD KEY `vendor_reviews_order_id_foreign` (`order_id`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wallets_user_id_unique` (`user_id`);

--
-- Indexes for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wallet_transactions_reference_unique` (`reference`),
  ADD KEY `wallet_transactions_user_id_foreign` (`user_id`),
  ADD KEY `wallet_transactions_order_id_foreign` (`order_id`),
  ADD KEY `wallet_transactions_wallet_id_created_at_index` (`wallet_id`,`created_at`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wishlists_user_id_product_id_unique` (`user_id`,`product_id`),
  ADD KEY `wishlists_product_id_foreign` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ai_actions`
--
ALTER TABLE `ai_actions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_action_limits`
--
ALTER TABLE `ai_action_limits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ai_chat_messages`
--
ALTER TABLE `ai_chat_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_chat_sessions`
--
ALTER TABLE `ai_chat_sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_emergency_status`
--
ALTER TABLE `ai_emergency_status`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ai_liability_logs`
--
ALTER TABLE `ai_liability_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_memory`
--
ALTER TABLE `ai_memory`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_permissions`
--
ALTER TABLE `ai_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `ai_policies`
--
ALTER TABLE `ai_policies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_providers`
--
ALTER TABLE `ai_providers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ai_simulations`
--
ALTER TABLE `ai_simulations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `analytics_daily`
--
ALTER TABLE `analytics_daily`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `analytics_events`
--
ALTER TABLE `analytics_events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `coupon_usages`
--
ALTER TABLE `coupon_usages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery_tracking`
--
ALTER TABLE `delivery_tracking`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `disputes`
--
ALTER TABLE `disputes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_campaigns`
--
ALTER TABLE `email_campaigns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_logs`
--
ALTER TABLE `email_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feature_toggles`
--
ALTER TABLE `feature_toggles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `job_metrics`
--
ALTER TABLE `job_metrics`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order_status_history`
--
ALTER TABLE `order_status_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_gateways`
--
ALTER TABLE `payment_gateways`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `price_history`
--
ALTER TABLE `price_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `review_votes`
--
ALTER TABLE `review_votes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `rollback_records`
--
ALTER TABLE `rollback_records`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `search_index_queue`
--
ALTER TABLE `search_index_queue`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shipping_methods`
--
ALTER TABLE `shipping_methods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `stock_histories`
--
ALTER TABLE `stock_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_history`
--
ALTER TABLE `stock_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_health_metrics`
--
ALTER TABLE `system_health_metrics`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_modes`
--
ALTER TABLE `system_modes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `temp_files`
--
ALTER TABLE `temp_files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vendor_bank_details`
--
ALTER TABLE `vendor_bank_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vendor_commissions`
--
ALTER TABLE `vendor_commissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendor_documents`
--
ALTER TABLE `vendor_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendor_payouts`
--
ALTER TABLE `vendor_payouts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendor_reviews`
--
ALTER TABLE `vendor_reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ai_actions`
--
ALTER TABLE `ai_actions`
  ADD CONSTRAINT `ai_actions_ai_provider_id_foreign` FOREIGN KEY (`ai_provider_id`) REFERENCES `ai_providers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ai_actions_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ai_actions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ai_actions_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ai_action_limits`
--
ALTER TABLE `ai_action_limits`
  ADD CONSTRAINT `ai_action_limits_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ai_chat_messages`
--
ALTER TABLE `ai_chat_messages`
  ADD CONSTRAINT `ai_chat_messages_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `ai_chat_sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ai_chat_sessions`
--
ALTER TABLE `ai_chat_sessions`
  ADD CONSTRAINT `ai_chat_sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ai_chat_sessions_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ai_emergency_status`
--
ALTER TABLE `ai_emergency_status`
  ADD CONSTRAINT `ai_emergency_status_triggered_by_foreign` FOREIGN KEY (`triggered_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ai_liability_logs`
--
ALTER TABLE `ai_liability_logs`
  ADD CONSTRAINT `ai_liability_logs_affected_user_id_foreign` FOREIGN KEY (`affected_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ai_liability_logs_ai_action_id_foreign` FOREIGN KEY (`ai_action_id`) REFERENCES `ai_actions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ai_liability_logs_ai_simulation_id_foreign` FOREIGN KEY (`ai_simulation_id`) REFERENCES `ai_simulations` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ai_memory`
--
ALTER TABLE `ai_memory`
  ADD CONSTRAINT `ai_memory_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ai_policies`
--
ALTER TABLE `ai_policies`
  ADD CONSTRAINT `ai_policies_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ai_simulations`
--
ALTER TABLE `ai_simulations`
  ADD CONSTRAINT `ai_simulations_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ai_simulations_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `analytics_daily`
--
ALTER TABLE `analytics_daily`
  ADD CONSTRAINT `analytics_daily_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `analytics_events`
--
ALTER TABLE `analytics_events`
  ADD CONSTRAINT `analytics_events_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `conversations_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `conversations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversations_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `coupons`
--
ALTER TABLE `coupons`
  ADD CONSTRAINT `coupons_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `coupon_usages`
--
ALTER TABLE `coupon_usages`
  ADD CONSTRAINT `coupon_usages_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `coupon_usages_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `coupon_usages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `delivery_tracking`
--
ALTER TABLE `delivery_tracking`
  ADD CONSTRAINT `delivery_tracking_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `delivery_tracking_order_item_id_foreign` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `disputes`
--
ALTER TABLE `disputes`
  ADD CONSTRAINT `disputes_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `disputes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `email_campaigns`
--
ALTER TABLE `email_campaigns`
  ADD CONSTRAINT `email_campaigns_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `email_logs`
--
ALTER TABLE `email_logs`
  ADD CONSTRAINT `email_logs_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `email_campaigns` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `email_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_address_id_foreign` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_shipping_method_id_foreign` FOREIGN KEY (`shipping_method_id`) REFERENCES `shipping_methods` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `order_items_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD CONSTRAINT `order_status_history_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_status_history_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD CONSTRAINT `payment_transactions_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payment_transactions_payment_gateway_id_foreign` FOREIGN KEY (`payment_gateway_id`) REFERENCES `payment_gateways` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payment_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `price_history`
--
ALTER TABLE `price_history`
  ADD CONSTRAINT `price_history_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_tags`
--
ALTER TABLE `product_tags`
  ADD CONSTRAINT `product_tags_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_tags_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `review_votes`
--
ALTER TABLE `review_votes`
  ADD CONSTRAINT `review_votes_review_id_foreign` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `review_votes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rollback_records`
--
ALTER TABLE `rollback_records`
  ADD CONSTRAINT `rollback_records_initiated_by_foreign` FOREIGN KEY (`initiated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `stock_histories`
--
ALTER TABLE `stock_histories`
  ADD CONSTRAINT `stock_histories_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_histories_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_history`
--
ALTER TABLE `stock_history`
  ADD CONSTRAINT `stock_history_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_history_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_history_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `system_modes`
--
ALTER TABLE `system_modes`
  ADD CONSTRAINT `system_modes_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `temp_files`
--
ALTER TABLE `temp_files`
  ADD CONSTRAINT `temp_files_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `vendors`
--
ALTER TABLE `vendors`
  ADD CONSTRAINT `vendors_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_bank_details`
--
ALTER TABLE `vendor_bank_details`
  ADD CONSTRAINT `vendor_bank_details_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_commissions`
--
ALTER TABLE `vendor_commissions`
  ADD CONSTRAINT `vendor_commissions_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vendor_commissions_order_item_id_foreign` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vendor_commissions_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_documents`
--
ALTER TABLE `vendor_documents`
  ADD CONSTRAINT `vendor_documents_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_payouts`
--
ALTER TABLE `vendor_payouts`
  ADD CONSTRAINT `vendor_payouts_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_reviews`
--
ALTER TABLE `vendor_reviews`
  ADD CONSTRAINT `vendor_reviews_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `vendor_reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vendor_reviews_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wallets`
--
ALTER TABLE `wallets`
  ADD CONSTRAINT `wallets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD CONSTRAINT `wallet_transactions_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `wallet_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wallet_transactions_wallet_id_foreign` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `wishlists_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
