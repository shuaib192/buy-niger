-- =====================================================
-- BuyNiger - Database Updates for Order/Dispute/Cancel
-- Run this SQL on your live database (phpMyAdmin)
-- =====================================================

-- 1. Create dispute_messages table for conversation threads
CREATE TABLE IF NOT EXISTS `dispute_messages` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `dispute_id` bigint(20) UNSIGNED NOT NULL,
    `user_id` bigint(20) UNSIGNED NOT NULL,
    `message` text NOT NULL,
    `is_admin` tinyint(1) NOT NULL DEFAULT 0,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `dispute_messages_dispute_id_foreign` (`dispute_id`),
    KEY `dispute_messages_user_id_foreign` (`user_id`),
    CONSTRAINT `dispute_messages_dispute_id_foreign` FOREIGN KEY (`dispute_id`) REFERENCES `disputes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `dispute_messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Add 'in_progress' to disputes status enum and add new columns
ALTER TABLE `disputes` 
    MODIFY COLUMN `status` ENUM('open', 'in_progress', 'resolved', 'closed', 'escalated') DEFAULT 'open',
    ADD COLUMN `vendor_id` bigint(20) UNSIGNED NULL AFTER `order_id`,
    ADD COLUMN `resolved_at` timestamp NULL DEFAULT NULL AFTER `resolution_notes`;

-- 3. Add foreign key for vendor_id on disputes (optional, may skip if vendor doesn't exist)
-- ALTER TABLE `disputes` ADD CONSTRAINT `disputes_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE SET NULL;
