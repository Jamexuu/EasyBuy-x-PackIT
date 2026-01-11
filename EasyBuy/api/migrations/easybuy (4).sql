-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 11, 2026 at 05:44 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `easybuy`
--
CREATE DATABASE IF NOT EXISTS `easybuy` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `easybuy`;

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

DROP TABLE IF EXISTS `addresses`;
CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `house_number` varchar(20) DEFAULT NULL,
  `street` varchar(20) DEFAULT NULL,
  `lot` varchar(20) DEFAULT NULL,
  `block` varchar(20) DEFAULT NULL,
  `barangay` varchar(50) DEFAULT NULL,
  `city` varchar(50) NOT NULL,
  `province` varchar(50) NOT NULL,
  `postal_code` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `house_number`, `street`, `lot`, `block`, `barangay`, `city`, `province`, `postal_code`) VALUES
(5, 9, '1', '1', '1', '1', 'Tinurik', 'Tanauan City', 'Batangas', '4232');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `created_at`) VALUES
(3, 9, '2026-01-08 15:01:56');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('order placed','waiting for courier','in transit','order arrived','cancelled') NOT NULL DEFAULT 'order placed',
  `total_amount` decimal(10,2) NOT NULL,
  `total_weight_grams` decimal(10,2) NOT NULL,
  `payment_method` enum('cod','paypal') NOT NULL DEFAULT 'cod',
  `shipping_fee` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','completed','failed','cancelled') NOT NULL DEFAULT 'pending',
  `transaction_id` varchar(255) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `status`, `total_amount`, `total_weight_grams`, `payment_method`, `shipping_fee`, `payment_status`, `transaction_id`, `order_date`) VALUES
(7, 9, 'order placed', 160.00, 2250.00, 'cod', 50.00, 'pending', NULL, '2026-01-02 17:36:59'),
(11, 9, 'order placed', 411.60, 1800.00, 'cod', 50.00, 'pending', NULL, '2026-01-08 14:57:03'),
(12, 9, 'order placed', 95.20, 225.00, 'cod', 50.00, 'pending', NULL, '2026-01-08 15:02:07'),
(13, 9, 'order placed', 85.84, 375.00, 'cod', 50.00, 'pending', NULL, '2026-01-08 15:02:53'),
(14, 9, 'order placed', 223.52, 2475.00, 'cod', 50.00, 'pending', NULL, '2026-01-10 12:34:07'),
(15, 9, 'order placed', 230.80, 900.00, 'paypal', 50.00, 'completed', '294138600H082174K', '2026-01-10 18:50:17'),
(16, 9, 'order placed', 95.20, 225.00, 'paypal', 50.00, 'completed', '1E8093497X478615F', '2026-01-10 19:14:37'),
(17, 9, 'order placed', 95.20, 225.00, 'cod', 50.00, 'pending', NULL, '2026-01-10 19:18:18'),
(18, 9, 'order placed', 95.20, 225.00, 'cod', 50.00, 'pending', NULL, '2026-01-10 19:18:59'),
(19, 9, 'order placed', 95.20, 225.00, 'paypal', 50.00, 'completed', '7W9981934N761241R', '2026-01-10 19:19:38'),
(20, 9, 'order placed', 383.66, 2700.00, 'cod', 50.00, 'pending', NULL, '2026-01-11 04:13:21'),
(21, 9, '', 176.24, 825.00, 'cod', 50.00, '', NULL, '2026-01-11 04:20:59'),
(22, 9, '', 131.04, 600.00, 'cod', 50.00, 'cancelled', NULL, '2026-01-11 04:41:57'),
(23, 9, 'cancelled', 131.04, 600.00, 'cod', 50.00, 'cancelled', NULL, '2026-01-11 04:43:03');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `product_price`, `quantity`, `created_at`) VALUES
(1, 7, 3, 'Squash', 22.00, 5, '2026-01-02 17:36:59'),
(5, 11, 1, 'Chinese Pechay', 56.50, 8, '2026-01-08 14:57:03'),
(6, 12, 1, 'Chinese Pechay', 56.50, 1, '2026-01-08 15:02:07'),
(7, 13, 2, 'White Onion', 44.80, 1, '2026-01-08 15:02:54'),
(8, 14, 2, 'White Onion', 44.80, 3, '2026-01-10 12:34:07'),
(9, 14, 3, 'Squash', 22.00, 3, '2026-01-10 12:34:07'),
(10, 15, 1, 'Chinese Pechai', 56.50, 4, '2026-01-10 18:50:17'),
(11, 16, 1, 'Chinese Pechai', 56.50, 1, '2026-01-10 19:14:37'),
(12, 17, 1, 'Chinese Pechai', 56.50, 1, '2026-01-10 19:18:18'),
(13, 18, 1, 'Chinese Pechai', 56.50, 1, '2026-01-10 19:18:59'),
(14, 19, 1, 'Chinese Pechai', 56.50, 1, '2026-01-10 19:19:38'),
(15, 20, 11, 'Ground Beef', 100.50, 3, '2026-01-11 04:13:21'),
(16, 20, 2, 'White Onion', 44.80, 3, '2026-01-11 04:13:21');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stocks` int(11) NOT NULL DEFAULT 0,
  `is_sale` tinyint(1) NOT NULL DEFAULT 0,
  `sale_percentage` int(11) DEFAULT NULL,
  `weight_grams` int(11) NOT NULL,
  `size` varchar(255) DEFAULT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_name`, `category`, `price`, `stocks`, `is_sale`, `sale_percentage`, `weight_grams`, `size`, `image`) VALUES
