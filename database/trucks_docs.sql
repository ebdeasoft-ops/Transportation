-- بيانات التأمين والاستمارة للشاحنات (الشاشة بتضيفهم لوحدها، الملف ده احتياطي)
ALTER TABLE `waybill_trucks`
  ADD COLUMN `insurance_company` varchar(150) DEFAULT NULL,
  ADD COLUMN `insurance_policy_no` varchar(100) DEFAULT NULL,
  ADD COLUMN `insurance_start` date DEFAULT NULL,
  ADD COLUMN `insurance_expiry` date DEFAULT NULL,
  ADD COLUMN `insurance_value` double DEFAULT NULL,
  ADD COLUMN `istimara_no` varchar(100) DEFAULT NULL,
  ADD COLUMN `istimara_expiry` date DEFAULT NULL;
