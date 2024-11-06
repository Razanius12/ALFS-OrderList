-- Drop existing tables if needed
-- SET FOREIGN_KEY_CHECKS = 0;
-- DROP TABLE IF EXISTS admins, workers, orders, positions, project_assignments, daily_earnings;
-- SET FOREIGN_KEY_CHECKS = 1;

-- Positions table to store all possible job positions
CREATE TABLE `positions` (
  `id_position` int(4) NOT NULL AUTO_INCREMENT,
  `position_name` varchar(32) NOT NULL,
  `department` ENUM('ADMIN', 'WORKER') NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Modified admins table with position reference
CREATE TABLE `admins` (
  `id_admin` int(4) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL UNIQUE,
  `name_admin` varchar(255) NOT NULL,
  `id_position` int(4) NOT NULL,
  `phone_number` varchar(16) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_admin`),
  FOREIGN KEY (`id_position`) REFERENCES `positions`(`id_position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Modified workers table with position reference
CREATE TABLE `workers` (
  `id_worker` int(4) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL UNIQUE,
  `name_worker` varchar(255) NOT NULL,
  `id_position` int(4) NOT NULL,
  `gender_worker` ENUM('MALE', 'FEMALE', 'OTHER') NOT NULL,
  `phone_number` varchar(16) NOT NULL,
  `availability_status` ENUM('AVAILABLE', 'TASKED') NOT NULL DEFAULT 'AVAILABLE',
  `current_tasks` int(2) NOT NULL DEFAULT 0,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_worker`),
  FOREIGN KEY (`id_position`) REFERENCES `positions`(`id_position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Enhanced orders table with more details
CREATE TABLE `orders` (
  `id_order` int(4) NOT NULL AUTO_INCREMENT,
  `order_name` varchar(255) NOT NULL,
  `description` text,
  `client_name` varchar(255) NOT NULL,
  `project_manager_id` int(4) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` ENUM('PENDING', 'IN_PROGRESS', 'COMPLETED', 'CANCELLED') NOT NULL DEFAULT 'PENDING',
  `start_date` date NOT NULL,
  `deadline` date NOT NULL,
  `completed_at` timestamp NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_order`),
  FOREIGN KEY (`project_manager_id`) REFERENCES `admins`(`id_admin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Project assignments table to track worker assignments
CREATE TABLE `project_assignments` (
  `id_assignment` int(4) NOT NULL AUTO_INCREMENT,
  `id_order` int(4) NOT NULL,
  `id_worker` int(4) NOT NULL,
  `assigned_by` int(4) NOT NULL,
  `status` ENUM('ASSIGNED', 'IN_PROGRESS', 'COMPLETED') NOT NULL DEFAULT 'ASSIGNED',
  `assigned_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `completed_at` timestamp NULL,
  PRIMARY KEY (`id_assignment`),
  FOREIGN KEY (`id_order`) REFERENCES `orders`(`id_order`),
  FOREIGN KEY (`id_worker`) REFERENCES `workers`(`id_worker`),
  FOREIGN KEY (`assigned_by`) REFERENCES `admins`(`id_admin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Daily earnings table for financial tracking
CREATE TABLE `daily_earnings` (
  `id_earning` int(4) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `total_orders` int(4) NOT NULL DEFAULT 0,
  `total_earnings` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_earning`),
  UNIQUE KEY `unique_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Trigger to update worker status when assigned to projects
DELIMITER //
CREATE TRIGGER after_project_assignment
AFTER INSERT ON project_assignments
FOR EACH ROW
BEGIN
    UPDATE workers 
    SET current_tasks = current_tasks + 1,
        availability_status = CASE 
            WHEN current_tasks + 1 > 0 THEN 'TASKED'
            ELSE 'AVAILABLE'
        END
    WHERE id_worker = NEW.id_worker;
END;//

-- Trigger to update worker status when project is completed
CREATE TRIGGER after_project_completion
AFTER UPDATE ON project_assignments
FOR EACH ROW
BEGIN
    IF NEW.status = 'COMPLETED' AND OLD.status != 'COMPLETED' THEN
        UPDATE workers 
        SET current_tasks = current_tasks - 1,
            availability_status = CASE 
                WHEN current_tasks - 1 = 0 THEN 'AVAILABLE'
                ELSE 'TASKED'
            END
        WHERE id_worker = NEW.id_worker;
    END IF;
END;//
DELIMITER ;
