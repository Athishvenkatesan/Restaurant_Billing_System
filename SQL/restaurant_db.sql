-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 31, 2025 at 07:47 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `restaurant_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

CREATE TABLE `bills` (
  `bill_id` int(11) NOT NULL,
  `bill_number` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL,
  `tax` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bills`
--

INSERT INTO `bills` (`bill_id`, `bill_number`, `date`, `time`, `subtotal`, `tax`, `total`) VALUES
(1, 'BILL1740641542', '0000-00-00', '01:02:36', '480.00', '24.00', '504.00'),
(2, 'BILL1740646271', '0000-00-00', '02:21:14', '240.00', '12.00', '252.00'),
(3, 'BILL1740646360', '0000-00-00', '02:22:51', '0.00', '0.00', '0.00'),
(4, 'BILL1740652444', '0000-00-00', '04:04:15', '290.00', '14.50', '304.50'),
(5, 'BILL1740655149', '0000-00-00', '04:51:34', '290.00', '14.50', '304.50'),
(7, 'BILL1742017042', '0000-00-00', '11:07:25', '290.00', '14.50', '304.50'),
(8, 'BILL1742024692', '0000-00-00', '01:14:59', '310.00', '15.50', '325.50'),
(9, 'BILL1742025109', '0000-00-00', '01:21:58', '500.00', '25.00', '525.00'),
(10, 'BILL1742025141', '0000-00-00', '01:22:25', '250.00', '12.50', '262.50'),
(11, 'BILL1742025499', '0000-00-00', '01:28:24', '190.00', '9.50', '199.50'),
(12, 'BILL1742026025', '0000-00-00', '01:37:16', '450.00', '22.50', '472.50'),
(13, 'BILL1742027079', '0000-00-00', '01:54:43', '100.00', '5.00', '105.00'),
(14, 'BILL1742027638', '2025-03-15', '02:04:36', '190.00', '9.50', '199.50'),
(15, 'BILL1742031834', '2025-03-15', '03:14:03', '100.00', '5.00', '105.00'),
(16, 'BILL1742034530', '2025-03-15', '03:58:52', '100.00', '5.00', '105.00');

-- --------------------------------------------------------

--
-- Table structure for table `bill_items`
--

