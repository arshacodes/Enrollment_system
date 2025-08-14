-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 14, 2025 at 05:38 AM
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
-- Database: `enrollment_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `type` enum('info','warning','error','success','login','logout','registration') DEFAULT 'info',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `message`, `type`, `created_at`) VALUES
(1, NULL, 'Admin logout: admin', 'logout', '2025-08-13 06:07:46'),
(2, NULL, 'Admin logout: admin', 'logout', '2025-08-13 06:08:00'),
(3, NULL, 'Admin logout: admin', 'logout', '2025-08-13 06:11:51'),
(4, NULL, 'Admin logout: admin', 'logout', '2025-08-13 06:23:35'),
(5, NULL, 'Admin logout: admin', 'logout', '2025-08-13 06:34:05'),
(6, NULL, 'Admin logout: admin', 'logout', '2025-08-13 06:35:49'),
(7, NULL, 'Admin logout: admin', 'logout', '2025-08-13 07:25:27'),
(8, NULL, 'Admin logout: admin', 'logout', '2025-08-13 07:26:51'),
(9, NULL, 'Admin logout: admin', 'logout', '2025-08-13 08:09:28'),
(10, NULL, 'Admin logout: admin', 'logout', '2025-08-13 08:09:45'),
(11, NULL, 'Admin logout: admin', 'logout', '2025-08-13 08:11:40'),
(12, NULL, 'Admin logout: admin', 'logout', '2025-08-13 08:26:45'),
(13, NULL, 'Admin logout: admin', 'logout', '2025-08-13 08:37:41'),
(14, NULL, 'Admin logout: admin', 'logout', '2025-08-13 08:46:27'),
(15, NULL, 'Admin logout: admin', 'logout', '2025-08-13 08:47:21'),
(16, NULL, 'Admin logout: admin', 'logout', '2025-08-13 08:55:53'),
(17, NULL, 'Admin logout: admin', 'logout', '2025-08-13 09:16:37'),
(18, NULL, 'Admin logout: admin', 'logout', '2025-08-13 09:31:16'),
(19, NULL, 'Admin logout: admin', 'logout', '2025-08-13 09:41:44'),
(20, NULL, 'Admin logout: admin', 'logout', '2025-08-13 10:00:45'),
(21, NULL, 'Admin logout: admin', 'logout', '2025-08-13 10:02:34'),
(22, NULL, 'Admin logout: admin', 'logout', '2025-08-13 10:37:36'),
(23, NULL, 'Admin logout: admin', 'logout', '2025-08-13 11:06:07'),
(24, NULL, 'Admin logout: admin', 'logout', '2025-08-13 14:07:52'),
(25, NULL, 'Admin logout: admin', 'logout', '2025-08-13 16:06:08'),
(26, NULL, 'Admin logout: admin', 'logout', '2025-08-13 16:51:24'),
(27, NULL, 'Admin logout: admin', 'logout', '2025-08-13 17:13:52'),
(28, NULL, 'Admin logout: admin', 'logout', '2025-08-13 19:54:09'),
(29, NULL, 'Admin logout: admin', 'logout', '2025-08-13 20:03:17'),
(30, NULL, 'Admin logout: admin', 'logout', '2025-08-13 20:30:19'),
(31, NULL, 'Admin logout: admin', 'logout', '2025-08-13 22:00:12'),
(32, NULL, 'Admin logout: admin', 'logout', '2025-08-13 22:15:21'),
(33, NULL, 'Admin logout: admin', 'logout', '2025-08-13 22:38:22'),
(34, NULL, 'Admin logout: admin', 'logout', '2025-08-13 23:47:29'),
(35, NULL, 'Admin logout: admin', 'logout', '2025-08-14 00:58:26'),
(36, 40, 'Enrolled in subject: IT 302', '', '2025-08-14 02:50:36'),
(37, NULL, 'Admin logout: admin', 'logout', '2025-08-14 03:11:45'),
(38, NULL, 'Admin logout: admin', 'logout', '2025-08-14 03:23:16'),
(39, NULL, 'Admin logout: admin', 'logout', '2025-08-14 03:25:50'),
(40, NULL, 'Admin logout: admin', 'logout', '2025-08-14 03:31:47'),
(41, 41, 'Enrolled in subject: IT 304', '', '2025-08-14 03:32:03');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','registrar','cashier') DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(2, 'registrar', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'registrar', '2025-08-13 05:34:21'),
(3, 'cashier', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cashier', '2025-08-13 05:34:21'),
(5, 'admin', '$2b$12$XD6ivV8Dgyxh4L4Q9h949.Dgaj/mbRStJ1.KYT4Pe3OLWLrgiTaI6', '', '2025-08-13 06:03:14');

-- --------------------------------------------------------

--
-- Table structure for table `billing`
--

CREATE TABLE `billing` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `semester` varchar(10) NOT NULL,
  `tuition_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `lab_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `misc_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_status` enum('unpaid','partial','paid') DEFAULT 'unpaid',
  `reference_number` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `billing`
--

INSERT INTO `billing` (`id`, `student_id`, `semester`, `tuition_fee`, `lab_fee`, `misc_fee`, `total_amount`, `payment_status`, `reference_number`, `created_at`) VALUES
(1, 40, '0', 10000.00, 2000.00, 1000.00, 13000.00, 'paid', NULL, '2025-08-14 02:50:36'),
(2, 41, '0', 10000.00, 2000.00, 1000.00, 13000.00, '', NULL, '2025-08-14 03:32:03');

-- --------------------------------------------------------

--
-- Table structure for table `billing_payments`
--

CREATE TABLE `billing_payments` (
  `id` int(11) NOT NULL,
  `billing_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `paid_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `billing_payments`
--

INSERT INTO `billing_payments` (`id`, `billing_id`, `amount`, `reference_number`, `paid_at`) VALUES
(1, 1, 5000.00, '', '2025-08-14 11:18:54'),
(2, 1, 3000.00, NULL, '2025-08-14 11:20:26'),
(3, 1, 3000.00, NULL, '2025-08-14 11:31:36'),
(4, 1, 2000.00, NULL, '2025-08-14 11:31:45'),
(5, 2, 3000.00, NULL, '2025-08-14 11:32:29'),
(6, 2, 10000.00, NULL, '2025-08-14 11:32:35');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `status` enum('enrolled','dropped','completed') DEFAULT 'enrolled',
  `grade` varchar(5) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `student_id`, `subject_id`, `status`, `grade`, `created_at`) VALUES
(1, 40, 1, 'enrolled', NULL, '2025-08-14 02:50:36'),
(2, 41, 4, 'enrolled', NULL, '2025-08-14 03:32:03');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `course` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_id`, `name`, `email`, `course`, `password`, `status`, `created_at`, `updated_at`) VALUES
(12, '', 'luke', 'luke@gmail.com', '', 'lukasgadin', 'approved', '2025-08-13 14:06:46', '2025-08-13 14:07:50'),
(18, '1', 'day', 'day@gmail.com', '', '123', 'approved', '2025-08-13 14:24:47', '2025-08-13 15:58:44'),
(20, '2025-3333', 'roblox', 'roblox@alagao.com', 'BSCS', '$2y$10$oNY.5mZ1XXcoi2CkHt6qD.iABWtDWkf.Z0KilJqhTo58wR.9Qp.7W', 'approved', '2025-08-13 16:10:15', '2025-08-13 16:51:07'),
(21, '2025-9898', 'iyaz', 'iyaz@gmail.com', 'BSIT', '$2y$10$s5dgCn9GtdiHnwX0IkasSusdaRI5PiBLjrFw00l5.LT73dgD0nKCe', 'approved', '2025-08-13 16:12:43', '2025-08-13 16:51:05'),
(22, '2023-7565', 'bata lang', 'bata@legal.com', 'BSBA', '$2y$10$9XFL9QI5iwfPz5UubeWK8OiY4v/QwaawUW013TeNoHKUd7m4DpW3K', 'approved', '2025-08-13 16:44:12', '2025-08-13 16:51:02'),
(23, '2025-00001', 'bata', 'mama@gmail.com', 'BSN', '$2y$10$ZJNctFwKHW2daVuRlWcKcez2mThfIouDOFNm6HNyolNapV1Rhvt.S', 'approved', '2025-08-13 16:46:03', '2025-08-13 16:50:58'),
(24, '2023-57921', 'william pateo', 'pateoday@gmail.com', 'BSCS', '$2y$10$zyMVeceoKFdTUj5o48PlM.H0b4JIuRJn5NHDFMMIRKSlqSOEp84Bm', 'approved', '2025-08-13 16:47:55', '2025-08-13 16:50:55'),
(25, '2023-6575', 'zxczxczxc', 'zxczxc@gmail.com', 'BSCS', '$2y$10$x/QqSSkpO3csztXijXcifuDg8mZf6zJubxW1yBh6R28oxsWFHZvO.', 'pending', '2025-08-13 16:58:16', '2025-08-13 16:58:16'),
(26, '2023-5321', 'DADDY', 'daddy@gmail.com', 'BSCS', '$2y$10$GHv5dqDqY/8mqXQ2PvIHC.cUJygP3TrIQ6nDZRDYCUs8bWuCXPri.', 'pending', '2025-08-13 17:02:28', '2025-08-13 17:02:28'),
(27, '2023-52111', 'mommy', 'mommy@gmail.com', 'BSBA', '$2y$10$SjC1fEN4d/dX0RVmRedqNOIbgGT4.4S8BrCgc/xclPZ.7uOHK2UX.', 'pending', '2025-08-13 17:03:49', '2025-08-13 17:03:49'),
(28, '2023-23542', 'papa', 'papa@gmail.com', 'BSIT', '$2y$10$ihZg3WB2oq2EqRaoNXKkMeSkAQUs33kWvxzTvoT8x04JX38fHHWEO', 'pending', '2025-08-13 17:05:19', '2025-08-13 17:05:19'),
(29, '2222-22222', 'koya', 'koyya@gmail.com', 'BSBA', '$2y$10$/4GSUAkQcAGMFFrn9yAH9emBIgimfMW3uzxbgVrqFIVbFzoX/yOc2', 'pending', '2025-08-13 17:06:08', '2025-08-13 17:06:08'),
(30, '2023-2123123', 'hey', 'hey@gmail.com', 'BSCS', '$2y$10$pCcBd4wX6CKriPLUAoFYzeQopB/CY8gtOxvhsTtrmft2hPBdZIKLC', 'pending', '2025-08-13 17:29:26', '2025-08-13 17:29:26'),
(31, '20233-5331', 'yhesha dayday', 'yhesha@gmail.com', 'BSBA', '$2y$10$BOnBsWfcE1HV751trfFaIu0XrThlVGx1e07odImwE7Bo76oFxuMFS', 'pending', '2025-08-13 17:37:31', '2025-08-13 17:37:31'),
(32, '2023-22221', 'gaogao', 'gaogao@gmail.com', 'BSBA', '$2y$10$xlqyCx2U.s2mQhW2kePltOZr7ieqmxmTiHqH5rm/5CKjFZtxUXQAO', 'pending', '2025-08-13 21:59:34', '2025-08-13 21:59:34'),
(33, '2302-53312', 'jorzen', 'jorzen@gmail.com', 'BSED', '$2y$10$m4zcRy//EKW9e6HzfqF9P.vRKQ1TDgskZBuLLaoVydbQ/BHOfWVEe', 'pending', '2025-08-13 23:46:46', '2025-08-13 23:46:46'),
(34, '2023-54221', 'prince pateo', 'pateodayzz@gmail.com', 'BSN', '$2y$10$xx4MQj3vNkg8QdlR6JdnGu8ib1LQT6wXcoDLbTgQrODM/sLYYVhJ.', 'pending', '2025-08-14 00:58:11', '2025-08-14 00:58:11'),
(35, '2025-07234', 'chiyo', 'chiyo@gmail.com', 'BSEE', '$2y$10$mkeHDfw0BEnW1NOQlE.nO.AkPty4bVSFuyhE8pO5tyKW.xmW7Zv7e', 'pending', '2025-08-14 01:11:24', '2025-08-14 01:11:24'),
(36, '2025-07233', 'hatdog', 'hatdog@gmail.com', 'BSEE', '$2y$10$sDgYbcC4fXI3zTwM5a/3fOBGY.QTdwFKeekO.boYploXtL8jozGp.', 'pending', '2025-08-14 01:13:20', '2025-08-14 01:13:20'),
(37, '2025-07232', 'zen', 'zen@gmail.com', 'BSEE', '$2y$10$TpyA53MZ9ocwCzZaFF23DeIQzT8ypzdHQMnvhz0MqKNPS6lsOyElK', 'pending', '2025-08-14 01:18:52', '2025-08-14 01:18:52'),
(38, '2025-12345', 'luke', 'luke1@gmail.com', 'BSCE', '$2y$10$T1gRGuvSg3ix96gHGbH0TeqArxUs6NmpXHQa6iDSu1wBgJNvE6Nge', 'pending', '2025-08-14 01:20:57', '2025-08-14 01:20:57'),
(39, '2025-01234', 'yuyu', 'yuyu@gmail.com', 'BSN', '$2y$10$CUz6xttVySA881BNRYF5TepZTOtCEromOOQnF8a0cjgT2cjnMjOBW', 'pending', '2025-08-14 01:23:02', '2025-08-14 01:23:02'),
(40, '2025-12346', 'yoyo', 'yoyo@gmail.com', 'BSN', '$2y$10$Ru471dBbXAqBgPAb6E8BseSQe7xszpDVgqaGOXPOhRmOQhDdyFTEe', 'approved', '2025-08-14 01:24:14', '2025-08-14 03:11:18'),
(41, '2025-67890', 'yiyi', 'yiyi@gmail.com', 'BSCS', '$2y$10$b7H1U4OqBdlBlhke45CS5u7vMqA7O76czMEEaWUx5B8ZSLaVIxNDS', 'approved', '2025-08-14 01:24:53', '2025-08-14 03:32:20'),
(42, '2025-23456', 'nene', 'nene@gmail.com', 'BSCE', '$2y$10$vo2t7sPJyFssOv0dlcM6A.Fnt4jiPRnxgKHUn./VCki0pqRCl2lIi', 'pending', '2025-08-14 01:27:10', '2025-08-14 01:27:10'),
(43, '2025-56789', 'ala', 'ala@gmail.com', 'BSCE', '$2y$10$X.0XIXkHMSzSfrUuD6wc5erBrI5zAYy7xr1BMsEoLmY6VdP29G8b.', 'pending', '2025-08-14 01:29:18', '2025-08-14 01:29:18'),
(44, '2024-56789', 'ghjkl', 'ghjkl@gmail.com', 'BSBA', '$2y$10$TLAueARCESg5vKeD4zhLv.XIXsaqZCIMelO71cUPEGZpf..l1aRHS', 'pending', '2025-08-14 01:31:27', '2025-08-14 01:31:27'),
(45, '2021-34567', 'qwert', 'qwert@gmail.com', 'BSED', '$2y$10$ELa9/nC3RvheAzZ/H1VXHeD64Ba911JfYRtPM6ADamzPFz18VvZ3S', 'pending', '2025-08-14 01:32:32', '2025-08-14 01:32:32');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_code` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `units` int(11) NOT NULL,
  `department` varchar(50) NOT NULL,
  `year_level` int(11) NOT NULL,
  `semester` int(11) NOT NULL,
  `instructor` varchar(100) NOT NULL,
  `schedule` varchar(100) NOT NULL,
  `max_slots` int(11) NOT NULL DEFAULT 30,
  `available_slots` int(11) NOT NULL DEFAULT 30,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_code`, `title`, `description`, `units`, `department`, `year_level`, `semester`, `instructor`, `schedule`, `max_slots`, `available_slots`, `is_active`, `created_at`) VALUES
