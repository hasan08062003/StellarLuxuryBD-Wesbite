-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 31, 2026 at 08:59 AM
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
-- Database: `my_ecommerce`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`) VALUES
(1, 'Md Rabiul Hasan', 'admin@stellarluxury.bd', '602044');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variant_id` int(11) DEFAULT 0,
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `trx_id` varchar(100) DEFAULT NULL,
  `payment_screenshot` varchar(255) DEFAULT NULL,
  `payment_status` varchar(20) DEFAULT 'Pending',
  `order_status` varchar(20) DEFAULT 'to_pay',
  `shipping_address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `payment_note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_id`, `user_id`, `total_amount`, `payment_method`, `trx_id`, `payment_screenshot`, `payment_status`, `order_status`, `shipping_address`, `created_at`, `status`, `payment_note`) VALUES
(1, 'ORD-6A5B3A69189A4', 1, 3120.00, 'manual', '96GHI876JKL', 'useruploads/pay_ORD-6A5B3A69189A4_1784363625.png', 'received', 'completed', 'Hirajheel Siddhirganj', '2026-07-18 08:33:45', 'Pending', NULL),
(2, 'ORD-6A5B400ED1876', 1, 970.00, 'manual', 'dstdstdfstgag', 'useruploads/pay_ORD-6A5B400ED1876_1784365070.jpg', 'Pending', 'to_receive', 'Hirajheel Siddhirganj', '2026-07-18 08:57:50', 'Pending', NULL),
(3, 'ORD-6A5B410B7FB11', 1, 3720.00, 'manual', 'jhfkyhfjufiyu', 'useruploads/pay_ORD-6A5B410B7FB11_1784365323.jpg', 'Pending', 'to_ship', 'Hirajheel Siddhirganj', '2026-07-18 09:02:03', 'Pending', NULL),
(4, 'ORD-6A5B48CC9B400', 1, 1620.00, 'points', '', '', 'Approved', 'completed', 'Hirajheel Siddhirganj', '2026-07-18 09:35:08', 'approved', NULL),
(5, 'ORD-6A5C984C76862', 1, 4620.00, 'gateway', '', '', 'Approved', 'to_ship', 'Hirajheel Siddhirganj', '2026-07-19 09:26:36', 'Pending', NULL),
(8, 'ORD-6A5E150482306', 2, 3120.00, 'gateway', '', '', 'Approved', 'to_ship', '14 no Road Chittagongroad', '2026-07-20 12:31:00', 'Pending', NULL),
(9, 'ORD-6A5E23633E086', 2, 1620.00, 'manual', 'Ushhzsjsjjsj', 'useruploads/pay_ORD-6A5E23633E086_1784554339.jpg', 'Pending', 'to_pay', '14 no Road Chittagongroad', '2026-07-20 13:32:19', 'Pending', NULL),
(10, 'ORD-6A5E2A23DAF41', 4, 4620.00, 'manual', 'Ghhhjjvvhj', 'useruploads/pay_ORD-6A5E2A23DAF41_1784556067.jpg', 'Pending', 'to_pay', 'Gaja khor', '2026-07-20 14:01:07', 'Pending', NULL),
(11, 'ORD-6A69D1AD97A16', 1, 1620.00, 'manual', 'nk.jbggl', 'useruploads/pay_ORD-6A69D1AD97A16_1785319853.png', 'Pending', 'to_pay', 'Hirajheel Siddhirganj 14 road', '2026-07-29 10:10:53', 'Pending', NULL),
(17, 'ORD-6A6A3992C6116', 1, 620.00, 'bkash', NULL, 'useruploads/pay_ORD-6A6A3992C6116_1785346450.jpg', 'Partial Paid', 'to_ship', 'Hirajheel Siddhirganj 14 road', '2026-07-29 17:34:10', 'Pending', NULL),
(18, 'ORD-6A6A505BAB5AD', 1, 16785.00, 'bkash', NULL, 'useruploads/pay_ORD-6A6A505BAB5AD_1785352283.jpg', 'Paid', 'to_ship', 'Hirajheel Siddhirganj 14 road', '2026-07-29 19:11:23', 'Pending', NULL),
(19, 'ORD-6A6B901EC3DD4', 4, 320.00, 'nagad', NULL, 'useruploads/pay_ORD-6A6B901EC3DD4_1785434142.png', 'Approved', 'to_ship', 'Gaja khor', '2026-07-30 17:55:42', 'Pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `variant_id` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `variant_id`) VALUES
(1, 1, 5, 2, 1500.00, 0),
(2, 2, 3, 1, 850.00, 0),
(3, 3, 1, 3, 1200.00, 0),
(4, 4, 5, 1, 1500.00, 0),
(5, 5, 5, 3, 1500.00, 0),
(6, 6, 3, 3, 850.00, 0),
(7, 7, 3, 3, 850.00, 0),
(8, 8, 5, 2, 1500.00, 0),
(9, 9, 5, 1, 1500.00, 0),
(10, 10, 5, 3, 1500.00, 0),
(11, 11, 5, 1, 1500.00, 0),
(12, 12, 5, 1, 1500.00, 0),
(13, 15, 10, 2, 500.00, 2),
(14, 15, 10, 2, 500.00, 3),
(15, 16, 5, 1, 1500.00, 0),
(16, 17, 10, 1, 500.00, 2),
(17, 18, 13, 1, 5555.00, 6),
(18, 18, 13, 1, 5555.00, 7),
(19, 18, 13, 1, 5555.00, 8),
(20, 19, 6, 1, 200.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `payment_requests`
--

CREATE TABLE `payment_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `trx_id` varchar(100) DEFAULT NULL,
  `screenshot` varchar(255) NOT NULL,
  `note` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_requests`
--

INSERT INTO `payment_requests` (`id`, `user_id`, `order_id`, `amount`, `payment_method`, `trx_id`, `screenshot`, `note`, `status`, `created_at`) VALUES
(1, 1, 'ORD-6A69D61D8B1EF', 1620.00, 'bkash', NULL, 'useruploads/pay_ORD-6A69D61D8B1EF_1785320989.jpg', 'aiiii', 'Approved', '2026-07-29 10:29:49'),
(2, 1, 'ORD-6A6A372BAD5C1', 2120.00, 'bkash', NULL, 'useruploads/pay_ORD-6A6A372BAD5C1_1785345835.jpg', '', 'Approved', '2026-07-29 17:23:55'),
(3, 1, 'ORD-6A6A38918ED45', 1620.00, 'bkash', NULL, 'useruploads/pay_ORD-6A6A38918ED45_1785346193.jpg', '', 'Approved', '2026-07-29 17:29:53'),
(4, 1, 'ORD-6A6A3992C6116', 620.00, 'bkash', NULL, 'useruploads/pay_ORD-6A6A3992C6116_1785346450.jpg', '', 'Approved', '2026-07-29 17:34:10'),
(5, 1, 'ORD-6A6A505BAB5AD', 16785.00, 'bkash', NULL, 'useruploads/pay_ORD-6A6A505BAB5AD_1785352283.jpg', 'bhdfgbdfhgf', 'Approved', '2026-07-29 19:11:23'),
(6, 4, 'ORD-6A6B901EC3DD4', 320.00, 'nagad', NULL, 'useruploads/pay_ORD-6A6B901EC3DD4_1785434142.png', '01827', 'Approved', '2026-07-30 17:55:42');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `material` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `stock_status` varchar(50) NOT NULL,
  `image` varchar(255) NOT NULL,
  `video` varchar(255) DEFAULT NULL,
  `image2` varchar(255) DEFAULT NULL,
  `image3` varchar(255) DEFAULT NULL,
  `image4` varchar(255) DEFAULT NULL,
  `image5` varchar(255) DEFAULT NULL,
  `image6` varchar(255) DEFAULT NULL,
  `image7` varchar(255) DEFAULT NULL,
  `image8` varchar(255) DEFAULT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `material`, `price`, `old_price`, `stock_status`, `image`, `video`, `image2`, `image3`, `image4`, `image5`, `image6`, `image7`, `image8`, `description`) VALUES
(1, 'Premium Silver Adjustable Ring', 'Ring', 'Silver', 1200.00, 1500.00, 'Pre-Order', 'uploads/Ring.jpg', 'images/bg-Video.mp4', 'uploads/Neckleace.jpg', 'uploads/Earring.jpg', NULL, NULL, NULL, NULL, NULL, 'Beautifully crafted pure silver ring for daily wear.'),
(2, 'Stainless Steel Cuban Chain Necklace', 'Necklace', 'Stainless Steel', 1800.00, 2200.00, 'Pre-order', 'uploads/Neckleace.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Heavy duty stainless steel chain necklace, rust-proof.'),
(3, 'Classic Rose Gold Bracelet', 'Bracelet', 'Normal', 850.00, 950.00, 'Not Available ', 'uploads/Breclet.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Trendy alloy alloy metal bracelet with custom finishing.'),
(5, 'Luxury Diamond Stud Earring', 'Earring', 'Stainless Steel', 1500.00, 1800.00, 'In Stock', 'uploads/Earring.jpg', 'images/bg-Video.mp4', 'uploads/Ring.jpg', 'uploads/Neckleace.jpg', NULL, NULL, NULL, NULL, NULL, 'Elegant and stylish earrings for special occasions.'),
(6, 'Couple Ring Set', 'Ring', 'Copper', 200.00, 300.00, 'In Stock', 'images/img1_1785323722_5739.jpg', 'images/vid_1785323722_5939.mp4', 'images/img2_1785334605_7730.jpg', '', '', '', '', '', '', 'Couple der jonno Sei akta ring set . '),
(7, 'Ledis Modern Bag ', 'Handbag', 'leather ', 500.00, 700.00, 'Out of Stock', 'images/img1_1785327863_5271.webp', 'images/vid_1785327863_2997.mp4', '', '', '', '', '', '', '', 'Quality 1:1 Grade . '),
(10, 'bag', 'Handbag', 'leather ', 500.00, 700.00, 'In Stock', 'images/img1_1785333615_8632.jpg', 'images/vid_1785333615_3962.mp4', 'images/img2_1785333615_2886.jpg', '', '', '', '', '', '', 'aaaaaaa'),
(11, 'shoes ', 'Heels', '', 5000.00, 6000.00, 'Out of stock', 'images/img1_1785346686_2882.jpg', 'images/vid_1785346686_8886.mp4', 'images/img2_1785346686_3031.png', '', '', '', '', '', '', 'fffffffffffffff'),
(12, 'Hasan', 'Bag', 'leather ', 555.00, 600.00, 'Pre-order', 'images/img1_1785349746_9359.jpg', 'images/vid_1785349746_7784.mp4', '', '', '', '', '', '', '', 'agadfsgafasdfgdfagdf'),
(13, 'sfdsfgdgdfs', 'Bag', '', 5555.00, 6666.00, 'In Stock', 'images/img1_1785352255_8293.jpg', 'images/vid_1785352255_5767.mp4', 'images/img2_1785352255_8663.jpg', '', '', '', '', '', '', 'dfzgdfshgdfhjfhdfgdfdfg');

-- --------------------------------------------------------

--
-- Table structure for table `product_color_variants`
--

CREATE TABLE `product_color_variants` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variant_image` varchar(255) NOT NULL,
  `color_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_color_variants`
