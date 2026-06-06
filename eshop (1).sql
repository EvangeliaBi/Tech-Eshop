-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: May 24, 2026 at 03:51 PM
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
-- Database: `eshop`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Laptops'),
(2, 'Desktops'),
(3, 'Οθόνες'),
(4, 'Πληκτρολόγια'),
(5, 'SSD - HDD');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `username`, `password`, `email`) VALUES
(1, 'Evangelia', '$2y$10$PAserTQS3wNCkaEYs.bigeU0lFXJM6BjRSDUabQXZ2alnZv2n.7wu', 'euaggeliampimpase@gmail.com'),
(2, 'Eva', '$2y$10$hASkiDOcHrSHc9KsmxTJveNnIIlq3FnEtfHRvFfcHhFp2kNZgsTE2', 'eua@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `category_id`) VALUES
(1, 'Asus Vivobook', 'X1605VA-OLED-SH2704W Laptop 16\" OLED (Core 9 270H/24 GB/1 TB/UHD Graphics/Windows 11 Home)', 999.00, 'laptop1.png', 1),
(2, 'Lenovo IdeaPad Slim 3', 'Laptop 15.1\" OLED (Ryzen 7 8840HS/24 GB/1 TB/780M/Windows 11 S)', 999.00, 'laptop2.png', 1),
(3, 'MSI Katana 15', 'HX B14WGK Laptop 15.6\" IPS (Core i9 14900HX/32 GB/1 TB/RTX 5070 8 GB/Windows 11 Home', 1949.00, 'laptop3.png', 1),
(4, 'Apple MacBook Air M4', 'Midnight Laptop 15.3\" Liquid Retina ( M4/16 GB/256 GB/Apple 10 Core GPU/macOS)', 1149.00, 'laptop4.png', 1),
(5, 'Lenovo Legion 9', 'Laptop 18\" IPS (Core Ultra 9 275HX/64 GB/4 TB/RTX 5090 24 GB/Windows 11 Home)', 6499.00, 'laptop5.png', 1),
(6, 'Turbo-X Nemesis', 'N8700 AI Desktop (AMD Ryzen 7 8700G/32 GB/1TB/9070XT 16 GB)', 2499.00, 'desk1.png', 2),
(7, 'Dell SFF Optiplex', '7060 Refurbished Desktop (Intel Core i7 8th Gen/16 GB/512GB/HD Graphics)', 499.00, 'desk2.png', 2),
(8, 'Turbo-X Sphere', 'SK5700 Desktop (AMD Ryzen 7 5700G/16 GB/500GB/Radeon Graphics)', 999.00, 'desk3.png', 2),
(9, 'Blackview Mini', 'PC Ryzen 5 7430U/16/512G', 569.00, 'desk4.png', 2),
(10, 'Turbo-X Spectra', 'S1470T Desktop (Intel Intel Core i7 14700K/32 GB/1TB/RTX 5070 Ti 16 GB)', 2999.00, 'desk5.png', 2),
(11, 'Samsung Monitor', '40\" LS40FG750EUXEN', 898.00, 'screen1.png', 3),
(12, 'Samsung Monitor', '32\" Smart M8 M80F', 489.00, 'screen2.png', 3),
(13, 'Turbo-X Monitor', '34\" Nemesis 3403WQX', 199.00, 'screen3.png', 3),
(14, 'Xiaomi Monitor', '27\" G Pro 27i', 299.00, 'screen4.png', 3),
(15, 'Samsung Monitor', '34\" ViewFinity S5 S50GC', 229.00, 'screen5.png', 3),
(16, 'Turbo-X', 'Πληκτρολόγιο-ποντίκι DWC 8000 Ενσύρματο', 13.99, 'key1.png', 4),
(17, 'Logitech', 'Πληκτρολόγιο - Ποντίκι MK 270 Ασύρματο', 32.99, 'key2.png', 4),
(18, 'Turbo-X', 'Erebus Gaming Ενσύρματο Πληκτρολόγιο EK20 60% Mini', 29.99, 'key3.png', 4),
(19, 'Logitech', 'Gaming Keyboard G Pro X TKL Black Lightspeed US Tactile', 234.99, 'key4.png', 4),
(20, 'Turbo-X', 'Πληκτρολόγιο Origin OK10 Ενσύρματο', 24.99, 'key5.png', 4),
(21, 'Patriot SSD', 'P300 NVMe M.2 512GB', 119.99, 'sd1.png', 5),
(22, 'Samsung SSD', '990 EVO Plus NVMe M.2 2TB', 299.99, 'sd2.png', 5),
(23, 'WD HDD', 'Red Plus NAS 4TB 3.5\"', 259.99, 'sd3.png', 5),
(24, 'Seagate HDD', 'BarraCuda 2TB 3.5\"', 159.99, 'sd4.png', 5),
(25, 'Corsair SSD', 'MP600 PRO NH 2TB NVMe M.2 PCIe Gen4 x4', 289.99, 'sd5.png', 5);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
