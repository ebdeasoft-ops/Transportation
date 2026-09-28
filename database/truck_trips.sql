-- حركة الشاحنات - شغّل الملف ده من phpMyAdmin (بديل عن php artisan migrate)
ALTER TABLE `waybill_trucks`
  ADD COLUMN `current_region` varchar(100) DEFAULT NULL,
  ADD COLUMN `current_city` varchar(150) DEFAULT NULL;

CREATE TABLE IF NOT EXISTS `truck_trips` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `truck_id` bigint unsigned NOT NULL,
  `driver_id` bigint unsigned DEFAULT NULL,
  `driver_name` varchar(255) DEFAULT NULL,
  `from_region` varchar(100) NOT NULL,
  `from_city` varchar(150) DEFAULT NULL,
  `to_region` varchar(100) NOT NULL,
  `to_city` varchar(150) DEFAULT NULL,
  `load_type` varchar(255) DEFAULT NULL,
  `load_weight` varchar(100) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `waybill_no` varchar(50) DEFAULT NULL,
  `loading_at` datetime DEFAULT NULL,
  `expected_unloading_at` datetime DEFAULT NULL,
  `unloaded_at` datetime DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT 1,
  `notes` text,
  `unload_notes` text,
  `user_id` bigint unsigned DEFAULT NULL,
  `unloaded_by` bigint unsigned DEFAULT NULL,
  `branchs_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `truck_trips_truck_id_status_index` (`truck_id`,`status`),
  KEY `truck_trips_loading_at_index` (`loading_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
