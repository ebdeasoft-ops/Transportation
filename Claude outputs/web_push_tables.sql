-- جدول الإشعارات (صف لكل مستخدم)
CREATE TABLE IF NOT EXISTS `app_notifications` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    BIGINT UNSIGNED NOT NULL,
  `type`       VARCHAR(40)  NULL,
  `title`      VARCHAR(255) NOT NULL,
  `body`       TEXT         NULL,
  `url`        VARCHAR(500) NULL,
  `icon`       VARCHAR(50)  NULL,
  `color`      VARCHAR(20)  NULL,
  `ref_key`    VARCHAR(150) NULL,
  `read_at`    TIMESTAMP    NULL,
  `created_at` TIMESTAMP    NULL,
  `updated_at` TIMESTAMP    NULL,
  PRIMARY KEY (`id`),
  KEY `app_notifications_user_id_read_at_index` (`user_id`,`read_at`),
  UNIQUE KEY `app_notifications_user_id_ref_key_unique` (`user_id`,`ref_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- اشتراكات Web Push (صف لكل جهاز/متصفح فعّل الإشعارات)
CREATE TABLE IF NOT EXISTS `push_subscriptions` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    BIGINT UNSIGNED NOT NULL,
  `endpoint`   VARCHAR(500) NOT NULL,
  `p256dh`     VARCHAR(255) NOT NULL,
  `auth`       VARCHAR(255) NOT NULL,
  `user_agent` VARCHAR(255) NULL,
  `created_at` TIMESTAMP    NULL,
  `updated_at` TIMESTAMP    NULL,
  PRIMARY KEY (`id`),
  KEY `push_subscriptions_user_id_index` (`user_id`),
  UNIQUE KEY `push_subscriptions_endpoint_unique` (`endpoint`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