CREATE TABLE `bill_items` (
  `item_id` int(11) NOT NULL,
  `bill_id` int(11) DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bill_items`
--

INSERT INTO `bill_items` (`item_id`, `bill_id`, `item_name`, `quantity`, `price`, `date`) VALUES
(1, 1, 'Margherita Pizza	', 2, '100.00', NULL),
(2, 1, 'Chicken Tikka', 2, '140.00', NULL),
(3, 2, 'Margherita Pizza	', 1, '100.00', NULL),
(4, 2, 'Chicken Tikka', 1, '140.00', NULL),
(5, 4, 'Chicken Tikka', 1, '140.00', NULL),
(6, 4, 'Nan', 1, '150.00', NULL),
(7, 5, 'Chicken Tikka', 1, '140.00', NULL),
(8, 5, 'Nan', 1, '150.00', NULL),
(9, 7, 'Margherita Pizza	', 1, '100.00', NULL),
(10, 7, 'Chicken Biryani', 1, '190.00', NULL),
(13, 9, 'Nan', 1, '160.00', NULL),
(14, 9, 'Chicken Biryani', 1, '190.00', NULL),
(15, 9, 'Paneer Butter Masala', 1, '150.00', NULL),
(16, 10, 'Margherita Pizza	', 1, '100.00', NULL),
(17, 10, 'Paneer Butter Masala', 1, '150.00', NULL),
(18, 11, 'Chicken Biryani', 1, '190.00', NULL),
(19, 12, 'Chicken Tikka', 1, '140.00', NULL),
(20, 12, 'Nan', 1, '160.00', NULL),
(21, 12, 'Paneer Butter Masala', 1, '150.00', NULL),
(22, 13, 'Margherita Pizza	', 1, '100.00', '0000-00-00'),
(23, 14, 'Chicken Biryani', 1, '190.00', '2025-03-15'),
(24, 15, 'Margherita Pizza	', 1, '100.00', '2025-03-15'),
(25, 16, 'Margherita Pizza	', 1, '100.00', '2025-03-15');

-- --------------------------------------------------------

--
-- Table structure for table `dishes`
--

CREATE TABLE `dishes` (
  `dish_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dishes`
--

INSERT INTO `dishes` (`dish_id`, `name`, `price`, `description`, `image`) VALUES
(1, 'Margherita Pizza	', '100.00', 'A simple pizza with tomatoes, mozzarella, and basil', NULL),
(2, 'Chicken Tikka', '140.00', 'Grilled marinated chicken pieces, served with mint chutney.', 'uploads/Chicken Tikka.jpeg'),
(8, 'Nan', '160.00', 'this is very tastey', 'uploads/nan.jpeg'),
(9, 'Chicken Biryani', '190.00', 'Chicken Biryani', 'uploads/Chicken Biryani.jpeg'),
(10, 'Paneer Butter Masala', '150.00', 'paneer butter masala', 'uploads/67d51dac0c42f_Paneer Butter Masala.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `dish_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `purchase_id` int(11) NOT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `material_id` int(11) DEFAULT NULL,
  `purchase_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) GENERATED ALWAYS AS (`quantity` * `unit_price`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`purchase_id`, `vendor_id`, `material_id`, `purchase_date`, `quantity`, `unit_price`) VALUES
(1, 1, 1, '2025-01-25 11:39:31', 10, '1000.00'),
(3, 1, 3, '2025-01-26 06:05:42', 10, '250.00'),
(4, 1, 2, '2025-03-15 06:40:01', 10, '200.00'),
(5, 1, 4, '2025-03-15 07:38:46', 25, '50.00');

-- --------------------------------------------------------

--
-- Table structure for table `raw_materials`
--

CREATE TABLE `raw_materials` (
  `material_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `stock_quantity` int(11) NOT NULL,
  `threshold` int(11) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `date_added` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `raw_materials`
--

INSERT INTO `raw_materials` (`material_id`, `name`, `stock_quantity`, `threshold`, `unit`, `date_added`) VALUES
(1, 'Rice', 10, 10, 'Kg', '2025-01-25'),
(2, 'Chicken', 2, 8, 'Kg', '2025-01-25'),
(3, 'Oil', 4, 25, 'liters', '2025-01-25'),
(4, 'Spices', 0, 2, 'Kg', '2025-01-25'),
(5, 'Biryani Rice', 10, 5, 'Kg', '2025-01-25'),
(6, 'Mutton', 10, 5, 'Kg', '2025-01-26'),
(7, 'Tomatos', 10, 5, 'Kg', '2025-03-15');

-- --------------------------------------------------------

--
-- Table structure for table `usage_history`
--

CREATE TABLE `usage_history` (
  `usage_id` int(11) NOT NULL,
  `material_id` int(11) DEFAULT NULL,
  `usage_quantity` int(11) DEFAULT NULL,
  `usage_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usage_history`
--

INSERT INTO `usage_history` (`usage_id`, `material_id`, `usage_quantity`, `usage_date`) VALUES
(10, 1, 5, '2025-01-25'),
(11, 2, 3, '2025-01-25'),
(12, 6, 10, '2025-01-26');

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `vendor_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`vendor_id`, `name`, `contact_person`, `contact_number`, `email`, `address`, `date_added`) VALUES
(1, 'Gnashes Rice Mill', 'Athish', '9585991586', 'athish0204@gmail.com', 'Mathiyapillai MANJANAKARA STREET MADURAI', '2025-01-25 11:00:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bills`
--
ALTER TABLE `bills`
  ADD PRIMARY KEY (`bill_id`);

--
-- Indexes for table `bill_items`
--
ALTER TABLE `bill_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `bill_id` (`bill_id`);

--
-- Indexes for table `dishes`
--
ALTER TABLE `dishes`
  ADD PRIMARY KEY (`dish_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `dish_id` (`dish_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`purchase_id`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `material_id` (`material_id`);

--
-- Indexes for table `raw_materials`
--
ALTER TABLE `raw_materials`
  ADD PRIMARY KEY (`material_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `usage_history`
--
ALTER TABLE `usage_history`
  ADD PRIMARY KEY (`usage_id`),
  ADD KEY `material_id` (`material_id`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`vendor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bills`
--
ALTER TABLE `bills`
  MODIFY `bill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `bill_items`
--
ALTER TABLE `bill_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `dishes`
--
ALTER TABLE `dishes`
  MODIFY `dish_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `purchase_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `raw_materials`
--
ALTER TABLE `raw_materials`
  MODIFY `material_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `usage_history`
--
ALTER TABLE `usage_history`
  MODIFY `usage_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `vendor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bill_items`
--
ALTER TABLE `bill_items`
  ADD CONSTRAINT `bill_items_ibfk_1` FOREIGN KEY (`bill_id`) REFERENCES `bills` (`bill_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`dish_id`) REFERENCES `dishes` (`dish_id`);

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`vendor_id`),
  ADD CONSTRAINT `purchases_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `raw_materials` (`material_id`);

--
-- Constraints for table `usage_history`
--
ALTER TABLE `usage_history`
  ADD CONSTRAINT `usage_history_ibfk_1` FOREIGN KEY (`material_id`) REFERENCES `raw_materials` (`material_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
