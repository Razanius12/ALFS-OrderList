-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2024 at 04:52 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_alf_solution`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name_admin` varchar(255) NOT NULL,
  `id_position` int(11) NOT NULL,
  `phone_number` varchar(16) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id_admin`, `username`, `password`, `name_admin`, `id_position`, `phone_number`, `created_at`, `updated_at`) VALUES
(1, 'theMostPowerfulAdmin', 'realsheeesh', 'Powerful Admin', 41, '6281234567878', '2024-11-27 06:22:26', '2024-11-28 09:08:33'),
(3, 'gnjr4PRI', 'apriyanto222', 'Ganjar Apriyanto', 43, '6285624634849', '2024-11-28 09:10:03', '2024-12-19 03:25:08'),
(6, 'OPMzzzz', 'zakiyesyes', 'Opik Muhammad Zaki', 40, '6281238314426', '2024-12-19 03:24:23', '2024-12-19 03:24:23');

-- --------------------------------------------------------

--
-- Table structure for table `gmaps`
--

CREATE TABLE `gmaps` (
  `id_maps` int(11) NOT NULL,
  `name_city_district` varchar(32) NOT NULL,
  `link_embed` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gmaps`
--

INSERT INTO `gmaps` (`id_maps`, `name_city_district`, `link_embed`) VALUES
(4, 'Bandung', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.739008282675!2d107.71778057414153!3d-6.921771567747041!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68dcd4f3fa4d39%3A0xce64b9d681e418e3!2sAlfsolution%20Office!5e0!3m2!1sen!2sid!4v1733919010044!5m2!1sen!2sid\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>'),
(5, 'Garut', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4707.635716614528!2d107.94893594479501!3d-7.16823935580455!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68b11b8104ff0b%3A0x330b1a00f1a74129!2sKantor%20ALF%20Solution%20Cabang%20Garut!5e0!3m2!1sen!2sid!4v1733715843772!5m2!1sen!2sid\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id_order` int(11) NOT NULL,
  `project_manager_id` int(11) DEFAULT NULL,
  `worker_id` int(11) DEFAULT NULL,
  `order_name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `status` enum('PENDING','IN_PROGRESS','COMPLETED','CANCELLED') DEFAULT 'PENDING',
  `order_price` int(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id_order`, `project_manager_id`, `worker_id`, `order_name`, `description`, `start_date`, `status`, `order_price`) VALUES
(19, 1, NULL, 'atest1', NULL, '2024-12-17 10:38:00', 'COMPLETED', 40),
(20, 1, NULL, 'atest2', NULL, '2024-12-18 10:39:00', 'COMPLETED', 50),
(21, 1, NULL, 'atest3', NULL, '2024-12-19 10:39:00', 'COMPLETED', 30),
(22, 1, NULL, 'atest4', NULL, '2024-12-20 10:41:00', 'COMPLETED', 60),
(23, 1, NULL, 'atest5', NULL, '2024-12-21 10:41:00', 'COMPLETED', 45),
(24, 1, NULL, 'atest6', NULL, '2024-12-22 10:41:00', 'COMPLETED', 55),
(25, 1, NULL, 'atest7', NULL, '2024-12-23 10:41:00', 'COMPLETED', 35),
(26, 1, NULL, 'atest8', NULL, '2024-12-24 10:41:00', 'COMPLETED', 65),
(27, 1, NULL, 'atest9', NULL, '2024-12-25 10:41:00', 'COMPLETED', 40),
(28, 1, NULL, 'atest10', NULL, '2024-12-26 10:41:00', 'COMPLETED', 55),
(29, 1, NULL, 'atest11', NULL, '2024-12-27 10:41:00', 'COMPLETED', 45),
(30, 1, NULL, 'atest12', NULL, '2024-12-28 10:41:00', 'COMPLETED', 60),
(31, 1, NULL, 'atest13', NULL, '2024-12-29 10:41:00', 'COMPLETED', 50),
(32, 1, NULL, 'atest14', NULL, '2024-12-30 10:41:00', 'COMPLETED', 70),
(33, 1, NULL, 'atest15', NULL, '2024-12-31 10:41:00', 'COMPLETED', 40),
(34, 1, NULL, 'atest16', NULL, '2025-01-01 10:41:00', 'COMPLETED', 55),
(35, 1, NULL, 'atest17', NULL, '2025-01-02 10:41:00', 'COMPLETED', 45),
(36, 1, NULL, 'atest18', NULL, '2025-01-03 10:41:00', 'COMPLETED', 65),
(37, 1, NULL, 'atest19', NULL, '2025-01-04 10:41:00', 'COMPLETED', 35),
(38, 1, NULL, 'atest20', NULL, '2025-01-05 10:41:00', 'COMPLETED', 60),
(39, 1, NULL, 'atest21', NULL, '2025-01-06 10:41:00', 'COMPLETED', 50),
(40, 1, NULL, 'atest22', NULL, '2025-01-07 10:41:00', 'COMPLETED', 45),
(41, 1, NULL, 'atest23', NULL, '2025-01-08 10:41:00', 'COMPLETED', 55),
(42, 1, NULL, 'atest24', NULL, '2025-01-09 10:41:00', 'COMPLETED', 40),
(43, 1, NULL, 'atest25', NULL, '2025-01-10 10:41:00', 'COMPLETED', 70),
(44, 1, NULL, 'atest26', NULL, '2025-01-11 10:41:00', 'COMPLETED', 45),
(45, 1, NULL, 'atest27', NULL, '2025-01-12 10:41:00', 'COMPLETED', 60),
(46, 1, NULL, 'atest28', NULL, '2025-01-13 10:41:00', 'COMPLETED', 50),
(47, 1, NULL, 'atest29', NULL, '2025-01-14 10:41:00', 'COMPLETED', 55),
(48, 1, NULL, 'atest30', NULL, '2025-01-15 10:41:00', 'COMPLETED', 40),
(49, 1, NULL, 'atest31', NULL, '2025-01-16 10:41:00', 'COMPLETED', 65),
(50, 1, NULL, 'atest32', NULL, '2025-01-17 10:41:00', 'COMPLETED', 45),
(51, 1, NULL, 'atest33', NULL, '2025-01-18 10:41:00', 'COMPLETED', 60),
(52, 1, NULL, 'atest34', NULL, '2025-01-19 10:41:00', 'COMPLETED', 50),
(53, 1, NULL, 'atest35', NULL, '2025-01-20 10:41:00', 'COMPLETED', 55),
(54, 1, NULL, 'atest36', NULL, '2025-01-21 10:41:00', 'COMPLETED', 40),
(55, 1, NULL, 'atest37', NULL, '2025-01-22 10:41:00', 'COMPLETED', 70),
(56, 1, NULL, 'atest38', NULL, '2025-01-23 10:41:00', 'COMPLETED', 45),
(57, 1, NULL, 'atest39', NULL, '2025-01-24 10:41:00', 'COMPLETED', 60),
(58, 1, NULL, 'atest40', NULL, '2025-01-25 10:41:00', 'COMPLETED', 50),
(59, 1, NULL, 'atest41', NULL, '2025-01-26 10:41:00', 'COMPLETED', 55),
(60, 1, NULL, 'atest42', NULL, '2025-01-27 10:41:00', 'COMPLETED', 40),
(61, 1, NULL, 'atest43', NULL, '2025-01-28 10:41:00', 'COMPLETED', 65),
(62, 1, NULL, 'atest44', NULL, '2025-01-29 10:41:00', 'COMPLETED', 45),
(63, 1, NULL, 'atest45', NULL, '2025-01-30 10:41:00', 'COMPLETED', 60),
(64, 1, NULL, 'atest46', NULL, '2025-01-31 10:41:00', 'COMPLETED', 50),
(79, 3, 6, 'DA - 4085197', 'Logo Design Brief\\r\\nLooking for a High End General Contractor Company logo for a new startup in Florida, USA. I think a graphic with a shell or something like that might look nice too. Thank you for your help!\\r\\n\\r\\nLogo Text\\r\\nShell Coast Construction LLC', '2025-02-01 09:31:00', 'PENDING', 10),
(80, 3, 10, 'DA - 4084837', 'Logo Design Brief\r\nLooking for a clean and simple logo for my new gourmet mushroom farm, located in Texas.\r\n\"Prosporeus Mushrooms\"\r\nGourmet mushrooms are my business.\r\nIm looking for the \"Prosporeus\" to have a subtle standout on the \"spore\" section. It should look like one word, but be able to see the word \"spore\" inside the entire word \"Prosporeus \". Something simple and clean with a mushroom influence. Maybe Texas influence. Not looking for a colorful logo. 3 solid colors maximum.\r\n\r\nTarget Market(s)\r\nRestaurants, general public\r\n\r\nIndustry/Entity Type\r\nMushroom Cultivation and Sales\r\n\r\nLogo Text\r\nProsporeus Mushrooms\r\n\r\nMust have\r\nSlight highlight to \"spore\" inside \"Prosporeus \". Mushroom influence\r\nNice to have\r\nSimple and clean. Mushroom design integrated into logo. Not a gimmicky Alice in wounderland mushroom... more interested in edible gourmet mushrooms like oyster, morel, chestnut. Maybe even a spore print design.\r\nShould not have\r\nNo fades or graduated colors. No more than 3 colors total', '2025-02-02 09:32:00', 'IN_PROGRESS', 20),
(81, 3, NULL, 'DA - 4084880', 'Logo Design Brief\r\nI need a logo design for a new natural and holistic cosmetic brand. Our cosmetics are designed not only to have an impact on skin but also to have an impact on mental health.\r\nAll of our bottles are amber glass, and we will need a second project for the label design.\r\nThe design should comunicate calm and hope\r\n\r\nLogo Text\r\nHealing', '2025-02-03 10:08:00', 'CANCELLED', 8),
(82, 3, NULL, 'DA - 4084742', 'Logo Design Brief\r\nMy brother and I need a logo for our new real estate company. I like some of the examples that were used in your teaser email including a lion/tiger/eagle and scrolling. We both like astronomy, are open to any design, we place extreme value in our last name \"Cassell\". We like the idea of \"CB\" or \"CBV interlocking letters as part of the emblem.\r\n\r\nTarget Market(s)\r\nReal Estate in the Piney Woods of East Texas\r\n\r\nIndustry/Entity Type\r\nReal Estate\r\n\r\nLogo Text\r\nCassell Brothers Ventures\r\n\r\nMust have\r\nCassell Brother Ventures or CBV\r\nNice to have\r\nSome sort of design that would fit and wear good on a hat or pullover shit or polo shirt', '2025-02-04 10:09:00', 'COMPLETED', 13);

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `id_position` int(11) NOT NULL,
  `position_name` varchar(32) NOT NULL,
  `department` enum('ADMIN','WORKER') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`id_position`, `position_name`, `department`, `created_at`) VALUES
(13, 'Content Creator', 'WORKER', '2024-11-24 05:01:22'),
(17, 'Designer', 'WORKER', '2024-11-24 07:40:15'),
(18, 'Live Host', 'WORKER', '2024-11-24 07:42:36'),
(40, 'Project Manager', 'ADMIN', '2024-11-27 05:03:40'),
(41, 'CEO', 'ADMIN', '2024-11-27 05:03:48'),
(43, 'CHRO', 'ADMIN', '2024-12-19 03:24:51');

-- --------------------------------------------------------

--
-- Table structure for table `remember_tokens`
--

CREATE TABLE `remember_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_type` enum('worker','admin') NOT NULL,
  `token` varchar(64) NOT NULL,
  `expiry` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `remember_tokens`
--

INSERT INTO `remember_tokens` (`id`, `user_id`, `user_type`, `token`, `expiry`, `created_at`) VALUES
(9, 11, 'worker', '30b878188892d6f50d6096ecf9355007df76cc8f30539de139067a00133e21c3', 1736901461, '2024-12-16 00:34:41'),
(19, 1, 'admin', '384fbc072c089f0422aa0a13b80a3560ebe1cec77c8aed61e3897df8fd66d061', 1737011590, '2024-12-17 04:17:05'),
(36, 10, 'worker', '56f5a5684bb94b3b980ce616677ec96cc78eecdcb76227c20a55425e355d586f', 1737169220, '2024-12-19 02:36:26'),
(37, 3, 'admin', '3c7c370824b85d4135f7fbb040b83e137bf65cd08bd2423a00a8cf253a6119fe', 1737171251, '2024-12-19 03:00:20'),
(38, 6, 'worker', '7f5659577c4ec2b75064dddcf02006efdfc9b1f37c160522b19effaefa163ff4', 1737171452, '2024-12-19 03:34:11');

-- --------------------------------------------------------

--
-- Table structure for table `workers`
--

CREATE TABLE `workers` (
  `id_worker` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name_worker` varchar(255) NOT NULL,
  `id_position` int(11) NOT NULL,
  `gender_worker` enum('MALE','FEMALE','OTHER') NOT NULL,
  `phone_number` varchar(16) NOT NULL,
  `availability_status` enum('AVAILABLE','TASKED') NOT NULL DEFAULT 'AVAILABLE',
  `assigned_order_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workers`
--

INSERT INTO `workers` (`id_worker`, `username`, `password`, `name_worker`, `id_position`, `gender_worker`, `phone_number`, `availability_status`, `assigned_order_id`, `created_at`, `updated_at`) VALUES
(6, 'razanius12', 'realgamer', 'Razan Muhammad Ihsan', 17, 'MALE', '6281238314426', 'AVAILABLE', NULL, '2024-11-26 07:14:02', '2024-12-19 02:16:46'),
(10, 'fauzanUber', 'fzfnfzfn', 'Muhammad Fauzan', 18, 'MALE', '6281234567878', 'TASKED', 80, '2024-11-29 10:01:30', '2024-12-19 03:03:24'),
(14, 'langs', 'yangaslinyanih', 'Erlangga', 13, 'MALE', '62895320087774', 'AVAILABLE', NULL, '2024-12-17 05:10:30', '2024-12-17 05:10:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `id_position` (`id_position`);

--
-- Indexes for table `gmaps`
--
ALTER TABLE `gmaps`
  ADD PRIMARY KEY (`id_maps`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id_order`),
  ADD KEY `project_manager_id` (`project_manager_id`),
  ADD KEY `worker_id` (`worker_id`) USING BTREE;

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id_position`);

--
-- Indexes for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- Indexes for table `workers`
--
ALTER TABLE `workers`
  ADD PRIMARY KEY (`id_worker`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `id_position` (`id_position`),
  ADD KEY `assigned_order_id` (`assigned_order_id`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `gmaps`
--
ALTER TABLE `gmaps`
  MODIFY `id_maps` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id_order` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id_position` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `workers`
--
ALTER TABLE `workers`
  MODIFY `id_worker` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`id_position`) REFERENCES `positions` (`id_position`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_worker` FOREIGN KEY (`worker_id`) REFERENCES `workers` (`id_worker`),
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`project_manager_id`) REFERENCES `admins` (`id_admin`);

--
-- Constraints for table `workers`
--
ALTER TABLE `workers`
  ADD CONSTRAINT `fk_worker_assigned_order` FOREIGN KEY (`assigned_order_id`) REFERENCES `orders` (`id_order`) ON DELETE SET NULL,
  ADD CONSTRAINT `workers_ibfk_1` FOREIGN KEY (`id_position`) REFERENCES `positions` (`id_position`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
