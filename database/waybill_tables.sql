-- بوليصة الشحن - شغّل هذا الملف من phpMyAdmin على قاعدة البيانات (بديل عن php artisan migrate)
CREATE TABLE IF NOT EXISTS `waybill_drivers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `id_number` varchar(50) DEFAULT NULL,
  `license_number` varchar(100) DEFAULT NULL,
  `license_issue_date` varchar(50) DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `notes` text,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `waybill_trucks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `plate_number` varchar(100) NOT NULL,
  `plate_region` varchar(150) DEFAULT NULL,
  `owner_name` varchar(255) DEFAULT NULL,
  `operation_license_number` varchar(100) DEFAULT NULL,
  `operation_license_issuer` varchar(150) DEFAULT NULL,
  `truck_type` varchar(150) DEFAULT NULL,
  `total_load` varchar(100) DEFAULT NULL,
  `default_driver_id` bigint unsigned DEFAULT NULL,
  `notes` text,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `waybill_customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `city` varchar(150) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `tax_no` varchar(50) DEFAULT NULL,
  `notes` text,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `waybills` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `waybill_no` varchar(50) NOT NULL,
  `date` date DEFAULT NULL,
  `date_hijri` varchar(50) DEFAULT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `destination_city` varchar(255) DEFAULT NULL,
  `driver_id` bigint unsigned DEFAULT NULL,
  `driver_name` varchar(255) DEFAULT NULL,
  `driver_license_number` varchar(100) DEFAULT NULL,
  `driver_license_issue_date` varchar(50) DEFAULT NULL,
  `truck_id` bigint unsigned DEFAULT NULL,
  `owner_name` varchar(255) DEFAULT NULL,
  `plate_number` varchar(100) DEFAULT NULL,
  `plate_region` varchar(150) DEFAULT NULL,
  `operation_license_number` varchar(100) DEFAULT NULL,
  `operation_license_issuer` varchar(150) DEFAULT NULL,
  `truck_type` varchar(150) DEFAULT NULL,
  `total_load` varchar(100) DEFAULT NULL,
  `departure_date` date DEFAULT NULL,
  `fare_paid_by` varchar(255) DEFAULT NULL,
  `delivery_within` varchar(255) DEFAULT NULL,
  `notes` text,
  `total_fare` double NOT NULL DEFAULT 0,
  `branchs_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `waybills_waybill_no_unique` (`waybill_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `waybill_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `waybill_id` bigint unsigned NOT NULL,
  `sender_name` varchar(255) DEFAULT NULL,
  `fare` double NOT NULL DEFAULT 0,
  `receiver_name` varchar(255) DEFAULT NULL,
  `goods_type` varchar(255) DEFAULT NULL,
  `goods_weight` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `waybill_items_waybill_id_index` (`waybill_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
