-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 28, 2025 at 05:28 PM
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
-- Database: `db_inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'CPU', '2025-10-27 06:13:20', NULL),
(2, 'HARD DISK', '2025-10-27 06:13:10', NULL),
(3, 'RAM', '2025-10-27 06:13:13', '2025-10-27 06:13:16'),
(4, 'SPEAKER', '2025-10-27 06:15:54', NULL),
(5, 'KEYBOARD', '2025-10-27 06:15:57', NULL),
(6, 'MOUSE', '2025-10-27 06:15:59', NULL),
(7, 'LAPTOP', '2025-10-27 06:16:01', NULL),
(8, 'HEADPHONES', '2025-10-27 06:16:03', NULL),
(9, 'MONITOR', '2025-10-27 06:16:05', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer_info`
--

CREATE TABLE `customer_info` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transaction_ID` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `cp_number` varchar(255) DEFAULT NULL,
  `payment` decimal(10,2) NOT NULL,
  `change_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_product`
--

CREATE TABLE `customer_product` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transaction_ID` varchar(255) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `total_price` decimal(10,2) NOT NULL,
  `sum_total` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_number` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(255) NOT NULL,
  `brand` varchar(255) DEFAULT NULL,
  `supplier` varchar(255) DEFAULT NULL,
  `current_stock` int(11) NOT NULL DEFAULT 0,
  `total_stock` int(11) NOT NULL DEFAULT 0,
  `price` decimal(10,2) NOT NULL,
  `selling_price` decimal(10,2) NOT NULL,
  `barcode` varchar(255) NOT NULL,
  `date_of_arrival` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `product_number`, `name`, `description`, `category`, `brand`, `supplier`, `current_stock`, `total_stock`, `price`, `selling_price`, `barcode`, `date_of_arrival`, `created_at`, `updated_at`) VALUES
(1, '12345', 'AMD RYZEN 5 5600G', 'FREE INSTALLATION OF WINDOWS 11 PRO OS AND MICROSOFT OFFICE 2016 WITH GAMES LIKE ROBLOX, VALORANT ETC..\r\n\r\nALL PARTS ARE BRAND NEW AND WILL BE TESTED THOROUGHLY BEFORE PACKED\r\n\r\nFREE INSTALLATION OF GAMES AND APPS WITH 1 YEAR WARRANTY\r\n\r\nWE AT ZAPMAX WANT TO DELIVER QUALITY PRODUCTS AT YOUR DOORSTEPS!\r\n\r\n\r\n\r\nBUILD PACKAGE 1 -  JUNGLE WHITE \r\n\r\nProcessor: Ryzen 5 5600G\r\n\r\nMotherboard: A520M ASUS/MSI/BIOSTAR \r\n\r\nGPU: iGPU:Radeon™ GRAPHICS 7 \r\n\r\nRAM: 8GB 2666MHz DDR4 /16GB 2666MHZ DDR4\r\n\r\nSSD:120GB SSD \r\n\r\nHDD:500GB 7200rpm / 1TB HDD\r\n\r\nCasing: KEYTECH ROBIN LITE \"ROG\" WHITE \r\n\r\nPower Supply: KEYTECH THUNDERBOLT 800 WATTS \r\n\r\nCooler Fan: 3X INPLAY ICE TOWER FANS WITH REMOTE\r\n\r\nUNIT ONLY OR WITH MONITOR?\r\n\r\nKEYBOARD, MOUSE AND SPEAKER INCLUDED IN MONITOR PACKAGES', 'CPU', 'AMD', 'RYZEN', 50, 50, 1499.00, 1699.00, '12345', '2025-10-28', '2025-10-27 08:07:55', '2025-10-28 01:34:22'),
(3, '23254', 'LANGTU 3Mode Mechanical keybaord RGB backlit Hotswap Knob DIY Screen Wireless Gaming Keyboard LT104', 'LANGTU 3Mode Mechanical keybaord RGB backlit Hotswap Knob DIY Screen Wireless Gaming Keyboard LT104\r\n\r\nParameter Description\r\n\r\nProduct name: LT104 mechanical keyboard\r\n\r\nNumber of keys: 104 Keys\r\n\r\nSwitch body: Switch body: Factory Lubed Linear AIR-SEA Switch ( 5PIN )\r\n\r\n\r\n\r\nScreen function : *********** CUSTOM GIF ANIMATION\r\n\r\n*********** Change the connection\r\n\r\n*********** System change\r\n\r\n*********** Backlit control\r\n\r\n*********** Language selection\r\n\r\n*********** Time display\r\n\r\n\r\n\r\nHot-swappable：FULL KEYS HOTSWAP\r\n\r\nRGB backlit : DIY SUPPORT\r\n\r\nType: TYPE-C\r\n\r\nConnection Mode: Wired Wireless , Bluetooth ( 3 mode to connect\r\n\r\nNo rush mode: no rush for all keys\r\n\r\nMacro Definition: Macro Programmable\r\n\r\nAccessories: Equipped with 163CM data cable\r\n\r\nProduct size: 445*138*40mm\r\n\r\nSystem Support: Window/Mac', 'KEYBOARD', 'Langtu', 'Langtu', 50, 50, 1500.00, 1728.00, '23254', '2025-10-28', '2025-10-28 01:41:48', '2025-10-28 01:44:04');

-- --------------------------------------------------------

--
-- Table structure for table `remarks`
--

CREATE TABLE `remarks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `returns`
--

CREATE TABLE `returns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transaction_ID` varchar(255) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_info`
--
ALTER TABLE `customer_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_info_transaction_id_unique` (`transaction_ID`);

--
-- Indexes for table `customer_product`
--
ALTER TABLE `customer_product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_product_transaction_id_foreign` (`transaction_ID`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `remarks`
--
ALTER TABLE `remarks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `returns`
--
ALTER TABLE `returns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `returns_transaction_id_foreign` (`transaction_ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `customer_info`
--
ALTER TABLE `customer_info`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_product`
--
ALTER TABLE `customer_product`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `remarks`
--
ALTER TABLE `remarks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `returns`
--
ALTER TABLE `returns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customer_product`
--
ALTER TABLE `customer_product`
  ADD CONSTRAINT `customer_product_transaction_id_foreign` FOREIGN KEY (`transaction_ID`) REFERENCES `customer_info` (`transaction_ID`) ON DELETE CASCADE;

--
-- Constraints for table `returns`
--
ALTER TABLE `returns`
  ADD CONSTRAINT `returns_transaction_id_foreign` FOREIGN KEY (`transaction_ID`) REFERENCES `customer_info` (`transaction_ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
