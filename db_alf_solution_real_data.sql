-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 29, 2024 at 08:20 AM
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
(21, 'order_6770335709cb91.15817242.png', 'order_6770335709fec4.58908844.svg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 'order_6770c29e1ca6a3.81566875.png', 'order_6770c29e1cdef7.79649361.svg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 'ref_6770c3575441a0.91201977.jpeg', 'ref_6770c357547f65.48550926.jpeg', 'ref_6770c35754b0b5.15745182.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 'order_6770c36e45fdc7.86433558.png', 'order_6770c36e46b036.26063389.svg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 'order_6770d5bd040051.83632287.png', 'order_6770d5bd044b32.61669477.svg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 'ref_6770d71c9aae85.12953951.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(27, 'ref_6770d7dc4c9923.29188066.jpeg', 'ref_6770d7dc4cc766.33541409.png', 'ref_6770d7dc4cfcf5.10076529.png', 'ref_6770d7dc4d3220.85820694.jpeg', 'ref_6770d7dc4d52c0.73218942.jpg', 'ref_6770d7dc4d7444.77053256.jpg', NULL, NULL, NULL, NULL),
(28, 'ref_6770d879c51160.55739071.jpg', 'ref_6770d879c53f63.94784901.png', 'ref_6770d879c583d5.76137249.png', 'ref_6770d879c5b5e8.85954098.png', 'ref_6770d879c5ecc1.88471371.jpeg', NULL, NULL, NULL, NULL, NULL),
(29, 'ref_6770d8e652e631.56150265.jpg', 'ref_6770d8e6531b31.97089611.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(30, 'ref_6770d99ecee126.96017594.png', 'ref_6770d99ecf2184.69145476.jpg', 'ref_6770d99ecf5844.65257791.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(31, 'ref_6770e7a6ee8dc1.47682490.png', 'ref_6770e7a704e5d9.65083155.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(32, 'order_6770e7e03df527.45512810.png', 'order_6770e7e03e2245.85101759.svg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(33, 'order_6770e962b195d6.97673574.png', 'order_6770e962b1f686.78030982.svg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(34, 'order_6770e9ce3fac16.73791040.png', 'order_6770e9ce40de95.73286410.png', 'order_6770e9ce415bd9.14163172.svg', 'order_6770e9ce41b6d2.61020311.svg', NULL, NULL, NULL, NULL, NULL, NULL),
(35, 'order_6770e9fddba389.85124903.png', 'order_6770e9fddbd445.51373214.svg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(36, 'order_6770ea183abb99.80568756.png', 'order_6770ea183ae906.49621256.svg', 'order_6770ea183e3525.69876792.png', 'order_6770ea183e6ed3.39613492.svg', NULL, NULL, NULL, NULL, NULL, NULL),
(37, 'order_6770ea2a8c3a93.31481563.png', 'order_6770ea2a8c6479.35130294.png', 'order_6770ea2a8c9526.67282584.svg', 'order_6770ea2a924f16.78669786.svg', NULL, NULL, NULL, NULL, NULL, NULL),
(38, 'order_6770ea418d82b2.64108666.png', 'order_6770ea418da662.57165465.svg', 'order_6770ea4193f337.18353334.png', 'order_6770ea41945114.38391620.svg', NULL, NULL, NULL, NULL, NULL, NULL),
(39, 'order_6770ea7d188a40.83240144.svg', 'order_6770ea7d18c641.46155929.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(40, 'ref_6770eb3e7f0ae8.63557057.png', 'ref_6770eb3e7f2c25.70738757.png', 'ref_6770eb3e7f63c1.80640284.jpg', 'ref_6770eb3e7f94a3.94109986.jpg', 'ref_6770eb3e7fc6b4.11272344.jpg', NULL, NULL, NULL, NULL, NULL),
(41, 'ref_6770eb86defc85.74935029.png', 'ref_6770eb86df34e0.36862293.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(42, 'order_6770ebb954e031.06000917.png', 'order_6770ebb9552e23.49630803.png', 'order_6770ebb95570e0.13622393.svg', 'order_6770ebb955aa22.55032038.svg', NULL, NULL, NULL, NULL, NULL, NULL),
(43, 'order_6770ebc349bf14.81065741.png', 'order_6770ebc349e558.02719136.png', 'order_6770ebc34a1987.70885958.svg', 'order_6770ebc34b08e1.48083266.svg', NULL, NULL, NULL, NULL, NULL, NULL),
(44, 'ref_6770ec1b2cc7f5.11109821.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(45, 'order_6770eeb5de9183.36690182.png', 'order_6770eeb5deca55.70830726.png', 'order_6770eeb5defea4.87966203.svg', 'order_6770eeb5df3db3.20504783.svg', NULL, NULL, NULL, NULL, NULL, NULL),
(46, 'order_6770ef9a3cd198.23083223.png', 'order_6770ef9a3d0e78.52184263.png', 'order_6770ef9a3d5414.80147168.svg', 'order_6770ef9a3d8939.16375380.svg', NULL, NULL, NULL, NULL, NULL, NULL),
(47, 'ref_6770f03e797ba2.98243134.jpg', 'ref_6770f03e79ab36.70737200.jpg', 'ref_6770f03e79dba5.23507275.jpg', 'ref_6770f03e7a0554.77945974.jpg', NULL, NULL, NULL, NULL, NULL, NULL),
(48, 'order_6770f04e3a36f3.87351601.png', 'order_6770f04e3ace23.98716750.png', 'order_6770f04e3b7930.09610955.svg', 'order_6770f04e3bc6a1.34440847.svg', NULL, NULL, NULL, NULL, NULL, NULL),
(49, 'ref_6770f13e26d1d5.64480361.webp', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(50, 'ref_6770f1a149b110.55209962.jpg', 'ref_6770f1a149e6a9.15403924.jpg', 'ref_6770f1a14a0ee2.63268929.jpg', 'ref_6770f1a14a42f5.52764141.jpg', NULL, NULL, NULL, NULL, NULL, NULL),
(51, 'ref_6770f2eacfb697.85076888.jpg', 'ref_6770f2ead005c3.39208632.webp', 'ref_6770f2ead05bf9.29439164.jpg', 'ref_6770f2ead0a632.37841999.jpg', 'ref_6770f2ead0fcc0.83337290.jpg', 'ref_6770f2ead5fb36.07372518.jpg', 'ref_6770f2ead62316.68506109.jpg', 'ref_6770f2ead642a2.72671500.png', 'ref_6770f2ead9d239.95445391.PNG', 'ref_6770f2eada07f5.48769212.png'),
(52, 'order_6770f39550fec0.24479908.png', 'order_6770f395513292.93217095.svg', 'order_6770f395516cf1.20810537.png', 'order_6770f39551a185.91321557.svg', NULL, NULL, NULL, NULL, NULL, NULL),
(53, 'order_6770f3a5d84327.36042494.png', 'order_6770f3a5d8e909.81897278.png', 'order_6770f3a5d92e98.82135465.svg', 'order_6770f3a5d95e38.29412412.svg', NULL, NULL, NULL, NULL, NULL, NULL),
(54, 'order_6770f3cbb0b609.59387349.png', 'order_6770f3cbb0e825.97034543.png', 'order_6770f3cbb13077.13644487.svg', 'order_6770f3cbb17631.94455947.svg', NULL, NULL, NULL, NULL, NULL, NULL),
(55, 'ref_6770f45280a255.20917075.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(56, 'order_6770f4789ae198.05196435.png', 'order_6770f4789b0c58.08141284.png', 'order_6770f4789b35c9.21973248.svg', 'order_6770f4789b6942.83439741.svg', NULL, NULL, NULL, NULL, NULL, NULL),
(57, 'order_6770f48ba51512.64484916.png', 'order_6770f48ba53cd5.09667597.png', 'order_6770f48ba56863.41020535.svg', 'order_6770f48ba595d8.55812297.svg', NULL, NULL, NULL, NULL, NULL, NULL),
(58, 'order_6770f6578988b7.84253236.png', 'order_6770f65789c083.13715636.png', 'order_6770f65789ead6.89250360.svg', 'order_6770f6578a2c69.52694876.svg', NULL, NULL, NULL, NULL, NULL, NULL);

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
(224, 3, 6, 'Big Pine Canvas', 'custom heavy duty fabric creations (bags, pillows, marine canvas, repairs to same.\\r\\n\\r\\nIndustry\\r\\nHome Furnishing\\r\\n\\r\\nOther notes\\r\\nI am envisioning a Pine Tree in dark green with a palm tree in front in a Bahama blue. see the rough sketch I did.`', '2024-12-01 10:15:00', '2024-12-01 13:45:00', '2024-12-03', 'COMPLETED', 14, 13, 14),
(226, 3, 10, 'Crestone Apparel', 'makes gender neutral technical UPF clothing for infants and other outdoor accessories geared towards families trying to get outdoors/into the mountains with young children\\r\\n\\r\\nIndustry\\r\\nFashion\\r\\n\\r\\nOther notes\\r\\nOur brand aesthetic is patagonia meets topo designs for babies with a side of fun color like Cotopaxi. We plan to eventually branch into adult apparel if things go well. Crestone Peak in Colorado is where the name is derived from. The sketch is a crappy version of just one idea of a crescent moon plus sangre de cristo mountain range.', '2024-12-01 14:30:00', '2024-12-01 17:45:00', '2024-12-03', 'COMPLETED', 21, 16, 32),
(227, 3, 6, 'Harris Creek Outfitters est 2021', 'Slogan\\r\\nNo swimming allowed\\r\\n\\r\\nDescription of the organization and its target audience\\r\\n\\r\\nWhite water Raft company\\r\\n\\r\\nIndustry\\r\\nSport\\r\\n\\r\\nOther notes\\r\\nI have a picture I want centralized in the logo. But I want it rendered as more of a simple sketch', '2024-12-04 09:20:00', '2024-12-04 13:15:00', '2024-12-06', 'COMPLETED', 34, 17, 22),
(228, 3, 6, 'Paloma Plumbing', 'Plumbing service and repair residential and commercial\\r\\n\\r\\nIndustry\\r\\nConstruction\\r\\n\\r\\nOther notes\\r\\nWould like to incorporate olive branch possibly. Attached are a couple sketches I threw together (don\\\'t laugh, lol). Like the color scheme of navy blue and peach (kinda get a sunset type vibe)', '2024-12-04 11:00:00', '2024-12-04 14:30:00', '2024-12-06', 'COMPLETED', 18, 18, 25),
(229, 3, 10, 'Meadow Green', 'Snow plow and lawn care services company\\r\\n\\r\\nIndustry\\r\\nLandscaping\\r\\n\\r\\nOther notes\\r\\nLooking for something mainly geared to snowplowing but they also do landscaping in the off season see sketches for client ideas', '2024-12-04 13:45:00', '2024-12-04 16:15:00', '2024-12-06', 'COMPLETED', 26, 19, 21),
(230, 3, 6, 'Jeunesse de Faoug', 'We are a group of young people from our village who organize parties during the year. We want to make a fairly casual logo for t-shirts.\\r\\n\\r\\nIndustry\\r\\nCommunity & Non-Profit\\r\\n\\r\\nOther notes\\r\\nOur village badge is the peacock so we want a logo that features a peacock. This is an image that pretty much describes the style we want (minimalist).', '2024-12-07 10:30:00', '2024-12-07 14:45:00', '2024-12-09', 'COMPLETED', 69, 20, 35),
(231, 3, 6, 'Mythikos', 'Travel services and planning\\r\\n\\r\\nIndustry\\r\\nTravel & Hotel\\r\\n\\r\\nOther notes\\r\\nWould like to base the design on the picture attached , communicating our focus on ancient history travel planning', '2024-12-07 15:20:00', '2024-12-07 19:45:00', '2024-12-09', 'COMPLETED', 37, 23, 24),
(232, 3, 10, 'Produck Goodz', 'Slogan\\r\\ngoodz you can bet on\\r\\n\\r\\nIndustry\\r\\ne-commerce\\r\\n\\r\\nWho are your competitors? (so we can see the logo designs of theirs) (optional)\\r\\nother e-commerce wholesalers\\r\\n\\r\\nPlease write down a few words about the history of the business. (optional)\\r\\nI just opened this llc. so i have no history but intend to make history with this llc. so my company name is produck goodz. It\\\'s for my online business. selling products on amazon.\\r\\n\\r\\nDo you have any preferred color scheme? (optional)\\r\\n1st choice - black and gold\\r\\nsecondary choices - red, black, white or red, white, blue or grey\\r\\n\\r\\nFor color i like black and gold or bright colors like red, white or blue.\\r\\n\\r\\nI generally have an idea of a duck similar to scrooge mcduck, leaning on the company name flipping a coin whinking.\\r\\n\\r\\nMy idea is somewhat like scrooge mcduck but i want a duck looking successful faced sideways over the company name winking his eye as he flips a coin.\\r\\n\\r\\nScrooge McDuck theme, it was just the idea and characteristic based around the idea. If possible i\\\'d like you to draw out a cooler looking duck.\\r\\n\\r\\nI do not want scrooge mcduck as my mascot at all. what i am asking is for a hip looking duck that\\\'s winking as he flips a coin positioned behind or leaning on the business name produck goodz. with the slogan underneath stating goodz you can bet on.', '2024-12-10 08:15:00', '2024-12-10 12:30:00', '2024-12-12', 'COMPLETED', 80, 26, 39),
(233, 3, 15, 'ice please', 'The business focuses on beverages and drinks made from tea with fruit flavors (peach, lemon, mango, and guava). The packaging will be in pouch packaging and ready-to-drink beverages.\\r\\n\\r\\nWe are looking for a typography design that can capture a fresh summer vibe when you are drinking our iced tea.\\r\\n\\r\\nPreferred colors: Shades of red, orange, blue, and yellow\\r\\n\\r\\nfor reference only: https://pin.it/5XHMvmFTm\\r\\n\\r\\nIndustry\\r\\nFood & Drink\\r\\n\\r\\nOther notes\\r\\n- the logo \\\"ice please\\\" is all lowercase\\r\\n- as it is a typography design, please use fonts that it is applicable for marketing purposes since it will be on the packaging\\r\\n- make it as vibrant as you can imagine\\r\\n-for reference only: https://pin.it/5XHMvmFTm', '2024-12-13 09:45:00', '2024-12-13 13:15:00', '2024-12-15', 'COMPLETED', 7, 27, 33),
(234, 3, 6, 'Oasis Property Management', 'Name\\r\\nOasis Properties (or Oasis Property Management) perhaps a few of each\\r\\n\\r\\nDescription\\r\\nWe manage two buildings in the Stadium District of Tacoma, WA. They are unique to their time periods (art deco 1950\\\'d and brick classic 1920\\\'s) which makes us stand out as the cooler properties on the block for being original and sort of quirky.\\r\\n\\r\\nIndustry\\r\\nReal Estate & Mortgage\\r\\n\\r\\nOther notes\\r\\nWe prefer the youthful modern Palm Springs vibe. Nothing cartooney or anything. Please no palm trees on an island, but palm leaves (if chosen) work fine.\\r\\n', '2024-12-13 14:30:00', '2024-12-13 18:45:00', '2024-12-15', 'COMPLETED', 42, 28, 36),
(235, 3, 15, 'GAC Impact Windows & Doors', 'GAC Impact Windows & Doors sells and installs windows and doors on residential homes.\\r\\nWe would like to use a globe - showing the United States (mainly Florida).\\r\\nAlso - we need to somehow incorporate the hurricane symbol and a roof or window into this logo. It can be added to the GAC letters or the globe.\\r\\n\\r\\nIndustry\\r\\nConstruction\\r\\n', '2024-12-13 11:20:00', '2024-12-13 15:45:00', '2024-12-15', 'COMPLETED', 30, 29, 34),
(236, 3, 6, 'Organic Strength', 'I am a fitness trainer. Organic Strength is my new workshop format. It consists of quite unique bodyweight exercises in order to train strenght and motor control in all the different angles of your main joints. I need a fresh logo that transports some kind of power and strength on the one side and has some organic or nature -ish touch on the other side.\\r\\n\\r\\nYou can maybe combine it with an animal which is strong and flexible.... like a mountain lion or a panther.\\r\\nIt´s a movement-oriented training system with body weight exercises. please no barbells or dumbbells in the logo because that´s not part of the system.\\r\\n\\r\\nIndustry\\r\\nPhysical Fitness\\r\\n', '2024-12-16 10:15:00', '2024-12-16 14:30:00', '2024-12-18', 'COMPLETED', 19, 30, 37),
(237, 3, 15, 'KnockOut FireWood Extravaganza', 'KnockOut FireWppd Extravaganza\\r\\nName to incorporate in the logo\\r\\nKnockOut FireWood\\r\\n\\r\\nDescription of the organization and its target audience\\r\\nFirewood and firewood bundles. Tourist, campers and home owners.\\r\\n\\r\\nIndustry\\r\\nAgriculture\\r\\n\\r\\nOther notes\\r\\nI would like the design to be a piece of fire wood holding a match in one hand and a marshmellow on fire in the other. Have a happy face and hair of flames. Like the candle on beauty and the beast\\r\\n', '2024-12-16 13:45:00', '2024-12-16 17:15:00', '2024-12-18', 'COMPLETED', 60, 31, 38),
(238, 3, 10, 'New World Essentials', 'Name to incorporate in the logo\\r\\nNew World Essentials\\r\\n\\r\\nSlogan to incorporate in the logo\\r\\nA new way for a new world\\r\\n\\r\\nDescription of the organization and its target audience\\r\\nWe carry products that are excellent for consumers of any age. As well as pet products. Organic products\\r\\n\\r\\nIndustry\\r\\nPhysical Fitness\\r\\n\\r\\nOther notes\\r\\nI really like the font that is used on US DOLLAR', '2024-12-19 09:30:00', '2024-12-19 13:45:00', '2024-12-21', 'COMPLETED', 100, 40, 42),
(239, 3, 10, 'Hannah House', 'Hannah House needs an inviting and memorable new logo\\r\\nName to incorporate in the logo\\r\\nThe Hannah House or Hannah House B & B\\r\\n\\r\\nDescription of the organization and its target audience\\r\\nOpening a small Bed & Breakfast in East Springfield, Pennsylvania just west of Erie.\\r\\n\\r\\nIndustry\\r\\nTravel & Hotel\\r\\n\\r\\nOther notes\\r\\nI would like to use turquoise in the design and maybe a five petal rose or a rose bud', '2024-12-19 15:20:00', '2024-12-19 19:45:00', '2024-12-21', 'COMPLETED', 40, 41, 43),
(240, 3, 15, 'Wentzville Whackers', 'Arrow is a senior living management company with a strong set of core values, a dedication to whole-person care, and a focus on being a great place to live and work.\\r\\n\\r\\nWentzville Whackers are a senior beachball team based in Missouri, and this logo will be for them (the team will do the final vote!). It\\\'s preferable that the featured ball be a beachball over a traditional volleyball.\\r\\n\\r\\nIndustry\\r\\nBusiness & Consulting\\r\\n\\r\\nOther notes\\r\\nAttached is their community logo if you want to take inspiration from the colors, but not necessary.', '2024-12-22 08:15:00', '2024-12-22 12:30:00', '2024-12-24', 'COMPLETED', 28, 44, 46),
(241, 3, 15, 'Entrepreneurs\\\' Camps of America', 'Educational camps\\r\\n\\r\\nIndustry\\r\\nEducation\\r\\n\\r\\nNotes\\r\\nProfessional logo that could be displayed on shirts, hats, notebooks, stationary and website.\\r\\nPlease show black and white logo on white background.', '2024-12-22 09:45:00', '2024-12-22 13:15:00', '2024-12-24', 'COMPLETED', 32, NULL, 45),
(242, 3, 15, 'Veloce', 'Logo Design Brief\\r\\nRedesign our logo.\\r\\nThe \\\'V\\\' to be one colour (not the 3d effect).\\r\\nOn the letter head, social media etc we must have the following included\\r\\nTraffic Management Solutions\\r\\n\\r\\nTarget Market(s)\\r\\nCorporate Customers\\r\\n\\r\\nIndustry/Entity Type\\r\\nTraffic Management.\\r\\n\\r\\nLogo Text\\r\\nVeloce\\r\\n\\r\\nMust have\\r\\nPlease see attachments', '2024-12-25 14:30:00', '2024-12-25 18:45:00', '2024-12-27', 'COMPLETED', 20, 47, 48),
(243, 3, 15, 'Coastal Cabinets & Countertops', 'Logo Design Brief\\r\\nWe are acquiring and existing 18 year old custom cabinet and coutertop business. We want to create a new logo concept. I am uploading their existing logo. We do not want to be identified as Only Coastal Properties. We also do not like or want a palm tree. We do not want to be strictly seen as residential homes as well. We are looking for something catchy, modern and design driven.\\r\\n\\r\\nOur location is Florida, but we will do business anywhere in the United States, so we don\\\'t want the logo to depict Florida or any specific location.\\r\\n\\r\\nLogo Text\\r\\nCoastal Cabinets and Countertops OR Coastal Cabinets & Countertops\\r\\n\\r\\nShould not have\\r\\nPalm Tree', '2024-12-28 11:20:00', '2024-12-28 15:45:00', '2024-12-30', 'COMPLETED', 28, 49, 52),
(244, 3, 6, 'Regal Rigs', 'Logo Design Brief\\r\\nI want to create a brand logo for a fashion label. The logo should be regal and classic.\\r\\n\\r\\nThe brand name is Regal Rigs and the tagline is Remarkable Fashion Artistry\\r\\n\\r\\nLogo Text\\r\\nRegal Rigs', '2024-12-28 10:15:00', '2024-12-28 14:30:00', '2024-12-30', 'COMPLETED', 42, NULL, 54),
(245, 3, 15, 'Begin Again Personal Training', 'Logo Design Brief\\r\\nWe need new branding for a personal training brand called Begin Again Personal Training. Previous logo is attached below. The personal training brand is focussed primarily on a holistic approach to health and fitness, and operates particularly with climbers. We would prefer not to see logos that are excessively focussed on gym equipment, muscle or are hyper masculinised. We are open to different colour palettes. We want our logo to feel unique (not too generic) and speak to our ethos of being approachable, fun and knowledgeable\\r\\n\\r\\nTarget Market(s)\\r\\n18+ All abilities, all genders\\r\\n\\r\\nIndustry/Entity Type\\r\\nHealth and Fitness\\r\\n\\r\\nLogo Text\\r\\nBegin Again Personal Training', '2024-12-28 13:45:00', '2024-12-28 17:15:00', '2024-12-30', 'COMPLETED', 44, 50, 53),
(246, 3, 6, 'AgencyCircle', 'Logo Design Brief\\r\\nFull details here: https://www.loom.com/share/d932d516680843ea97fa26508cc3e558\\r\\n\\r\\nLogo Text\\r\\nAgencyCircle', '2024-12-31 09:30:00', '2024-12-31 13:45:00', '2025-01-02', 'COMPLETED', 100, 51, 58),
(247, 3, 10, 'Reach Realty', 'A combination Icon/Wordmark Logo for the Real Estate Brokerage \\\"Reach Realty\\\"\\r\\n\\r\\nLogo Design Brief\\r\\nThis logo is for my real estate brokerage \\\"Reach Realty\\\" our tag line is \\\"Guiding You Home\\\"\\r\\nThe following is a 45 second promtional message used to promote our brokerage wherever we go.\\r\\n“Hi, my name is Adam Price and I am with Reach Realty. We are a full service real estate brokerage, empowering families on a journey to their next home. We educate along the trail and shine a light on any pitfalls in our path. Some journeys are far and winding, some are short and sweet, no matter the distance, we always walk until your home. Whether you\\\'re just having fun, exploring the idea of a journey or you\\\'ve strapped your boots on. The first step is always the same. Download our app where you can browse homes for fun or schedule an appointment to start walking.”\\r\\nI want the logo to combine an icon with a wordmark that says Reach Realty.\\r\\n\\r\\nLogo Text\\r\\nIcon + Reach Realty + Tagline + Guiding You Home', '2024-12-31 15:20:00', '2024-12-31 19:45:00', '2025-01-02', 'COMPLETED', 37, NULL, 57),
(248, 3, 15, 'Ultimate-Nutrition.', 'Logo Design Brief\\r\\nUltimate-Nutrition has been established since 1978. Our current range of human sports and health supplements is sold online under the brand name Ultimate-Nutrition.com.\\r\\n\\r\\nWe wish to develop a natural supplement range specifically dogs & cats, which will run alongside our products for humans.\\r\\n\\r\\nWe would like a logo which is strong visually yet simple but immediately recognisable as a pet product. (Dog/Cat)\\r\\n\\r\\nWe are looking to use electric blue pots with a cadmium yellow lid. I have attached an image so you can see the type and colour of the pot and lid.\\r\\n\\r\\nEventually this logo will be on the product labels.\\r\\n\\r\\nLogo Text\\r\\nUltimate-Nutrition. Natural Pet Supplements for owners that care.', '2024-12-31 08:15:00', '2024-12-31 12:30:00', '2025-01-02', 'COMPLETED', 51, 55, 56);

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
(57, 3, 'admin', '55c78c464c825bc722b7c582b3b4b83f1d3cb95892123d39740c1ba793b5c958', 1738035001, '2024-12-27 10:03:33'),
(76, 6, 'worker', '2b17e97aa13582eefe89f9bd13745fe07de8d2d00f6ff50dbe0bbf215925461c', 1738048329, '2024-12-29 07:12:08');

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
(6, 'razanius12', 'realgamer', 'Razan Muhammad Ihsan', 17, 'MALE', '6281238314426', 'TASKED', 244, '2024-11-26 07:14:02', '2024-12-29 07:00:11', 'main/imgdata/profile/676e7c52b35c4_profile_pic.png'),
(10, 'fauzanUber', 'fzfnfzfn', 'Muhammad Fauzan', 18, 'MALE', '6281234567878', 'TASKED', 247, '2024-11-29 10:01:30', '2024-12-29 07:04:34', ''),
(14, 'langs', 'yangaslinyanih', 'Erlangga', 13, 'MALE', '62895320087774', 'AVAILABLE', NULL, '2024-12-17 05:10:30', '2024-12-17 05:10:30', ''),
(15, 'vivi', 'prettiestgurls', 'Evelyna Cristina Ziovaj', 17, 'FEMALE', '6282257762471', 'TASKED', 248, '2024-12-24 15:30:59', '2024-12-29 07:04:10', 'main/imgdata/profile/676e81e2b23f2_profile_pic.png');

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
  MODIFY `id_attachment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `gmaps`
--
ALTER TABLE `gmaps`
  MODIFY `id_maps` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id_order` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=249;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id_position` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

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
