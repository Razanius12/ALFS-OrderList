-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 28, 2024 at 06:21 PM
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id_admin`, `username`, `password`, `name_admin`, `id_position`, `phone_number`, `created_at`, `updated_at`, `profile_pic`) VALUES
(1, 'theMostPowerfulAdmin', 'realsheeesh', 'Powerful Admin', 41, '6281234567878', '2024-11-27 06:22:26', '2024-11-28 09:08:33', NULL),
(3, 'gnjr4PRI', 'apriyanto222', 'Ganjar Apriyanto', 43, '6285624634849', '2024-11-28 09:10:03', '2024-12-27 10:31:24', 'main/imgdata/profile/676e81fc8b16e_profile_pic.png'),
(6, 'OPMzzzz', 'zakiyesyes', 'Opik Muhammad Zaki', 40, '6281238314426', '2024-12-19 03:24:23', '2024-12-19 03:24:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE `attachments` (
  `id_attachment` int(11) NOT NULL,
  `atch1` varchar(255) DEFAULT NULL,
  `atch2` varchar(255) DEFAULT NULL,
  `atch3` varchar(255) DEFAULT NULL,
  `atch4` varchar(255) DEFAULT NULL,
  `atch5` varchar(255) DEFAULT NULL,
  `atch6` varchar(255) DEFAULT NULL,
  `atch7` varchar(255) DEFAULT NULL,
  `atch8` varchar(255) DEFAULT NULL,
  `atch9` varchar(255) DEFAULT NULL,
  `atch10` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attachments`
--

