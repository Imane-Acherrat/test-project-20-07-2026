-- Factory Log Sifter - Database Export
-- Compatible with MySQL 5.7+/8.0
-- Generates the same schema produced by `php artisan migrate`

CREATE DATABASE IF NOT EXISTS `factory_log_sifter`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `factory_log_sifter`;

-- --------------------------------------------------------
-- Table: users
-- --------------------------------------------------------
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('engineer','administrator') NOT NULL DEFAULT 'engineer',
  `remember_token` VARCHAR(100) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: sensor_alerts
-- --------------------------------------------------------
CREATE TABLE `sensor_alerts` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `sensor_id` VARCHAR(255) NOT NULL,
  `machine_unit` VARCHAR(255) NOT NULL,
  `error_code` VARCHAR(255) NOT NULL,
  `vibration_amplitude` INT NOT NULL,
  `severity` ENUM('Info','Warning','Critical') NOT NULL,
  `status` ENUM('Open','Resolved','Not Important') NOT NULL DEFAULT 'Open',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: sessions (used by Laravel's session driver)
-- --------------------------------------------------------
CREATE TABLE `sessions` (
  `id` VARCHAR(255) NOT NULL,
  `user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `ip_address` VARCHAR(45) NULL DEFAULT NULL,
  `user_agent` TEXT NULL,
  `payload` LONGTEXT NOT NULL,
  `last_activity` INT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: password_reset_tokens
-- --------------------------------------------------------
CREATE TABLE `password_reset_tokens` (
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Seed data: default users
-- Password for both accounts is: password
-- Hash below is bcrypt('password') - regenerate with `php artisan tinker`
-- if you need a fresh hash: Hash::make('password')
-- --------------------------------------------------------
INSERT INTO `users` (`name`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
('Admin User', 'admin@factory.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrator', NOW(), NOW()),
('Engineer User', 'engineer@factory.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'engineer', NOW(), NOW());
