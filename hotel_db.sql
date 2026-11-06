-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Nov 06, 2026 at 08:17 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hotel_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','confirmed','checked_in','checked_out','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `customer_id` int(11) DEFAULT NULL,
  `payment_status` enum('unpaid','paid') NOT NULL DEFAULT 'unpaid',
  `payment_method` varchar(30) DEFAULT NULL,
  `transaction_id` varchar(40) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `guest_id`, `room_id`, `check_in`, `check_out`, `total_amount`, `status`, `created_at`, `customer_id`, `payment_status`, `payment_method`, `transaction_id`, `paid_at`) VALUES
(1, 1, 4, '2026-11-05', '2026-11-10', 17495.00, 'checked_out', '2026-11-04 04:41:07', NULL, 'unpaid', NULL, NULL, NULL),
(2, 2, 1, '2026-11-13', '2026-11-14', 1999.00, 'cancelled', '2026-11-04 04:47:47', NULL, 'unpaid', NULL, NULL, NULL),
(3, 3, 6, '2027-10-04', '2027-10-06', 11998.00, 'checked_out', '2026-11-04 05:36:50', NULL, 'unpaid', NULL, NULL, NULL),
(4, 4, 11, '2026-11-05', '2026-11-08', 17997.00, 'checked_out', '2026-11-04 09:58:40', NULL, 'unpaid', NULL, NULL, NULL),
(5, 5, 6, '2026-11-04', '2026-11-07', 17997.00, 'checked_out', '2026-11-04 12:19:41', NULL, 'unpaid', NULL, NULL, NULL),
(6, 6, 1, '2026-11-26', '2026-11-30', 7996.00, 'checked_in', '2026-11-04 12:26:11', NULL, 'unpaid', NULL, NULL, NULL),
(7, 7, 2, '2026-11-26', '2026-11-30', 7996.00, 'checked_in', '2026-11-04 12:28:47', NULL, 'paid', 'Cash at Hotel', NULL, '2026-11-05 11:15:27'),
(8, 8, 1, '2026-11-06', '2026-11-07', 1999.00, 'cancelled', '2026-11-04 12:30:09', 1, 'unpaid', NULL, NULL, NULL),
(9, 9, 13, '2027-01-01', '2027-01-04', 13497.00, 'checked_in', '2026-11-04 12:55:41', NULL, 'unpaid', NULL, NULL, NULL),
(10, 10, 5, '2026-11-08', '2026-11-16', 27992.00, 'checked_in', '2026-11-05 04:51:35', NULL, 'unpaid', NULL, NULL, NULL),
(11, 11, 1, '2026-11-06', '2026-11-08', 3998.00, 'cancelled', '2026-11-05 04:57:02', 2, 'unpaid', NULL, NULL, NULL),
(12, 12, 3, '2026-11-27', '2026-11-28', 1999.00, 'checked_in', '2026-11-05 05:21:01', 2, 'paid', 'Cash at Hotel', NULL, '2026-11-05 11:14:28'),
(13, 13, 13, '2026-11-05', '2026-11-06', 4499.00, 'checked_in', '2026-11-05 05:53:31', 2, 'paid', 'Cash at Hotel', NULL, '2026-11-05 11:24:23'),
(14, 14, 14, '2026-11-05', '2026-11-11', 26994.00, 'checked_in', '2026-11-05 11:42:09', NULL, 'paid', 'Cash at Hotel', NULL, '2026-11-05 17:12:35');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(150) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `phone`, `subject`, `message`, `created_at`, `is_read`) VALUES
(1, 'Vanshika Gupta', 'vanshikagupta7505@gmail.com', '', 'booking', 'i want to book my room', '2026-11-04 11:40:19', 1),
(2, 'anushka', 'ncjdb@gmail.com', '464657465673', 'rjhbfh', 'nfjrff', '2026-11-04 11:42:03', 1);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `password`, `created_at`) VALUES
(1, 'vanshika gupta', 'admin@grandstay.com', '64576736', '$2y$10$sI48dBBx0JaglueNb1Vp9eL28jSLtwHwEueNzPR9spoo.hi1yaKxW', '2026-11-04 12:29:38'),
(2, 'yashvi gupta', 'yashvi@gmail.com', '7548787836', '$2y$10$b.im1ttZcvDBP2eG/831n.u7ZO5HrlF73mJhqjzvae0UxCyBkn/uu', '2026-11-05 04:56:29');

