-- ربط الحمولة بالعميل المسجل في العملاء - شغّل الملف ده من phpMyAdmin
ALTER TABLE `truck_trips`
  ADD COLUMN `customer_account_id` bigint unsigned DEFAULT NULL;