--

INSERT INTO `product_color_variants` (`id`, `product_id`, `variant_image`, `color_name`, `created_at`) VALUES
(2, 10, 'images/var_10_1785333615_1.jpg', 'brown', '2026-07-29 14:00:15'),
(3, 10, 'images/var_10_1785333615_2.webp', 'Coffee', '2026-07-29 14:00:15'),
(4, 11, 'images/var_11_1785346726_0.png', 'orange', '2026-07-29 17:38:46'),
(5, 11, 'images/var_11_1785346726_1.png', 'black', '2026-07-29 17:38:46'),
(6, 13, 'images/var_13_1785352256_0.jpg', 'Balck', '2026-07-29 19:10:56'),
(7, 13, 'images/var_13_1785352256_1.jpg', 'green', '2026-07-29 19:10:56'),
(8, 13, 'images/var_13_1785352256_2.jpg', 'purple', '2026-07-29 19:10:56');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT 5,
  `review_text` text DEFAULT NULL,
  `review_image` varchar(255) DEFAULT NULL,
  `points_earned` int(11) DEFAULT 10,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `order_id`, `product_id`, `rating`, `review_text`, `review_image`, `points_earned`, `created_at`) VALUES
(1, 1, 1, 5, 4, 'Good Quality and Fast Delivary', 'useruploads/review_1_1784452776.jpg', 10, '2026-07-19 09:19:36'),
(2, 2, 7, 3, 5, 'Good ', 'useruploads/review_2_1784478024.jpg', 10, '2026-07-19 16:20:24'),
(4, 1, 4, 5, 5, 'sei vai ', 'useruploads/review_1_1785346168.jpg', 10, '2026-07-29 17:29:28');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`) VALUES
(1, 'offer_text', '🎉 Welcome to StellarLuxury BD! 🚀 | 💎 Grab our exclusive 50% discount on first order! ✨');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(20) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `primary_address` text DEFAULT NULL,
  `secondary_address` text DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `points` int(11) DEFAULT 0,
  `membership_tier` varchar(50) DEFAULT 'New Member'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `phone`, `dob`, `primary_address`, `secondary_address`, `shipping_address`, `profile_image`, `points`, `membership_tier`) VALUES
(1, 'Hasan', 'hhhasannn.2003@gmail.com', '$2y$10$dhoq8u0g.T/13o9fs/d8yeLtiBIp5jAY3iCzXc4A6WuCqs5uJJ.Xu', '2026-07-14 19:43:39', '01780180810', '2003-06-08', 'Hirajheel Siddhirganj', 'Hirajheel 14 road', 'Hirajheel Siddhirganj 14 road', 'useruploads/user_1_1784360328.jpg', 2147483647, 'Reseller / Merchant'),
(2, 'Rabiul', 'mdrabiulhasan08062003@gmail.com', '$2y$10$COprZatP8YeAS7d9lbe8xO1Bw6.qWDdh8fuN1bgGQX0rciABKTO9S', '2026-07-19 11:41:44', '', '0000-00-00', '', '', '14 no Road Chittagongroad', NULL, 2147483647, 'New Member'),
(3, 'Imtiaz Ahmed', 'dragoononto@gmail.com', '$2y$10$cxmNSNKG0N6mbzowSrb5G.kScBmUayS6ivGgoLVLpLmstkVKnkxca', '2026-07-19 11:47:32', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(4, 'Noor', '2024000000040@seu.edu.bd', '$2y$10$1.HgWns6wN0EBv0GWvwVROlRhuCwFYNFrxZ1tvVOQkn8q2b/Irpw.', '2026-07-20 13:51:34', '01827837071', '2026-07-17', 'dhaka gulshan', '', 'Gaja khor', NULL, 1000000, 'VIP Customer');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_requests`
--
ALTER TABLE `payment_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_color_variants`
--
ALTER TABLE `product_color_variants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `payment_requests`
--
ALTER TABLE `payment_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `product_color_variants`
--
ALTER TABLE `product_color_variants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
