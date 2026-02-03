-- Add created_by column to buildings
ALTER TABLE `buildings` ADD `created_by` BIGINT UNSIGNED NULL;
ALTER TABLE `buildings` ADD CONSTRAINT `buildings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL;

-- Add created_by column to units
ALTER TABLE `units` ADD `created_by` BIGINT UNSIGNED NULL;
ALTER TABLE `units` ADD CONSTRAINT `units_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL;

-- Add created_by column to tenants
ALTER TABLE `tenants` ADD `created_by` BIGINT UNSIGNED NULL;
ALTER TABLE `tenants` ADD CONSTRAINT `tenants_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL;

-- Add created_by column to rents
ALTER TABLE `rents` ADD `created_by` BIGINT UNSIGNED NULL;
ALTER TABLE `rents` ADD CONSTRAINT `rents_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL;

-- Add created_by column to payments
ALTER TABLE `payments` ADD `created_by` BIGINT UNSIGNED NULL;
ALTER TABLE `payments` ADD CONSTRAINT `payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL;

-- Add created_by column to maintenance_requests
ALTER TABLE `maintenance_requests` ADD `created_by` BIGINT UNSIGNED NULL;
ALTER TABLE `maintenance_requests` ADD CONSTRAINT `maintenance_requests_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL;