INSERT INTO `attachments` (`id_attachment`, `atch1`, `atch2`, `atch3`, `atch4`, `atch5`, `atch6`, `atch7`, `atch8`, `atch9`, `atch10`) VALUES
(13, 'ref_67702bec0350b1.11680174.png', 'ref_67702bec0388b1.37067231.jpeg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 'order_67702c056285f9.30712447.png', 'order_67702c0562b529.59726683.svg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 'ref_677030c473d131.72262434.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 'ref_677030ff328681.23535783.jpg', 'ref_677030ff32bc98.91991436.jpeg', 'ref_677030ff32f493.19517402.jpeg', 'ref_677030ff332277.22338845.jpeg', NULL, NULL, NULL, NULL, NULL, NULL),
(18, 'ref_6770321d1fb476.24616432.pdf', 'ref_6770321d1fed42.64306189.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 'ref_677032cfaacad3.81915042.jpeg', 'ref_677032cfab1292.14059193.jpeg', 'ref_677032cfac0b73.59007356.jpeg', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 'ref_677033250dda44.91610599.jpg', 'ref_677033250e2562.10674083.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 'order_6770335709cb91.15817242.png', 'order_6770335709fec4.58908844.svg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

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
  `finished_at` datetime DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `status` enum('PENDING','IN_PROGRESS','COMPLETED','CANCELLED') DEFAULT 'PENDING',
  `order_price` int(255) DEFAULT NULL,
  `references_id` int(11) DEFAULT NULL,
  `attach_result_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id_order`, `project_manager_id`, `worker_id`, `order_name`, `description`, `start_date`, `finished_at`, `deadline`, `status`, `order_price`, `references_id`, `attach_result_id`) VALUES
(224, 3, 6, 'Big Pine Canvas', 'custom heavy duty fabric creations (bags, pillows, marine canvas, repairs to same.\\r\\n\\r\\nIndustry\\r\\nHome Furnishing\\r\\n\\r\\nOther notes\\r\\nI am envisioning a Pine Tree in dark green with a palm tree in front in a Bahama blue. see the rough sketch I did.`', '2024-12-28 23:48:00', '2024-12-28 23:49:09', '2024-12-31', 'COMPLETED', 14, 13, 14),
(226, 3, 10, 'Crestone Apparel', 'makes gender neutral technical UPF clothing for infants and other outdoor accessories geared towards families trying to get outdoors/into the mountains with young children\\r\\n\\r\\nIndustry\\r\\nFashion\\r\\n\\r\\nOther notes\\r\\nOur brand aesthetic is patagonia meets topo designs for babies with a side of fun color like Cotopaxi. We plan to eventually branch into adult apparel if things go well. Crestone Peak in Colorado is where the name is derived from. The sketch is a crappy version of just one idea of a crescent moon plus sangre de cristo mountain range.', '2024-12-29 00:07:00', NULL, '2025-01-01', 'PENDING', 21, 16, NULL),
(227, 3, 6, 'Harris Creek Outfitters est 2021', 'Slogan\\r\\nNo swimming allowed\\r\\n\\r\\nDescription of the organization and its target audience\\r\\n\\r\\nWhite water Raft company\\r\\n\\r\\nIndustry\\r\\nSport\\r\\n\\r\\nOther notes\\r\\nI have a picture I want centralized in the logo. But I want it rendered as more of a simple sketch', '2024-12-29 00:09:00', NULL, '2025-01-01', 'IN_PROGRESS', 34, 17, NULL),
(228, 3, 6, 'Paloma Plumbing', 'Plumbing service and repair residential and commercial\\r\\n\\r\\nIndustry\\r\\nConstruction\\r\\n\\r\\nOther notes\\r\\nWould like to incorporate olive branch possibly. Attached are a couple sketches I threw together (don\\\'t laugh, lol). Like the color scheme of navy blue and peach (kinda get a sunset type vibe)', '2024-12-29 00:14:00', NULL, '2025-01-01', 'PENDING', 18, 18, NULL),
(229, 3, 10, 'Meadow Green', 'Snow plow and lawn care services company\\r\\n\\r\\nIndustry\\r\\nLandscaping\\r\\n\\r\\nOther notes\\r\\nLooking for something mainly geared to snowplowing but they also do landscaping in the off season see sketches for client ideas', '2024-12-29 00:15:00', '2024-12-29 00:20:23', '2025-01-01', 'COMPLETED', 26, 19, 21),
(230, 3, 6, 'Jeunesse de Faoug', 'We are a group of young people from our village who organize parties during the year. We want to make a fairly casual logo for t-shirts.\\r\\n\\r\\nIndustry\\r\\nCommunity & Non-Profit\\r\\n\\r\\nOther notes\\r\\nOur village badge is the peacock so we want a logo that features a peacock. This is an image that pretty much describes the style we want (minimalist).', '2024-12-29 00:18:00', NULL, '2025-01-01', 'PENDING', 69, 20, NULL);

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
(57, 3, 'admin', '6c8482487ddfc39e908097b3b77e847dac1394f880d6fd5b55ed8c796c6949d3', 1737885813, '2024-12-27 10:03:33'),
(58, 15, 'worker', 'ea6e8d6b3e844aa59d21a469de1e9ff4e008206fd6aa95bab4743528d763be7c', 1737886229, '2024-12-27 10:10:29'),
(61, 6, 'worker', 'f2f34373de3a4ff1af708f0232de46e2d0a4c21c9ce31b7cba143924b0b9ff4c', 1737998470, '2024-12-28 17:21:09');

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `profile_pic` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workers`
--

INSERT INTO `workers` (`id_worker`, `username`, `password`, `name_worker`, `id_position`, `gender_worker`, `phone_number`, `availability_status`, `assigned_order_id`, `created_at`, `updated_at`, `profile_pic`) VALUES
(6, 'razanius12', 'realgamer', 'Razan Muhammad Ihsan', 17, 'MALE', '6281238314426', 'TASKED', 227, '2024-11-26 07:14:02', '2024-12-28 17:16:16', 'main/imgdata/profile/676e7c52b35c4_profile_pic.png'),
(10, 'fauzanUber', 'fzfnfzfn', 'Muhammad Fauzan', 18, 'MALE', '6281234567878', 'AVAILABLE', 229, '2024-11-29 10:01:30', '2024-12-28 17:20:44', ''),
(14, 'langs', 'yangaslinyanih', 'Erlangga', 13, 'MALE', '62895320087774', 'AVAILABLE', NULL, '2024-12-17 05:10:30', '2024-12-17 05:10:30', ''),
(15, 'vivi', 'prettiestgurls', 'Evelyna Cristina Ziovaj', 17, 'FEMALE', '6282257762471', 'TASKED', NULL, '2024-12-24 15:30:59', '2024-12-27 10:30:58', 'main/imgdata/profile/676e81e2b23f2_profile_pic.png');

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
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id_attachment`);

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
  ADD KEY `worker_id` (`worker_id`) USING BTREE,
  ADD KEY `references_id` (`references_id`),
  ADD KEY `attach_result_id` (`attach_result_id`);

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
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id_attachment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `gmaps`
--
ALTER TABLE `gmaps`
  MODIFY `id_maps` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id_order` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=231;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id_position` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `workers`
--
ALTER TABLE `workers`
  MODIFY `id_worker` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`project_manager_id`) REFERENCES `admins` (`id_admin`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`references_id`) REFERENCES `attachments` (`id_attachment`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`attach_result_id`) REFERENCES `attachments` (`id_attachment`) ON DELETE SET NULL;

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