(1, 'IT 302', 'Systems Integration and Architecture 1', NULL, 3, 'IT', 1, 0, 'Prof. Felices', 'MWF 8:00-9:00 AM', 40, 34, 1, '2025-08-13 05:34:39'),
(2, 'IT 301', 'Human Computer Interaction 2', NULL, 3, 'IT', 1, 0, 'Prof. Sunga', 'TTH 10:00-11:30 AM', 35, 28, 1, '2025-08-13 05:34:39'),
(3, 'IT 303', 'Networking 2', NULL, 3, 'IT', 2, 0, 'Prof. Nadala', 'MWF 1:00-2:00 PM', 30, 30, 1, '2025-08-13 05:34:39'),
(4, 'IT 304', 'Quantitative Method', NULL, 3, 'IT', 1, 0, 'Prof. Gagalac', 'TTH 2:00-3:30 PM', 35, 24, 1, '2025-08-13 05:34:39'),
(5, 'IT 306', 'Integrative Programming Technologies 2', NULL, 3, 'IT', 1, 0, 'Prof. Daguino', 'MWF 3:00-4:00 PM', 25, 20, 1, '2025-08-13 05:34:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `billing`
--
ALTER TABLE `billing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `billing_payments`
--
ALTER TABLE `billing_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `billing_id` (`billing_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_enrollment` (`student_id`,`subject_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subject_code` (`subject_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `billing`
--
ALTER TABLE `billing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `billing_payments`
--
ALTER TABLE `billing_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `students` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `billing`
--
ALTER TABLE `billing`
  ADD CONSTRAINT `billing_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `billing_payments`
--
ALTER TABLE `billing_payments`
  ADD CONSTRAINT `billing_payments_ibfk_1` FOREIGN KEY (`billing_id`) REFERENCES `billing` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
