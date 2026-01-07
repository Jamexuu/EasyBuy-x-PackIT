ALTER TABLE `drivers`
  ADD COLUMN `active_vehicle_id` INT(11) NULL AFTER `vehicle_type`,
  ADD KEY `FK_drivers_active_vehicle` (`active_vehicle_id`);

CREATE TABLE IF NOT EXISTS `driver_vehicles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `driver_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `license_plate` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `FK_dv_driver` (`driver_id`),
  KEY `FK_dv_vehicle` (`vehicle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `driver_vehicles`
  ADD CONSTRAINT `FK_dv_driver` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_dv_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `drivers`
  ADD CONSTRAINT `FK_drivers_active_vehicle` FOREIGN KEY (`active_vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;