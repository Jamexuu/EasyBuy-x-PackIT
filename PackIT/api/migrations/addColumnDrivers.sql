ALTER TABLE `drivers`
  ADD COLUMN `first_name` VARCHAR(100) NOT NULL AFTER `user_id`,
  ADD COLUMN `last_name` VARCHAR(100) NOT NULL AFTER `first_name`,
  ADD COLUMN `email` VARCHAR(150) NOT NULL AFTER `last_name`,
  ADD COLUMN `password` VARCHAR(255) NOT NULL AFTER `email`,
  ADD COLUMN `contact_number` VARCHAR(30) NOT NULL AFTER `password`,
  ADD COLUMN `house_number` VARCHAR(50) NOT NULL AFTER `contact_number`,
  ADD COLUMN `street` VARCHAR(150) NOT NULL AFTER `house_number`,
  ADD COLUMN `province` VARCHAR(100) NOT NULL AFTER `street`,
  ADD COLUMN `city` VARCHAR(100) NOT NULL AFTER `province`,
  ADD COLUMN `barangay` VARCHAR(100) NOT NULL AFTER `city`;

-- Prevent duplicate driver emails
ALTER TABLE `drivers`
  ADD UNIQUE KEY `uniq_drivers_email` (`email`);