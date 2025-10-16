-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 16, 2025 at 04:13 AM
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
-- Database: `dtrecorder`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'CITE OFFICE', 'CITE'),
(2, 'JJ Narvasa', 'Narvasa123');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `department_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `department_name`) VALUES
(1, 'CAHS'),
(7, 'CAS'),
(6, 'CCJE'),
(2, 'CEA'),
(5, 'CELA'),
(3, 'CITE'),
(4, 'CMA');

-- --------------------------------------------------------

--
-- Table structure for table `duty_logs`
--

CREATE TABLE `duty_logs` (
  `id` int(11) NOT NULL,
  `assigned_id` int(11) NOT NULL,
  `duty_date` date NOT NULL,
  `time_in` time NOT NULL,
  `time_out` time NOT NULL,
  `remarks` text DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `logged_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `duty_requests`
--

CREATE TABLE `duty_requests` (
  `id` int(11) NOT NULL,
  `assigned_id` int(11) NOT NULL,
  `duty_date` date NOT NULL,
  `time_in` time NOT NULL,
  `time_out` time NOT NULL,
  `remarks` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `submitted_at` datetime DEFAULT current_timestamp(),
  `reviewed_at` datetime DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `duty_requests`
--

INSERT INTO `duty_requests` (`id`, `assigned_id`, `duty_date`, `time_in`, `time_out`, `remarks`, `status`, `submitted_at`, `reviewed_at`, `reviewed_by`) VALUES
(1, 1, '2025-10-14', '08:00:00', '11:00:00', 'Library shelf labeling', 'pending', '2025-10-16 10:13:09', NULL, NULL),
(2, 1, '2025-10-16', '09:30:00', '12:30:00', 'Assisted with book returns', 'pending', '2025-10-16 10:13:09', NULL, NULL),
(3, 2, '2025-10-15', '10:00:00', '13:00:00', 'Inventory check in science lab', 'pending', '2025-10-16 10:13:09', NULL, NULL),
(4, 2, '2025-10-17', '08:30:00', '11:30:00', 'Helped organize lab equipment', 'pending', '2025-10-16 10:13:09', NULL, NULL),
(5, 3, '2025-10-14', '13:00:00', '16:00:00', 'Assisted in guidance office filing', 'pending', '2025-10-16 10:13:09', NULL, NULL),
(6, 3, '2025-10-18', '09:00:00', '12:00:00', 'Helped with student ID distribution', 'pending', '2025-10-16 10:13:09', NULL, NULL),
(7, 4, '2025-10-15', '07:30:00', '10:30:00', 'Cleaned and arranged sports equipment', 'pending', '2025-10-16 10:13:09', NULL, NULL),
(8, 4, '2025-10-19', '10:00:00', '13:00:00', 'Assisted during PE class setup', 'pending', '2025-10-16 10:13:09', NULL, NULL),
(9, 5, '2025-10-16', '14:00:00', '17:00:00', 'Helped decorate bulletin boards', 'pending', '2025-10-16 10:13:09', NULL, NULL),
(10, 5, '2025-10-20', '08:00:00', '11:00:00', 'Assisted in art room cleanup', 'pending', '2025-10-16 10:13:09', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `scholarship_types`
--

CREATE TABLE `scholarship_types` (
  `id` int(11) NOT NULL,
  `scholarship_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scholarship_types`
--

INSERT INTO `scholarship_types` (`id`, `scholarship_name`) VALUES
(1, 'HK25'),
(2, 'HK50'),
(3, 'HK75'),
(4, 'SA');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `password_hash` varchar(255) NOT NULL,
  `reset_token_hash` varchar(64) DEFAULT NULL,
  `reset_token_expiration` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `middle_name`, `last_name`, `email_address`, `student_id`, `created_at`, `password_hash`, `reset_token_hash`, `reset_token_expiration`) VALUES
(1, 'Carl Elijah Ron', 'Cayabyab', 'Canullas', 'caca.canullas.up@phinmaed.com', '03-01-2425-041045', '2025-10-16 06:13:03', '$2y$10$/qO5DIugElVIbDNzRJSawuS6bsyQooO3XDwju/tHWLEaTpA1/6Wcu', NULL, NULL),
(2, 'Arceli', 'Viernes', 'Mapili', 'arvi.mapili.up@phinmaed.com', '03-01-2425-043344', '2025-10-16 09:58:56', '$2y$10$QT796RCm/OQ9UBfj4YRdwO6NM6SQd6CwvULRe1jq04R53mg7w2Qry', NULL, NULL),
(3, 'Jeverlee', 'Resonable', 'Naron', 'jere.naron.up@phinmaed.com', '03-01-2425-045551', '2025-10-16 10:00:26', '$2y$10$ufkk45PnsJuvGQGBANtS7.HC/LUMFUk926cSOgnwvhGNKHriKbwHq', NULL, NULL),
(4, 'Miguel', 'Galpao', 'Nasurada', 'miga.nasurada.up@phinmaed.com', '03-01-2425-449255', '2025-10-16 10:01:15', '$2y$10$uKU0LWMHToNe1AgmPBd2xOc12MbEWx9KBucMOgy7QvMbs9SQNc5Q.', NULL, NULL),
(5, 'Junald', 'Sapiera', 'Valencia', 'unsa.valencia.up@phinmaed.com', '03-01-2425-040144', '2025-10-16 10:02:32', '$2y$10$ebzBiK.Pnz2zyNha34Ne7.3E2T3qn5GbL.49HWiPiGeDMIjvQcssq', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_assigned`
--

CREATE TABLE `users_assigned` (
  `assigned_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `student_id` bigint(20) NOT NULL,
  `assigned_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_assigned`
--

INSERT INTO `users_assigned` (`assigned_id`, `admin_id`, `student_id`, `assigned_at`) VALUES
(1, 1, 1, '2025-10-16 06:13:29'),
(2, 1, 2, '2025-10-16 10:04:27'),
(3, 1, 3, '2025-10-16 10:04:33'),
(4, 1, 4, '2025-10-16 10:04:38'),
(5, 1, 5, '2025-10-16 10:04:42');

-- --------------------------------------------------------

--
-- Table structure for table `users_info`
--

CREATE TABLE `users_info` (
  `user_id` bigint(20) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `scholarship_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_info`
--

INSERT INTO `users_info` (`user_id`, `department_id`, `scholarship_id`) VALUES
(1, 3, 2),
(2, 3, 4),
(3, 3, 4),
(4, 3, 1),
(5, 3, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `department_name` (`department_name`);

--
-- Indexes for table `duty_logs`
--
ALTER TABLE `duty_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assigned_id` (`assigned_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `duty_requests`
--
ALTER TABLE `duty_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assigned_id` (`assigned_id`),
  ADD KEY `reviewed_by` (`reviewed_by`);

--
-- Indexes for table `scholarship_types`
--
ALTER TABLE `scholarship_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `scholarship_name` (`scholarship_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_address` (`email_address`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD UNIQUE KEY `reset_token_hash` (`reset_token_hash`);

--
-- Indexes for table `users_assigned`
--
ALTER TABLE `users_assigned`
  ADD PRIMARY KEY (`assigned_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `users_info`
--
ALTER TABLE `users_info`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `users_info_department` (`department_id`),
  ADD KEY `users_info_scholarship` (`scholarship_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `duty_logs`
--
ALTER TABLE `duty_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `duty_requests`
--
ALTER TABLE `duty_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `scholarship_types`
--
ALTER TABLE `scholarship_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users_assigned`
--
ALTER TABLE `users_assigned`
  MODIFY `assigned_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `duty_logs`
--
ALTER TABLE `duty_logs`
  ADD CONSTRAINT `duty_logs_ibfk_1` FOREIGN KEY (`assigned_id`) REFERENCES `users_assigned` (`assigned_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `duty_logs_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `duty_requests`
--
ALTER TABLE `duty_requests`
  ADD CONSTRAINT `duty_requests_ibfk_1` FOREIGN KEY (`assigned_id`) REFERENCES `users_assigned` (`assigned_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `duty_requests_ibfk_2` FOREIGN KEY (`reviewed_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users_assigned`
--
ALTER TABLE `users_assigned`
  ADD CONSTRAINT `users_assigned_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_assigned_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users_info`
--
ALTER TABLE `users_info`
  ADD CONSTRAINT `users_info_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `users_info_scholarship` FOREIGN KEY (`scholarship_id`) REFERENCES `scholarship_types` (`id`),
  ADD CONSTRAINT `users_info_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