(1, 'Chinese Pechai', 'produce', 56.50, 46, 1, 20, 225, '200g-250g', '../Product Images/all/1.webp'),
(2, 'White Onion', 'Produce', 44.80, 47, 1, 20, 375, '350g-400g', '../Product Images/all/2.webp'),
(3, 'Squash', 'Produce', 22.00, 50, 0, NULL, 450, '400g-500g', '../Product Images/all/3.webp'),
(4, 'Tomato Native', 'Produce', 56.75, 50, 0, NULL, 375, '350g-400g', '../Product Images/all/4.webp'),
(5, 'Broccoli', 'Produce', 126.08, 50, 0, NULL, 450, '400g-500g', '../Product Images/all/5.webp'),
(6, 'Gala Apples', 'Produce', 230.00, 30, 0, NULL, 1500, '10pcs', '../Product Images/all/6.webp'),
(7, 'Banana Saba', 'Produce', 105.00, 50, 0, NULL, 1250, '1000g-1500g', '../Product Images/all/7.webp'),
(8, 'Watermelon', 'Produce', 186.00, 30, 0, NULL, 2000, '1000g-3000g', '../Product Images/all/8.webp'),
(9, 'Mango Green Medium', 'Produce', 220.00, 40, 0, NULL, 950, '900g-1000g', '../Product Images/all/9.webp'),
(10, 'Pomelo Premium', 'Produce', 347.00, 30, 0, NULL, 925, '850g-1000g', '../Product Images/all/10.webp'),
(11, 'Ground Beef', 'Meat and Seafood', 100.50, 37, 1, 25, 525, '500g-550g', '../Product Images/all/11.webp'),
(12, 'Osso Buco', 'Meat and Seafood', 218.35, 30, 1, 25, 525, '500g-550g', '../Product Images/all/12.webp'),
(13, 'Pork Steak', 'Meat and Seafood', 156.00, 40, 0, NULL, 450, '400g-500g', '../Product Images/all/13.webp'),
(14, 'Lechon Belly', 'Meat and Seafood', 725.75, 20, 0, NULL, 2150, '1800g-2500g', '../Product Images/all/14.webp'),
(15, 'Chicken Recado', 'Meat and Seafood', 118.25, 40, 0, NULL, 500, '500g', '../Product Images/all/15.webp'),
(16, 'Chicken Adobo Cut', 'Meat and Seafood', 130.90, 40, 0, NULL, 525, '500g-550g', '../Product Images/all/16.webp'),
(17, 'Yellow Fin Tuna Steak Cut', 'Meat and Seafood', 450.00, 25, 0, NULL, 900, '800g-1000g', '../Product Images/all/17.webp'),
(18, 'Pasayan Putian Large', 'Meat and Seafood', 249.50, 30, 0, NULL, 490, '480g-500g', '../Product Images/all/18.webp'),
(19, 'Salmon Belly', 'Meat and Seafood', 208.50, 30, 0, NULL, 500, '450g-550g', '../Product Images/all/19.webp'),
(20, 'Bangus Boneless', 'Meat and Seafood', 190.00, 35, 0, NULL, 425, '350g-500g', '../Product Images/all/20.webp'),
(21, 'Selecta Milk', 'Dairy', 206.00, 50, 1, 20, 2060, '1L 2pcs', '../Product Images/all/21.webp'),
(22, 'Pizza Topping', 'Dairy', 263.00, 40, 1, 20, 175, '175g', '../Product Images/all/22.webp'),
(23, 'Cheddar Burger Slices', 'Dairy', 189.00, 50, 0, NULL, 280, '20pcs', '../Product Images/all/23.webp'),
(24, 'Salted Butter', 'Dairy', 186.00, 50, 0, NULL, 200, '200g', '../Product Images/all/24.webp'),
(25, 'Havarti Cheese', 'Dairy', 240.00, 40, 0, NULL, 200, '200g', '../Product Images/all/25.webp'),
(26, 'Cherry Yogurt', 'Dairy', 140.00, 60, 0, NULL, 100, '100g', '../Product Images/all/26.webp'),
(27, 'Cheesy Spread', 'Dairy', 240.00, 50, 0, NULL, 500, '500g', '../Product Images/all/27.webp'),
(28, 'Creamy Butter', 'Dairy', 182.00, 50, 0, NULL, 200, '200g', '../Product Images/all/28.webp'),
(29, 'Alaska Evaporated Milk', 'Dairy', 61.00, 60, 0, NULL, 515, '500ml', '../Product Images/all/29.webp'),
(30, 'Dairy Magic', 'Dairy', 132.00, 50, 0, NULL, 225, '225g', '../Product Images/all/30.webp'),
(31, 'Purefoods Chicken Katsu', 'Frozen Goods', 220.00, 40, 1, 20, 500, '500g', '../Product Images/all/31.webp'),
(32, 'CDO Fantastik Young Pork Tocino', 'Frozen Goods', 89.00, 50, 1, 20, 225, '225g', '../Product Images/all/32.webp'),
(33, 'Veega Meat Adobo Flakes', 'Frozen Goods', 125.00, 40, 0, NULL, 160, '160g', '../Product Images/all/33.webp'),
(34, 'Purefoods TJ Hotdog Cheese', 'Frozen Goods', 150.00, 50, 0, NULL, 500, '500g', '../Product Images/all/34.webp'),
(35, 'Highlands Angus Beef Franks', 'Frozen Goods', 225.00, 40, 0, NULL, 500, '500g', '../Product Images/all/35.webp'),
(36, 'CDO Ulam Burger Patties', 'Frozen Goods', 70.50, 50, 0, NULL, 225, '225g', '../Product Images/all/36.webp'),
(37, 'Purefoods Crazy Cut Nuggets', 'Frozen Goods', 108.00, 50, 0, NULL, 200, '200g', '../Product Images/all/37.webp'),
(38, 'Purefoods Honeycured Bacon Sliced', 'Frozen Goods', 700.00, 30, 0, NULL, 500, '', '../Product Images/all/38.webp'),
(39, 'Sae Min Mushroom Enoky', 'Frozen Goods', 55.00, 50, 0, NULL, 200, '200g', '../Product Images/all/39.webp'),
(40, 'Chopseuy Mix', 'Frozen Goods', 46.00, 50, 0, NULL, 450, '400g-500g', '../Product Images/all/40.webp'),
(41, 'UFC Banana Catsup', 'Condiments and Sauces', 67.00, 60, 1, 15, 1000, '1kg', '../Product Images/all/41.webp'),
(42, 'Maggi Magic Sarap Seasoning', 'Condiments and Sauces', 77.50, 70, 1, 15, 128, '8g 16pcs', '../Product Images/all/42.webp'),
(43, 'Silver Swan Soy Sauce', 'Condiments and Sauces', 56.00, 60, 0, NULL, 1050, '1L', '../Product Images/all/43.webp'),
(44, 'Knorr Sinigang sa Sampalok Mix', 'Condiments and Sauces', 28.50, 80, 0, NULL, 44, '44g', '../Product Images/all/44.webp'),
(45, 'Mama Sita\'s Oyster Sauce', 'Condiments and Sauces', 80.50, 60, 0, NULL, 405, '405g', '../Product Images/all/45.webp'),
(46, 'Mang Tomas Lechon Sauce - Regular', 'Condiments and Sauces', 50.00, 60, 0, NULL, 550, '550g', '../Product Images/all/46.webp'),
(47, 'Del Monte Tomato Ketchup - Original', 'Condiments and Sauces', 32.50, 70, 0, NULL, 320, '320g', '../Product Images/all/47.webp'),
(48, 'Coco Mama Fresh Gata', 'Condiments and Sauces', 68.50, 50, 0, NULL, 410, '400ml', '../Product Images/all/48.webp'),
(49, 'Del Monte Spaghetti Sauce Italian', 'Condiments and Sauces', 99.50, 50, 0, NULL, 1000, '1kg', '../Product Images/all/49.webp'),
(50, 'Datu Puti Vinegar Pouch', 'Condiments and Sauces', 42.50, 70, 0, NULL, 1020, '1L', '../Product Images/all/50.webp'),
(51, 'SkyFlakes Crakers', 'Snacks', 65.00, 80, 1, 15, 100, '10pcs', '../Product Images/all/51.webp'),
(52, 'Oreo Strawberry', 'Snacks', 85.00, 70, 1, 15, 90, '9pcs', '../Product Images/all/52.webp'),
(53, 'Cheese Ring', 'Snacks', 19.00, 100, 0, NULL, 60, '60g', '../Product Images/all/53.webp'),
(54, 'Pillows Chocolate', 'Snacks', 11.00, 100, 0, NULL, 38, '38g', '../Product Images/all/54.webp'),
(55, 'Butter Coconut', 'Snacks', 55.00, 80, 0, NULL, 14, '14g', '../Product Images/all/55.webp'),
(56, 'Breadstick', 'Snacks', 70.00, 80, 0, NULL, 100, '10pcs', '../Product Images/all/56.webp'),
(57, 'Breadfan', 'Snacks', 8.00, 120, 0, NULL, 24, '24g', '../Product Images/all/57.webp'),
(58, 'Hello', 'Snacks', 60.00, 80, 0, NULL, 100, '10pcs', '../Product Images/all/58.webp'),
(59, 'Breadsticks', 'Snacks', 69.00, 80, 0, NULL, 100, '10pcs', '../Product Images/all/59.webp'),
(60, 'Nova', 'Snacks', 20.00, 100, 0, NULL, 25, '25g', '../Product Images/all/60.webp'),
(61, 'Coca Cola Coke Zero', 'Beverages', 68.50, 60, 1, 15, 1575, '1.5L', '../Product Images/all/61.webp'),
(62, 'Rite N Lite Strawberry + Kiwi', 'Beverages', 29.50, 80, 1, 15, 260, '250ml', '../Product Images/all/62.webp'),
(63, 'San Mig Coffee 3-in-1 Sugar Free Original', 'Beverages', 36.75, 80, 0, NULL, 90, '90g', '../Product Images/all/63.webp'),
(64, 'Mogu Mogu Strawberry Juice', 'Beverages', 45.00, 70, 0, NULL, 335, '320ml', '../Product Images/all/64.webp'),
(65, 'Alfonso 1 Light Brand', 'Beverages', 275.00, 40, 0, NULL, 735, '700ml', '../Product Images/all/65.webp'),
(66, 'Red Horse Can', 'Beverages', 323.50, 50, 0, NULL, 2100, '6pcs', '../Product Images/all/66.webp'),
(67, 'Coca-Cola Coke', 'Beverages', 65.95, 60, 0, NULL, 1575, '1.5L PET', '../Product Images/all/67.webp'),
(68, 'Chuckie Chocolate Milk Drink', 'Beverages', 31.50, 80, 0, NULL, 260, '250ml', '../Product Images/all/68.webp'),
(69, 'Dutch Mill Strawberry', 'Beverages', 81.50, 60, 0, NULL, 760, '180ml 4pcs', '../Product Images/all/69.webp'),
(70, 'Dutch Mill Delight', 'Beverages', 46.50, 70, 0, NULL, 420, '400mL', '../Product Images/all/70.webp'),
(71, 'Palmolive Shampoo Silky Straight', 'Personal', 40.00, 80, 1, 15, 90, '15ml 6pcs', '../Product Images/all/71.webp'),
(72, 'Dove Shampoo Keratin Straight & Silky', 'Personal', 41.00, 80, 1, 15, 81, '13.5ml 6pcs', '../Product Images/all/72.webp'),
(73, 'Bioderm Soap Coolness', 'Personal', 36.75, 90, 0, NULL, 90, '90g', '../Product Images/all/73.webp'),
(74, 'Kojiesan Soap Skin Lightening Kojic Acid', 'Personal', 99.75, 70, 0, NULL, 195, '65g 3pcs', '../Product Images/all/74.webp'),
(75, 'Modess Sanitary Napkin with Wings Cottony', 'Personal', 155.00, 60, 0, NULL, 400, '29+3pcs', '../Product Images/all/75.webp'),
(76, 'Sisters Napkin Cotton Day With Wings', 'Personal', 22.00, 90, 0, NULL, 80, '8pcs', '../Product Images/all/76.webp'),
(77, 'Femme Bathroom Tissue 2 ply', 'Personal', 109.75, 70, 0, NULL, 1200, '150pulls 12pcs', '../Product Images/all/77.webp'),
(78, 'Ponds Facial Cream WB Pink Lightening', 'Personal', 176.75, 60, 0, NULL, 40, '40g', '../Product Images/all/78.webp'),
(79, 'Belo Sunscreen Sun Expert Tinted SPF50', 'Personal', 499.75, 40, 0, NULL, 50, '50ml', '../Product Images/all/79.webp'),
(80, 'Ph Care Feminine Wash Floral Clean', 'Personal', 177.75, 60, 0, NULL, 260, '250ml', '../Product Images/all/80.webp'),
(84, 'sprite', 'beverages', 80.00, 50, 1, 20, 500, '1500', '../Product Images/all/product_6962851d56f951.43086369.png');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `contact_number`, `role`, `created_at`) VALUES
(9, 'test', 'test', 'test@gmail.com', '098f6bcd4621d373cade4e832627b4f6', '0987654321', 'user', '2025-12-25 23:52:21'),
(10, 'james', 'mercado', 'admin@gmail.com', '2f9a957fe3cfe9735c7e9b51a23b7e18', '0987654321', 'admin', '2025-12-25 23:54:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_user_address` (`user_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_user_cart` (`user_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_cart_cart_items` (`cart_id`),
  ADD KEY `FK_product_cart_items` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_user_order` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_order_order_item` (`order_id`),
  ADD KEY `FK_product_order_item` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `FK_user_address` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `FK_user_cart` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `FK_cart_cart_items` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_product_cart_items` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `FK_user_order` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `FK_order_order_item` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_product_order_item` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