-- --------------------------------------------------------

--
-- Table structure for table `food_items`
--

CREATE TABLE `food_items` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `available` tinyint(1) DEFAULT 1,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food_items`
--

INSERT INTO `food_items` (`id`, `name`, `category`, `description`, `price`, `available`, `image`) VALUES
(1, 'Paneer Butter Masala', 'Main Course', 'Creamy tomato gravy with soft paneer', 280.00, 1, 'paneer.jpg'),
(2, 'Dal Makhani', 'Main Course', 'Slow cooked black lentils with butter', 220.00, 1, 'dal.jpg'),
(3, 'Veg Biryani', 'Main Course', 'Fragrant rice with vegetables and spices', 240.00, 1, 'biryani.jpg'),
(4, 'Butter Naan', 'Breads', 'Soft tandoor naan with butter', 45.00, 1, 'naan.jpg'),
(5, 'Tandoori Roti', 'Breads', 'Whole wheat roti from the tandoor', 25.00, 1, 'roti.jpg'),
(6, 'Veg Sandwich', 'Snacks', 'Grilled sandwich with fresh veggies', 120.00, 1, 'sandwich.jpg'),
(7, 'French Fries', 'Snacks', 'Crispy golden fries', 110.00, 1, 'fries.jpg'),
(8, 'Masala Chai', 'Beverages', 'Hot Indian spiced tea', 40.00, 1, 'chai.jpg'),
(9, 'Cold Coffee', 'Beverages', 'Chilled creamy coffee', 120.00, 1, 'coffee.jpg'),
(10, 'Gulab Jamun', 'Desserts', 'Two warm syrupy jamuns', 90.00, 1, 'jamun.jpg'),
(13, 'pizza', 'Snacks', 'Cheesy and crunchy', 300.00, 1, 'food_6aec5fdc61124.jpg'),
(14, 'burger', 'Snacks', 'Fluffy and Cheese', 150.00, 1, 'food_6aec5ff3ae311.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `food_orders`
--

CREATE TABLE `food_orders` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','preparing','delivered','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food_orders`
--

INSERT INTO `food_orders` (`id`, `booking_id`, `total`, `status`, `created_at`) VALUES
(1, 1, 240.00, 'preparing', '2026-11-04 10:52:20'),
(2, 1, 240.00, 'delivered', '2026-11-04 11:22:06'),
(3, 4, 440.00, 'delivered', '2026-11-04 11:22:23'),
(4, 1, 140.00, 'preparing', '2026-11-04 11:34:37');

-- --------------------------------------------------------

--
-- Table structure for table `food_order_items`
--

CREATE TABLE `food_order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food_order_items`
--

INSERT INTO `food_order_items` (`id`, `order_id`, `item_id`, `qty`, `price`) VALUES
(1, 1, 9, 2, 120.00),
(2, 2, 9, 2, 120.00),
(3, 3, 9, 3, 120.00),
(4, 3, 8, 2, 40.00),
(5, 4, 4, 2, 45.00),
(6, 4, 5, 2, 25.00);

-- --------------------------------------------------------

--
-- Table structure for table `guests`
--

