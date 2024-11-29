-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 29, 2024 at 09:46 AM
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
-- Table structure for table `project_assignments`
--

CREATE TABLE `project_assignments` (
  `id_assignment` int(4) NOT NULL,
  `id_order` int(4) NOT NULL,
  `id_worker` int(4) NOT NULL,
  `assigned_by` int(4) NOT NULL,
  `status` enum('ASSIGNED','IN_PROGRESS','COMPLETED') NOT NULL DEFAULT 'ASSIGNED',
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `project_assignments`
--
DELIMITER $$
CREATE TRIGGER `after_project_assignment` AFTER INSERT ON `project_assignments` FOR EACH ROW BEGIN
    UPDATE workers 
    SET current_tasks = current_tasks + 1,
        availability_status = CASE 
            WHEN current_tasks + 1 > 0 THEN 'TASKED'
            ELSE 'AVAILABLE'
        END
    WHERE id_worker = NEW.id_worker;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_project_completion` AFTER UPDATE ON `project_assignments` FOR EACH ROW BEGIN
    IF NEW.status = 'COMPLETED' AND OLD.status != 'COMPLETED' THEN
        UPDATE workers 
        SET current_tasks = current_tasks - 1,
            availability_status = CASE 
                WHEN current_tasks - 1 = 0 THEN 'AVAILABLE'
                ELSE 'TASKED'
            END
        WHERE id_worker = NEW.id_worker;
    END IF;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `project_assignments`
--
ALTER TABLE `project_assignments`
  ADD PRIMARY KEY (`id_assignment`),
  ADD KEY `id_order` (`id_order`),
  ADD KEY `id_worker` (`id_worker`),
  ADD KEY `assigned_by` (`assigned_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `project_assignments`
--
ALTER TABLE `project_assignments`
  MODIFY `id_assignment` int(4) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `project_assignments`
--
ALTER TABLE `project_assignments`
  ADD CONSTRAINT `project_assignments_ibfk_1` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`),
  ADD CONSTRAINT `project_assignments_ibfk_2` FOREIGN KEY (`id_worker`) REFERENCES `workers` (`id_worker`),
  ADD CONSTRAINT `project_assignments_ibfk_3` FOREIGN KEY (`assigned_by`) REFERENCES `admins` (`id_admin`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
