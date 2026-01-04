SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE IF NOT EXISTS `vehicles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `package_type` varchar(80) NOT NULL,
  `fare` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_kg` int(11) NOT NULL DEFAULT 0,
  `size_length_m` decimal(6,2) NOT NULL DEFAULT 0.00,
  `size_width_m` decimal(6,2) NOT NULL DEFAULT 0.00,
  `size_height_m` decimal(6,2) NOT NULL DEFAULT 0.00,
  `image_file` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_vehicle_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `vehicles`
(`name`, `package_type`, `fare`, `max_kg`, `size_length_m`, `size_width_m`, `size_height_m`, `image_file`)
VALUES
('Motorcycle', 'Envelope', 100.00, 20, 0.50, 0.40, 0.50, 'motorcycle.png'),
('Tricycle', 'Small Box', 150.00, 50, 0.70, 0.50, 0.50, 'tricycle.png'),
('Sedan', 'Med Box', 200.00, 200, 1.00, 0.60, 0.70, 'sedan.png'),
('Pick-up Truck', 'Big Box', 250.00, 800, 2.70, 1.50, 0.50, 'pickup.png'),
('Closed Van', 'Pallet (Perishable)', 300.00, 1000, 2.10, 1.30, 1.30, 'van.png'),
('Forward Truck', 'Pallet (Non Perishable)', 350.00, 1200, 10.00, 2.40, 2.30, 'forward truck.png');


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;