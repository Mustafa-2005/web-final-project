-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 18, 2025 at 04:12 PM
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
-- Database: `galaxy x`
--

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `course_title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `image_name` varchar(150) NOT NULL,
  `duration` int(11) NOT NULL,
  `course_price` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_title`, `description`, `image_name`, `duration`, `course_price`) VALUES
(1, 'Space Exploration 101', 'Learn the basics of space travel, astronomy, and the history of space exploration.', 'Space Exploration 101.jpg', 40, 300),
(2, 'Advanced Astrophysics', 'Deep dive into the wonders of the universe with advanced astrophysics, covering topics like black holes, dark matter, and cosmology.', 'Advanced Astrophysics.jpg', 60, 450),
(3, 'Rocket Science Basics', 'Understand the fundamentals of rocket engineering, propulsion systems, and orbital mechanics.', 'Rocket Science Basics.jpeg', 50, 400);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_data` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,0) NOT NULL,
  `shipping_address` text NOT NULL,
  `status` enum('arrived','cancelled','processing','') NOT NULL DEFAULT 'processing'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_item`
--

CREATE TABLE `order_item` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_type` enum('product','trip','course') NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_name` varchar(150) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `total_price` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount_paid` decimal(10,0) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `store`
--

CREATE TABLE `store` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `categore` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `image_name` varchar(150) NOT NULL,
  `in_stock` int(11) NOT NULL,
  `product_price` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `store`
--

INSERT INTO `store` (`product_id`, `product_name`, `categore`, `description`, `image_name`, `in_stock`, `product_price`) VALUES
(1, 'Space Suit Pro', 'Apparel', 'Advanced suit for zero-gravity conditions with thermal shield.', 'Space Suit Pro.jpg', 10, 15000),
(2, 'Zero-G Snacks', 'Food', 'Delicious freeze-dried space food in different flavors.', 'Zero-G Snacks.jpg', 100, 120),
(3, 'Meteorite Fragment', 'Collectibles', 'Authentic meteorite piece from outer space, certified.', 'Meteorite Fragment.jpg', 5, 9999),
(4, 'Galaxy Poster Set', 'Decor', '3 high-quality posters of galaxies, nebulae, and planets.', 'Galaxy Poster Set.jpg', 50, 50),
(5, 'Moon Dust Vial', 'Collectibles', 'Special edition moon dust sample for collectors.', 'Moon Dust Vial.jpg', 20, 2500),
(6, 'Alien Plush Toy', 'Toys', 'Cute and cuddly alien plush with glowing eyes.', 'Alien Plush Toy.jpg', 75, 25),
(7, 'Explorer Helmet', 'Apparel', 'Replica of astronaut helmet with voice modulation feature.', 'Explorer Helmet.jpg', 15, 1200),
(8, 'VR Galaxy Tour', 'Experiences', 'Experience the Milky Way in VR with 360° immersion pack.', 'VR Galaxy Tour.jpg', 30, 350),
(9, 'Alien Artifact Replica', 'Collectibles', 'Inspired by ancient space myths. A mystery in your hands.', 'Alien Artifact Replica.jpg', 25, 650);

-- --------------------------------------------------------

--
-- Table structure for table `trips`
--

CREATE TABLE `trips` (
  `trip_id` int(11) NOT NULL,
  `trip_name` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `image_name` varchar(150) NOT NULL,
  `view_details_link` varchar(150) NOT NULL,
  `duration` int(11) NOT NULL,
  `trip_start_data` date NOT NULL,
  `Destination` varchar(150) NOT NULL,
  `capacity` int(11) NOT NULL,
  `trip_price` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trips`
--