CREATE TABLE `guests` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `id_proof` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guests`
--

INSERT INTO `guests` (`id`, `full_name`, `email`, `phone`, `id_proof`, `created_at`) VALUES
(1, 'Vanshika gupta', 'guptavanshu0311@gmail.com', '1234567890', NULL, '2026-11-04 04:41:07'),
(2, 'Tanuja Gupta', 'Tanu0319@gmail.com', '4536563665', NULL, '2026-11-04 04:47:47'),
(3, 'saloni gupta', 'saloni@gmail.com', '5478363452', NULL, '2026-11-04 05:36:50'),
(4, 'Tanya', 'abc@gmail.com', '2354647346', NULL, '2026-11-04 09:58:40'),
(5, 'sejal gaba', 'sejal@gmail.com', '647563786475', NULL, '2026-11-04 12:19:41'),
(6, 'radhika', 'radhu0311@gmail.com', '543667365635', NULL, '2026-11-04 12:26:11'),
(7, 'radhika', 'radhu0311@gmail.com', '543667365635', NULL, '2026-11-04 12:28:47'),
(8, 'vanshika gupta', 'admin@grandstay.com', '64576736', NULL, '2026-11-04 12:30:09'),
(9, 'Prashant gupta', 'prashant1234@gmail.com', '6398675570', NULL, '2026-11-04 12:55:41'),
(10, 'Garima Pathak', 'garima@gmail.com', '128903457', NULL, '2026-11-05 04:51:35'),
(11, 'yashvi gupta', 'yashvi@gmail.com', '7548787836', NULL, '2026-11-05 04:57:02'),
(12, 'yashvi gupta', 'yashvi@gmail.com', '7548787836', NULL, '2026-11-05 05:21:01'),
(13, 'Vishnu Saraswat', 'vishnu@gmail.com', '9706867858', NULL, '2026-11-05 05:53:31'),
(14, 'Parmanand Gupta', 'pappu@gmail.com', '45638678367', NULL, '2026-11-05 11:42:09');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `comment` text NOT NULL,
  `visible` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `booking_id`, `customer_id`, `name`, `rating`, `comment`, `visible`, `created_at`) VALUES
(1, 12, 2, 'yashvi gupta', 5, 'amazing hotel', 1, '2026-11-05 05:24:13');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `room_number` varchar(10) NOT NULL,
  `room_type_id` int(11) NOT NULL,
  `status` enum('available','occupied','cleaning','maintenance') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room_number`, `room_type_id`, `status`) VALUES
(1, '101', 1, 'occupied'),
(2, '102', 1, 'occupied'),
(3, '103', 1, 'occupied'),
(4, '201', 2, 'cleaning'),
(5, '202', 2, 'occupied'),
(6, '301', 3, 'cleaning'),
(7, '104', 1, 'maintenance'),
(8, '105', 1, 'available'),
(9, '203', 2, 'available'),
(10, '204', 2, 'available'),
(11, '302', 3, 'cleaning'),
(12, '205', 2, 'available'),
(13, '303', 4, 'occupied'),
(14, '304', 4, 'occupied');

-- --------------------------------------------------------

--
-- Table structure for table `room_types`
--

CREATE TABLE `room_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price_per_night` decimal(10,2) NOT NULL,
  `max_guests` int(11) DEFAULT 2,
  `image` varchar(255) DEFAULT NULL,
  `pano` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`id`, `name`, `description`, `price_per_night`, `max_guests`, `image`, `pano`) VALUES
(1, 'Standard Room', 'Comfortable room with AC, TV and free WiFi.', 1999.00, 2, 'standard.jpg', 'standard360.jpg'),
(2, 'Deluxe Room', 'Spacious room with king bed, balcony and breakfast.', 3499.00, 3, 'deluxe.jpg', 'deluxe360.jpg'),
(3, 'Suite', 'Luxury suite with living area, bathtub and city view.', 5999.00, 4, 'suite.jpg', 'suite360.jpg'),
(4, 'Family Room', 'Large room with 2 beds, perfect for families.', 4499.00, 5, 'family.jpg', 'familyroom360.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','reception','kitchen','staff') NOT NULL DEFAULT 'reception',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `active`) VALUES
(1, 'Vanshika', 'admin@grandstay.com', '$2y$10$70wMkO8hgD.51G4eQCRqm.jR6OD63/jGCUH8NBflcGkQA8PpTku4W', 'admin', '2026-11-04 04:22:14', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guest_id` (`guest_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `food_items`
--
ALTER TABLE `food_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `food_orders`
--
ALTER TABLE `food_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `food_order_items`
--
ALTER TABLE `food_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `guests`
--
ALTER TABLE `guests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_id` (`booking_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_number` (`room_number`),
  ADD KEY `room_type_id` (`room_type_id`);

--
-- Indexes for table `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `food_items`
--
ALTER TABLE `food_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `food_orders`
--
ALTER TABLE `food_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `food_order_items`
--
ALTER TABLE `food_order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `guests`
--
ALTER TABLE `guests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);

--
-- Constraints for table `food_orders`
--
ALTER TABLE `food_orders`
  ADD CONSTRAINT `food_orders_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`);

--
-- Constraints for table `food_order_items`
--
ALTER TABLE `food_order_items`
  ADD CONSTRAINT `food_order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `food_orders` (`id`),
  ADD CONSTRAINT `food_order_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `food_items` (`id`);

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
