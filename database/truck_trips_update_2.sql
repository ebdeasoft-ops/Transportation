-- ملكية الشاحنة + الفاتورة والمرجع والسعر والمرفق للحمولة
-- شغّل الملف ده من phpMyAdmin (بعد truck_trips.sql)
ALTER TABLE `waybill_trucks`
  ADD COLUMN `ownership` varchar(20) DEFAULT NULL;

ALTER TABLE `truck_trips`
  ADD COLUMN `ownership` varchar(20) DEFAULT NULL,
  ADD COLUMN `invoice_number` varchar(100) DEFAULT NULL,
  ADD COLUMN `reference_no` varchar(100) DEFAULT NULL,
  ADD COLUMN `price` double NOT NULL DEFAULT 0,
  ADD COLUMN `attachment` varchar(255) DEFAULT NULL;