INSERT INTO `trips` (`trip_id`, `trip_name`, `description`, `image_name`, `view_details_link`, `duration`, `trip_start_data`, `Destination`, `capacity`, `trip_price`) VALUES
(1, 'Moon Tour', '3-Day trip around the moon. A once-in-a-lifetime experience.', 'cm-09.jpg', '../html/moon-tour.html', 3, '2025-08-01', 'Moon', 4, 25000),
(2, 'Mars Adventure', 'Explore the red planet in a 7-day mission with expert guidance.', 'cm-11.jpg', '../html/mars-adventure.html', 7, '2025-09-15', 'Mars', 6, 120000),
(3, 'ISS Orbit Tour', 'Live on the International Space Station for 2 nights.', 'cm-12.jpg', '../html/iss-orbit-tour.html', 2, '2025-07-20', 'Low Earth Orbit', 3, 80000),
(4, 'Jupiter Exploration', '10-day journey to explore Jupiter and its moons.', 'cm-13.jpg', '../html/jupiter-exploration.html', 10, '2026-01-10', 'Jupiter System', 2, 250000),
(5, 'Saturn Voyage', 'Fly by Saturn and its stunning rings in this unforgettable trip.', 'download (14).png', '../html/saturn-voyage.html', 5, '2026-03-05', 'Saturn System', 4, 175000),
(6, 'Andromeda Expedition', 'Journey through the Andromeda galaxy with our experienced astronauts.', 'Andromeda.jpeg', '../html/andromeda-expedition.html', 30, '2027-01-01', 'Andromeda Galaxy', 2, 500000),
(7, 'Alien Planet Visit', 'A unique expedition to a newly discovered alien planet.', 'Venus.jpg', '../html/align.html', 14, '2026-06-10', 'Exoplanet Kepler-186f', 3, 300000),
(8, 'A Stellar Bouquet Visit', 'Discover vibrant nebulae and star clusters in this breathtaking tour.', 'A Stellar Bouquet Visit.webp', '../html/Stellar.html', 10, '2026-08-20', 'Carina Nebula Region', 4, 400000),
(9, 'Helix Nebula Flyby', 'Witness the intricate beauty of the Helix Nebula up close.', 'helix.webp', '../html/helix.html', 4, '2025-11-01', 'Helix Nebula', 3, 200000);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `address` text NOT NULL,
  `password` varchar(150) NOT NULL,
  `role` enum('customer','admin') NOT NULL DEFAULT 'customer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `address`, `password`, `role`) VALUES
(1, 'Mustafa fathy wahby', 'mustafa@gmail.com', 'home', '$2y$10$cLm4WviNsTUVfYJzJs/Iu.9YkGzXnR4MzVVQHMdH6dBHUuApW8AGK', 'customer'),
(2305190, 'mustafa fathy', '2305190@gmail.com', 'home', '$2y$10$U8IGYIQF8uerVw71kjiK4O1he5bvcRBDFO/y6cjSrTXjW0uTHXH8q', 'admin'),
(2305197, 'Mahmoud Mansour', 'mahmoudmansour552015@gmail.com', 'Edko', '$2y$12$VMuoWsrwoa3pHsRoYqEn5Ow8QOEFFGFBpQ7viMThA7fzjp1Of7Y/u', 'admin'),
(2305198, 'Mustafa fathy', 'mu029968@gmail.com', 'home', '$2y$10$0ykm5RcqKC/DJ6lFU3d0SulJFoa58MO0XnDd0IW6zCy6sx1wOTxCq', 'customer'),
(2305201, 'abdo', '2305152@gmail.com', 'home', '$2y$10$fNuwUQiBpFOoZ1noGV1/SuRiMz559.iUIattP64S7FzqJfcGab64.', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`) USING BTREE;

--
-- Indexes for table `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `payments_ibfk_1` (`order_id`);

--
-- Indexes for table `store`
--
ALTER TABLE `store`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `trips`
--
ALTER TABLE `trips`
  ADD PRIMARY KEY (`trip_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_item`
--
ALTER TABLE `order_item`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `store`
--
ALTER TABLE `store`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `trips`
--
ALTER TABLE `trips`
  MODIFY `trip_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `order_item_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `orders` (`user_id`);
COMMIT;

