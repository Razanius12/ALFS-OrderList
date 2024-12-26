-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 25, 2024 at 06:18 PM
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
(3, 'gnjr4PRI', 'apriyanto222', 'Ganjar Apriyanto', 43, '6285624634849', '2024-11-28 09:10:03', '2024-12-19 03:25:08', NULL),
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
  `attachment_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id_order`, `project_manager_id`, `worker_id`, `order_name`, `description`, `start_date`, `finished_at`, `deadline`, `status`, `order_price`, `attachment_id`) VALUES
(128, 3, 15, 'Artisan Coffee Roasters Branding', 'Create logo for small-batch, local coffee roastery. Vintage-inspired design with modern touches. Incorporate coffee bean or brewing equipment subtly. Earthy color palette.', '2024-11-25 09:45:00', '2024-12-10 16:15:00', '2024-12-12', 'COMPLETED', 950, NULL),
(129, 3, 15, 'Sustainable Fashion Brand Logo', 'Design logo for eco-friendly clothing line. Represent sustainability, minimalism, and modern fashion. Use natural color scheme. Must convey ethical production values.', '2024-12-03 11:20:00', '2024-12-25 21:32:11', '2024-12-18', 'COMPLETED', 1100, NULL),
(130, 3, 15, 'Urban Vertical Farming Logo', 'Create logo for innovative urban agriculture company. Blend technology and nature. Geometric plant/building hybrid design. Clean, progressive visual language.', '2024-11-28 15:00:00', '2024-12-08 13:45:00', '2024-12-10', 'COMPLETED', 1250, NULL),
(132, 3, 15, 'Wellness Meditation App Logo', 'Design logo for mental health and meditation mobile application. Represent calm, balance, and modern wellness. Minimalist design with soft, soothing colors.', '2024-11-22 13:30:00', '2024-12-05 11:10:00', '2024-12-07', 'COMPLETED', 850, NULL),
(133, 3, 15, 'Electric Vehicle Charging Network', 'Create logo for regional EV charging infrastructure company. Represent innovation, sustainability, and technology. Use electric/energy-inspired design elements.', '2024-12-09 16:45:00', '2024-12-25 19:04:51', '2024-12-25', 'COMPLETED', 1600, NULL),
(134, 3, 15, 'Organic Skincare Brand Identity', 'Design logo for natural, organic skincare line. Emphasize purity, botanical ingredients. Soft, clean design with botanical subtle references.', '2024-11-20 09:00:00', '2024-12-02 14:20:00', '2024-12-05', 'COMPLETED', 1000, NULL),
(135, 3, 15, 'Local Artisan Cheese Company', 'Create logo for small-batch, handcrafted cheese producer. Represent traditional cheesemaking, local agriculture. Blend rustic and modern design elements.', '2024-12-11 11:50:00', NULL, '2024-12-27', 'PENDING', 1150, NULL),
(140, 3, 15, 'QuantumLeap Consulting Logo', 'Professional logo for technology consulting firm', '2024-12-08 11:45:00', '2024-12-25 21:19:55', '2024-12-25', 'COMPLETED', 400, NULL),
(141, 3, 15, 'Green Horizon Farms Logo', 'Agricultural logo representing sustainable farming', '2024-12-10 13:00:00', NULL, '2024-12-27', 'IN_PROGRESS', 375, NULL),
(142, 3, 15, 'NeuroPeak Performance Logo', 'Dynamic logo for mental health and wellness center', '2024-12-12 09:30:00', NULL, '2024-12-28', 'IN_PROGRESS', 425, NULL),
(143, 3, 15, 'StellarTech Robotics Logo', 'Futuristic logo for robotics and AI company', '2024-12-15 15:45:00', NULL, '2024-12-30', 'PENDING', 475, NULL),
(144, 3, 15, 'Harmony Holistic Wellness Logo', 'Serene logo for integrated health practice', '2024-12-18 10:00:00', NULL, '2024-12-31', 'PENDING', 350, NULL),
(145, 3, 15, 'Arctic Innovations Logo', 'Clean, minimalist logo for climate technology startup', '2024-12-20 14:00:00', NULL, '2025-01-05', 'PENDING', 400, NULL),
(146, 3, 15, 'Pulse Fitness Studios Logo', 'Modern, energetic logo for fitness and wellness brand', '2024-12-22 11:15:00', NULL, '2025-01-10', 'PENDING', 525, NULL),
(147, 3, 6, 'Prosporeus Mushrooms Logo', 'Logo design for gourmet mushroom farm with subtle \"spore\" highlight', '2024-12-01 09:00:00', '2024-12-14 16:30:00', '2024-12-15', 'COMPLETED', 450, NULL),
(148, 3, 6, 'EcoTech Innovations Logo', 'Minimalist logo for sustainable technology startup', '2024-12-02 10:30:00', NULL, '2024-12-20', 'IN_PROGRESS', 350, NULL),
(149, 3, 6, 'Urban Roots Cafe Branding', 'Modern logo design for farm-to-table coffee shop', '2024-12-05 14:15:00', NULL, '2024-12-22', 'PENDING', 500, NULL),
(150, 3, 6, 'QuantumLeap Consulting Logo', 'Professional logo for technology consulting firm', '2024-12-08 11:45:00', '2024-12-24 10:15:00', '2024-12-25', 'COMPLETED', 400, NULL),
(151, 3, 6, 'Green Horizon Farms Logo', 'Agricultural logo representing sustainable farming', '2024-12-10 13:00:00', NULL, '2024-12-27', 'PENDING', 375, NULL),
(152, 3, 6, 'NeuroPeak Performance Logo', 'Dynamic logo for mental health and wellness center', '2024-12-12 09:30:00', '2024-12-28 14:45:00', '2024-12-28', 'COMPLETED', 425, NULL),
(153, 3, 6, 'StellarTech Robotics Logo', 'Futuristic logo for robotics and AI company', '2024-12-15 15:45:00', NULL, '2024-12-30', 'IN_PROGRESS', 475, NULL),
(154, 3, 6, 'Harmony Holistic Wellness Logo', 'Serene logo for integrated health practice', '2024-12-18 10:00:00', '2024-12-31 11:30:00', '2024-12-31', 'COMPLETED', 350, NULL),
(155, 3, 6, 'Arctic Innovations Logo', 'Clean, minimalist logo for climate technology startup', '2024-12-20 14:00:00', NULL, '2025-01-05', 'PENDING', 400, NULL),
(156, 3, 6, 'Pulse Fitness Studios Logo', 'Modern, energetic logo for fitness and wellness brand', '2024-12-22 11:15:00', '2025-01-10 09:45:00', '2025-01-10', 'COMPLETED', 525, NULL),
(157, 3, 10, 'Woodland Whispers Brewery Logo', 'Rustic logo for craft brewery with nature themes', '2024-12-25 13:30:00', NULL, '2025-01-15', 'IN_PROGRESS', 450, NULL),
(158, 3, 10, 'Quantum Leap Educational Tech Logo', 'Innovative logo for online learning platform', '2024-12-27 09:45:00', NULL, '2025-01-20', 'PENDING', 375, NULL),
(159, 3, 10, 'Ocean Guardians Conservation Logo', 'Environmentally focused logo for marine protection', '2024-12-29 15:00:00', '2025-01-25 13:15:00', '2025-01-25', 'COMPLETED', 500, NULL),
(160, 3, 10, 'Mindful Moments Meditation Logo', 'Calming logo for meditation and wellness app', '2024-12-31 10:15:00', NULL, '2025-01-30', 'IN_PROGRESS', 350, NULL),
(161, 3, 10, 'Tech Horizon Innovations Logo', 'Forward-thinking logo for emerging tech company', '2025-01-02 14:30:00', '2025-02-05 10:00:00', '2025-02-05', 'COMPLETED', 425, NULL),
(162, 3, 10, 'Green Palette Organic Foods Logo', 'Fresh and natural logo for organic food brand', '2025-01-05 11:00:00', NULL, '2025-02-10', 'PENDING', 400, NULL),
(163, 3, 10, 'Cosmic Connections Astronomy Logo', 'Space-inspired logo for astronomy education center', '2025-01-07 09:15:00', '2025-02-15 15:45:00', '2025-02-15', 'COMPLETED', 475, NULL),
(164, 3, 10, 'Urban Jungle Landscaping Logo', 'Modern logo for urban gardening and design firm', '2025-01-10 13:45:00', NULL, '2025-02-20', 'IN_PROGRESS', 375, NULL),
(165, 3, 10, 'Wellness Wave Health Center Logo', 'Holistic health logo with fluid, dynamic design', '2025-01-12 15:30:00', '2025-02-25 11:30:00', '2025-02-25', 'COMPLETED', 525, NULL),
(166, 3, 10, 'Digital Nomad Collective Logo', 'Contemporary logo for remote work community', '2025-01-15 10:45:00', NULL, '2025-03-01', 'CANCELLED', 450, NULL),
(167, 3, 14, 'Prosporeus Mushrooms Logo', 'Logo design for gourmet mushroom farm with subtle \"spore\" highlight', '2024-12-01 09:00:00', '2024-12-14 16:30:00', '2024-12-15', 'COMPLETED', 450, NULL),
(168, 3, 14, 'EcoTech Innovations Logo', 'Minimalist logo for sustainable technology startup', '2024-12-02 10:30:00', NULL, '2024-12-20', 'IN_PROGRESS', 350, NULL),
(169, 3, 14, 'Urban Roots Cafe Branding', 'Modern logo design for farm-to-table coffee shop', '2024-12-05 14:15:00', NULL, '2024-12-22', 'PENDING', 500, NULL),
(170, 3, 14, 'QuantumLeap Consulting Logo', 'Professional logo for technology consulting firm', '2024-12-08 11:45:00', '2024-12-24 10:15:00', '2024-12-25', 'COMPLETED', 400, NULL),
(171, 3, 14, 'Green Horizon Farms Logo', 'Agricultural logo representing sustainable farming', '2024-12-10 13:00:00', NULL, '2024-12-27', 'PENDING', 375, NULL),
(172, 3, 14, 'NeuroPeak Performance Logo', 'Dynamic logo for mental health and wellness center', '2024-12-12 09:30:00', '2024-12-28 14:45:00', '2024-12-28', 'COMPLETED', 425, NULL),
(173, 3, 14, 'StellarTech Robotics Logo', 'Futuristic logo for robotics and AI company', '2024-12-15 15:45:00', NULL, '2024-12-30', 'IN_PROGRESS', 475, NULL),
(174, 3, 14, 'Harmony Holistic Wellness Logo', 'Serene logo for integrated health practice', '2024-12-18 10:00:00', '2024-12-31 11:30:00', '2024-12-31', 'COMPLETED', 350, NULL),
(175, 3, 14, 'Arctic Innovations Logo', 'Clean, minimalist logo for climate technology startup', '2024-12-20 14:00:00', NULL, '2025-01-05', 'PENDING', 400, NULL),
(176, 3, 14, 'Pulse Fitness Studios Logo', 'Modern, energetic logo for fitness and wellness brand', '2024-12-22 11:15:00', '2025-01-10 09:45:00', '2025-01-10', 'COMPLETED', 525, NULL),
(177, 3, 14, 'Woodland Whispers Brewery Logo', 'Rustic logo for craft brewery with nature themes', '2024-12-25 13:30:00', NULL, '2025-01-15', 'IN_PROGRESS', 450, NULL),
(178, 3, 14, 'Quantum Leap Educational Tech Logo', 'Innovative logo for online learning platform', '2024-12-27 09:45:00', NULL, '2025-01-20', 'PENDING', 375, NULL),
(179, 3, 14, 'Ocean Guardians Conservation Logo', 'Environmentally focused logo for marine protection', '2024-12-29 15:00:00', '2025-01-25 13:15:00', '2025-01-25', 'COMPLETED', 500, NULL),
(180, 3, 14, 'Mindful Moments Meditation Logo', 'Calming logo for meditation and wellness app', '2024-12-31 10:15:00', NULL, '2025-01-30', 'IN_PROGRESS', 350, NULL),
(181, 3, 14, 'Tech Horizon Innovations Logo', 'Forward-thinking logo for emerging tech company', '2025-01-02 14:30:00', '2025-02-05 10:00:00', '2025-02-05', 'COMPLETED', 425, NULL),
(182, 3, 14, 'Green Palette Organic Foods Logo', 'Fresh and natural logo for organic food brand', '2025-01-05 11:00:00', NULL, '2025-02-10', 'PENDING', 400, NULL),
(183, 3, 14, 'Cosmic Connections Astronomy Logo', 'Space-inspired logo for astronomy education center', '2025-01-07 09:15:00', '2025-02-15 15:45:00', '2025-02-15', 'COMPLETED', 475, NULL),
(184, 3, 14, 'Urban Jungle Landscaping Logo', 'Modern logo for urban gardening and design firm', '2025-01-10 13:45:00', NULL, '2025-02-20', 'IN_PROGRESS', 375, NULL),
(185, 3, 14, 'Wellness Wave Health Center Logo', 'Holistic health logo with fluid, dynamic design', '2025-01-12 15:30:00', '2025-02-25 11:30:00', '2025-02-25', 'COMPLETED', 525, NULL),
(186, 3, 14, 'Digital Nomad Collective Logo', 'Contemporary logo for remote work community', '2025-01-15 10:45:00', NULL, '2025-03-01', 'CANCELLED', 450, NULL);

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
(50, 3, 'admin', 'f5843035efb38f63af2341a9dd73cf420349a429adaabde5626474fc0c624c7e', 1737696783, '2024-12-25 05:33:03'),
(53, 15, 'worker', '668e90086de2f3c8d48147f443a85a985787b2bed0c5a4ac5a439154d0f6bcc9', 1737736920, '2024-12-25 16:42:00');

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
(6, 'razanius12', 'realgamer', 'Razan Muhammad Ihsan', 17, 'MALE', '6281238314426', 'AVAILABLE', NULL, '2024-11-26 07:14:02', '2024-12-19 02:16:46', ''),
(10, 'fauzanUber', 'fzfnfzfn', 'Muhammad Fauzan', 18, 'MALE', '6281234567878', 'AVAILABLE', NULL, '2024-11-29 10:01:30', '2024-12-24 18:15:06', ''),
(14, 'langs', 'yangaslinyanih', 'Erlangga', 13, 'MALE', '62895320087774', 'AVAILABLE', NULL, '2024-12-17 05:10:30', '2024-12-17 05:10:30', ''),
(15, 'vivi', 'prettiestgurls', 'Evelyna Cristina Ziovaj', 17, 'FEMALE', '6282257762471', 'TASKED', NULL, '2024-12-24 15:30:59', '2024-12-25 14:21:28', '');

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
  ADD KEY `attachment_id` (`attachment_id`) USING BTREE;

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
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id_attachment` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gmaps`
--
ALTER TABLE `gmaps`
  MODIFY `id_maps` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id_order` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=188;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id_position` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `workers`
--
ALTER TABLE `workers`
  MODIFY `id_worker` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

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
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`attachment_id`) REFERENCES `attachments` (`id_attachment`);

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
