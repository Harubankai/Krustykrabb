-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 31, 2026 at 04:01 PM
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
-- Database: `qtieeyons`
--

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL DEFAULT 'meal',
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `image` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `name`, `category`, `description`, `price`, `image`, `created_at`, `updated_at`) VALUES
(1, 'Krabby Patty', 'meal', 'The legendary signature burger of the Krusty Krab', 45.00, 'images/krabpat.jpg', '2026-03-30 19:35:11', '2026-03-30 19:35:11'),
(2, 'Double Krabby Patty', 'meal', 'Two juicy patties stacked with sea-fresh toppings.', 55.00, 'images/double_kp.jpg', '2026-03-30 19:35:12', '2026-03-30 19:35:12'),
(3, 'Triple Krabby Patty', 'meal', 'Three layers of the legendary Krabby Patty flavor.', 65.00, 'images/triple_kp.jpg', '2026-03-30 19:35:13', '2026-03-30 19:35:13'),
(4, 'Coral Bits (Small)', 'meal', 'Crispy bite-sized coral bits, perfect snack size.', 30.00, 'images/small_cb.png', '2026-03-30 19:35:14', '2026-03-30 19:35:14'),
(5, 'Coral Bits (Medium)', 'meal', 'Golden coral bits in a medium sharing-size basket.', 40.00, 'images/medium_cb.png', '2026-03-30 19:35:15', '2026-03-30 19:35:15'),
(6, 'Coral Bits (Large)', 'meal', 'Large crispy coral bits for the hungriest customers.', 50.00, 'images/large_cb.png', '2026-03-30 19:35:16', '2026-03-30 19:35:16'),
(7, 'Kelp Shake (Small)', 'drinks', 'Small-size kelp shake for quick refreshment.', 45.00, 'images/kelpshake.jpg', '2026-03-30 19:35:17', '2026-03-30 19:35:17'),
(8, 'Kelp Shake (Medium)', 'drinks', 'Medium-size kelp shake to keep you going.', 50.00, 'images/kelpshake.jpg', '2026-03-30 19:35:18', '2026-03-30 19:35:18'),
(9, 'Kelp Shake (Large)', 'drinks', 'Big-size kelp shake for maximum refreshment.', 55.00, 'images/kelpshake.jpg', '2026-03-30 19:35:19', '2026-03-30 19:35:19'),
(10, 'Seafoam Soda (Small)', 'drinks', 'Fizzing seafoam soda in a small chilled cup.', 35.00, 'images/seafoam.jpg', '2026-03-30 19:35:20', '2026-03-30 19:35:20'),
(11, 'Seafoam Soda (Medium)', 'drinks', 'Classic bubbly seafoam soda for everyday meals.', 40.00, 'images/seafoam.jpg', '2026-03-30 19:35:21', '2026-03-30 19:35:21'),
(12, 'Seafoam Soda (Large)', 'drinks', 'Large sparkling seafoam soda to complete your order.', 45.00, 'images/seafoam.jpg', '2026-03-30 19:35:22', '2026-03-30 19:35:22');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2026_02_10_153429_create_users_table', 1),
(2, '2026_02_10_155128_create_sessions_table', 1),
(3, '2026_03_06_102943_create_user_table', 1),
(4, '2026_03_06_125322_add_password_reset_to_user_table', 1),
(5, '2026_03_10_000001_insert_default_admin_user', 1),
(6, '2026_03_10_120000_add_contact_fields_to_user_table', 1),
(7, '2026_03_12_000001_insert_default_rider_user', 1),
(8, '2026_03_12_150449_create_orders_and_items_tables', 1),
(9, '2026_03_31_030426_add_rider_and_timestamps_to_orders_table', 2),
(10, '2026_03_31_031233_drop_wrong_foreign_key_on_orders', 3),
(11, '2026_03_31_032627_create_menu_items_table', 4);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` varchar(255) DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `rider_id` bigint(20) UNSIGNED DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `total_items` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Preparing',
  `delivery_step` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `accepted_at` timestamp NULL DEFAULT NULL,
  `picked_up_at` timestamp NULL DEFAULT NULL,
  `in_transit_at` timestamp NULL DEFAULT NULL,
  `arrived_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_id`, `customer_id`, `rider_id`, `total`, `total_items`, `payment_method`, `status`, `delivery_step`, `created_at`, `updated_at`, `accepted_at`, `picked_up_at`, `in_transit_at`, `arrived_at`, `completed_at`) VALUES
(3, 'ORD-1774926785234', 3, 1, 180.00, 4, 'Cash on Delivery', 'Completed', 4, '2026-03-30 19:13:05', '2026-03-30 19:14:18', '2026-03-30 19:13:23', NULL, '2026-03-30 19:13:36', '2026-03-30 19:13:58', '2026-03-30 19:14:18'),
(4, 'ORD-1774926874330', 3, 1, 45.00, 1, 'Cash on Delivery', 'Completed', 4, '2026-03-30 19:14:35', '2026-03-30 19:16:45', '2026-03-30 19:14:53', NULL, '2026-03-30 19:15:02', '2026-03-30 19:15:55', '2026-03-30 19:16:45'),
(5, 'ORD-1774926991906', 3, 1, 45.00, 1, 'Cash on Delivery', 'Completed', 4, '2026-03-30 19:16:32', '2026-03-30 19:17:46', '2026-03-30 19:16:52', NULL, '2026-03-30 19:16:59', '2026-03-30 19:17:28', '2026-03-30 19:17:46'),
(6, 'ORD-1774927284858', 3, 1, 30.00, 1, 'Cash on Delivery', 'Completed', 4, '2026-03-30 19:21:25', '2026-03-30 19:23:09', '2026-03-30 19:21:35', NULL, '2026-03-30 19:21:43', '2026-03-30 19:22:28', '2026-03-30 19:23:09');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `name`, `price`, `quantity`, `image`, `created_at`, `updated_at`) VALUES
(1, 5, 'Krabby Patty', 45.00, 1, NULL, '2026-03-30 19:16:32', '2026-03-30 19:16:32'),
(2, 6, 'Coral Bits (Small)', 30.00, 1, NULL, '2026-03-30 19:21:25', '2026-03-30 19:21:25');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `token_expires_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `phone`, `address`, `password`, `role`, `created_at`, `updated_at`, `password_reset_token`, `token_expires_at`) VALUES
(1, 'Admin', 'admin@gmail.com', NULL, NULL, '$2y$12$zKiZCFH.F/sXFp8fy7eDU.n4m8NOtnjRGggJKJ6bUxPGQnU2FuYgS', 'admin', '2026-03-30 09:55:44', '2026-03-30 09:55:44', NULL, NULL),
(2, 'Rider', 'rider@gmail.com', NULL, NULL, '$2y$12$gzodniD7.PAQF9WeHa6dPONfmZMOjcrXGVpDzG2TqeYF1GNEstCma', 'rider', '2026-03-30 09:55:44', '2026-03-30 09:55:44', NULL, NULL),
(3, 'Yona Pandak', 'akatsukiharold25@gmail.com', NULL, NULL, '$2y$12$vzj.IqeSaNPk4ShNJ/YAJ.mt/T2vWP0t31w.yzqrEBuJr6ZLNXD9u', 'customer', '2026-03-30 09:56:46', '2026-03-30 10:58:13', '5Diw2esZErb0yuVrIwML0HodYNGGySho5LPYbaZcfEf3bXWT2wO2plN3Wr82', '2026-03-30 11:58:13'),
(4, 'mang elyot', 'genjutsukaido25@gmail.com', '092222222121212', NULL, '$2y$12$Q3vCsIPp.RiBHTUlCvnNAORwxEAy8A.HyvwTLVVeeMMMN2xBMt2jy', 'rider', '2026-03-30 09:57:42', '2026-03-30 09:57:42', NULL, NULL),
(5, 'itsmeharu', 'ayawonleona@gmail.com', NULL, NULL, '$2y$12$ptzSawpf.MOOcen9X9sWWeFoQ9Tg/NBDZf80PUB2SmjLGxU/gIhV2', 'customer', '2026-03-30 10:28:10', '2026-03-30 11:05:33', '5axsVgHchJvDtdkpZxfCGvNFVFaOBwk7koClX9N1SjqCh8Zc8DCYQ96lU2ZX', '2026-03-30 12:05:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_id_unique` (`order_id`),
  ADD KEY `orders_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_email_unique` (`email`);

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
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
