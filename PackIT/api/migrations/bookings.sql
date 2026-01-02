SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `pickup_house` varchar(100) DEFAULT NULL,
  `pickup_barangay` varchar(100) DEFAULT NULL,
  `pickup_municipality` varchar(100) NOT NULL,
  `pickup_province` varchar(100) NOT NULL,
  `pickup_lat` decimal(10, 8) DEFAULT NULL,
  `pickup_lng` decimal(11, 8) DEFAULT NULL,
  `drop_house` varchar(100) DEFAULT NULL,
  `drop_barangay` varchar(100) DEFAULT NULL,
  `drop_municipality` varchar(100) NOT NULL,
  `drop_province` varchar(100) NOT NULL,
  `drop_lat` decimal(10, 8) DEFAULT NULL,
  `drop_lng` decimal(11, 8) DEFAULT NULL,
  `vehicle_type` enum('car','motorcycle','van','forward_truck') NOT NULL,
  `distance_km` decimal(10, 2) DEFAULT NULL,
  `base_amount` decimal(10, 2) NOT NULL DEFAULT 0,
  `distance_amount` decimal(10, 2) NOT NULL DEFAULT 0,
  `door_to_door_amount` decimal(10, 2) NOT NULL DEFAULT 0,
  `total_amount` decimal(10, 2) NOT NULL,
  `payment_status` enum('pending','paid','failed') NOT NULL DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `tracking_status` enum('pending','accepted','picked_up','in_transit','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `FK_user_booking` (`user_id`),
  KEY `FK_driver_booking` (`driver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


ALTER TABLE `bookings`
  ADD CONSTRAINT `FK_user_booking` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_driver_booking` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;