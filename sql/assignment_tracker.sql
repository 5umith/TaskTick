-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2026 at 08:06 AM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `assignment_tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `due_date` date NOT NULL,
  `teacher_id` int(150) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6),
  `updated_at` timestamp(6) NULL DEFAULT current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`id`, `title`, `description`, `due_date`, `teacher_id`, `created_at`, `updated_at`) VALUES
(1, 'ds', 'ds assignment', '2025-05-13', 4, '2025-05-08 15:55:25.359298', '2025-05-08 15:55:25.359298'),
(2, 'bankai', 'gkidugkwi kujhfkudeh', '2025-05-13', 4, '2025-05-08 17:48:44.861443', '2025-05-08 17:48:44.861443'),
(3, 'AI Assignment', 'Dear Students, kindly submit your assignments before due date.', '2025-05-12', 4, '2025-05-09 06:14:51.938379', '2025-05-09 06:14:51.938379'),
(4, 'java assignment', 'must be submitted before 15', '2025-05-15', 4, '2025-05-09 15:24:24.771732', '2025-05-09 15:24:24.771732'),
(5, 'cma', 'cma assignment', '2025-05-15', 4, '2025-05-09 15:34:07.228434', '2025-05-09 15:34:07.228434'),
(6, 'ASSIGNMENT MAADA BABA', 'assignment', '2025-05-12', 4, '2025-05-09 15:40:30.438493', '2025-05-09 15:40:30.438493'),
(7, 'ASSIGNMENT MAADA BABA', 'assignment', '2025-05-12', 4, '2025-05-09 15:42:51.450466', '2025-05-09 15:42:51.450466'),
(8, 'rappa assignment maad', 'assignment', '2025-05-12', 4, '2025-05-09 15:50:43.059019', '2025-05-09 15:50:43.059019'),
(9, 'PHP', 'Complete assignment.', '2025-05-16', 4, '2025-05-15 09:50:16.681990', '2025-05-15 09:50:16.681990'),
(10, 'java', 'rutika complete your assignment', '2025-05-24', 4, '2025-05-23 05:50:29.521670', '2025-05-23 05:50:29.521670'),
(11, 'PHP Assignment', 'Write answers for unit 1 questions.', '2025-05-30', 11, '2025-05-23 16:18:45.505972', '2025-05-23 16:18:45.505972'),
(12, 'PHP Assignment', 'Write answers for unit 1 questions.', '2025-05-30', 11, '2025-05-23 16:19:29.414215', '2025-05-23 16:19:29.414215'),
(13, 'PHP Assignment', 'yttr5d', '2025-05-30', 12, '2025-05-29 05:50:24.681868', '2025-05-29 05:50:24.681868'),
(14, 'PHP Assignment', 'yttr5d', '2025-05-30', 12, '2025-05-29 05:51:05.446943', '2025-05-29 05:51:05.446943'),
(15, 'PHP Assignment', 'yttr5d', '2025-05-30', 12, '2025-05-29 05:51:20.823440', '2025-05-29 05:51:20.823440'),
(16, 'php', '20 qns', '2025-05-31', 4, '2025-05-29 07:48:06.045391', '2025-05-29 07:48:06.045391'),
(17, 'dsa', 'heaps assignment', '2026-05-23', 15, '2026-05-20 07:19:47.806981', '2026-05-20 07:19:47.806981'),
(18, 'dumb', 'aaa', '2026-05-30', 15, '2026-05-21 05:07:49.734948', '2026-05-21 05:07:49.734948'),
(19, 'check mail 1', '1', '2026-05-30', 15, '2026-05-21 05:15:00.702093', '2026-05-21 05:15:00.702093'),
(20, 'email check 2', '2', '2026-05-28', 15, '2026-05-21 05:21:31.975523', '2026-05-21 05:21:31.975523'),
(21, 'check 3', '3', '2026-06-05', 15, '2026-05-21 05:23:43.621332', '2026-05-21 05:23:43.621332'),
(22, 'check final', 'te', '2026-05-22', 15, '2026-05-21 05:55:08.955833', '2026-05-21 05:55:08.955833'),
(23, 'mail check 1', '1', '2026-05-22', 15, '2026-05-21 06:13:48.578007', '2026-05-21 06:13:48.578007'),
(24, 'mail check 1', '1', '2026-05-22', 15, '2026-05-21 06:16:46.386402', '2026-05-21 06:16:46.386402'),
(25, 'hhh', 'jhh', '2026-05-30', 15, '2026-05-21 06:17:17.473078', '2026-05-21 06:17:17.473078'),
(26, 'as tract', 'trac', '2026-05-29', 15, '2026-05-21 06:21:22.796179', '2026-05-21 06:21:22.796179'),
(27, 'ce', 'ce', '2026-06-04', 15, '2026-05-21 06:39:24.890717', '2026-05-21 06:39:24.890717'),
(28, 'url check', 'check', '2026-05-28', 15, '2026-05-21 06:55:01.158836', '2026-05-21 06:55:01.158836'),
(29, 'url check 2', 'chwck 2', '2026-05-30', 15, '2026-05-21 07:00:24.935462', '2026-05-21 07:00:24.935462');

-- --------------------------------------------------------

--
-- Table structure for table `student_assignments`
--

CREATE TABLE `student_assignments` (
  `id` int(11) NOT NULL,
  `student_id` int(150) NOT NULL,
  `assignment_id` int(150) NOT NULL,
  `status` enum('not_started','in_progress','completed','') NOT NULL,
  `submission` text NOT NULL,
  `feedback` text NOT NULL,
  `submitted_at` datetime(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `student_assignments`
--

INSERT INTO `student_assignments` (`id`, `student_id`, `assignment_id`, `status`, `submission`, `feedback`, `submitted_at`) VALUES
(1, 1, 1, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(2, 3, 2, 'in_progress', 'djbkn', '', '0000-00-00 00:00:00.000000'),
(3, 2, 3, 'not_started', '', 'kjghk', '0000-00-00 00:00:00.000000'),
(4, 3, 3, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(5, 1, 4, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(6, 5, 5, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(7, 5, 6, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(8, 7, 6, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(9, 5, 7, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(10, 7, 7, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(11, 5, 8, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(12, 8, 8, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(13, 1, 9, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(14, 2, 9, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(15, 3, 9, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(16, 5, 9, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(17, 7, 9, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(18, 8, 9, 'in_progress', '', '', '0000-00-00 00:00:00.000000'),
(19, 10, 10, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(20, 1, 11, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(21, 2, 11, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(22, 3, 11, 'not_started', 'hfyhtfvjh', 'gujygju', '0000-00-00 00:00:00.000000'),
(23, 5, 11, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(24, 7, 11, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(25, 8, 11, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(26, 10, 11, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(27, 1, 12, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(28, 2, 12, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(29, 3, 12, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(30, 5, 12, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(31, 7, 12, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(32, 8, 12, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(33, 10, 12, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(34, 1, 13, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(35, 7, 13, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(36, 1, 14, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(37, 7, 14, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(38, 1, 15, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(39, 7, 15, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(40, 5, 16, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(41, 7, 16, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(42, 1, 17, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(43, 14, 17, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(44, 1, 18, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(45, 14, 18, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(46, 14, 19, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(47, 16, 19, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(48, 14, 20, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(49, 16, 20, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(50, 14, 21, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(51, 16, 21, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(52, 14, 22, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(53, 16, 22, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(54, 14, 23, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(55, 16, 23, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(56, 14, 24, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(57, 16, 24, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(58, 16, 25, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(59, 16, 26, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(60, 16, 27, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(61, 16, 28, 'not_started', '', '', '0000-00-00 00:00:00.000000'),
(62, 16, 29, 'not_started', '', '', '0000-00-00 00:00:00.000000');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(150) NOT NULL,
  `role` varchar(155) NOT NULL,
  `reset_token` varchar(10) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `reset_token`, `reset_token_expires`) VALUES
(2, 'dingo', 'dingo@gmail.com', '$2y$10$tEjJOpblucC7BZKSXGC/beEk3obg0fsw8ZsYbvS6LNjihM0Q1wwIC', 'student', NULL, NULL),
(3, 'bankai', 'bankai@gmail.com', '$2y$10$iUGJKqbkaC8fS2myZxRrhumBoXNKr2bd6U3zhjZpFT.nH03tU.xnS', 'student', NULL, NULL),
(4, 'teach', 'teach@gmail.com', '$2y$10$rCDw5A69Drk8OI/PL2LO3.PhbkxcJ2KP16ujJLB5HgyuuOA/IvVza', 'teacher', NULL, NULL),
(7, 'babanna', 'prashantbaba27@gmail.com', '$2y$10$9hivhWEezIVs907rDY8Vvuq0FYcvzOEuGmbJwnyWWnALdy.XB14m.', 'student', NULL, NULL),
(8, 'babanna', 'tarun.eth27@gmail.com', '$2y$10$i7nrqScC6T9UDV1E4eVyH.tYymaNJpXD8woCHsBmPd6bvfW6a9kNa', 'student', NULL, NULL),
(9, 'Shri Raksha', 'rakshu@gmail.com', '$2y$10$j0DImPYO1vxpnVyaCV8EruFMJVGJZz.3aleZJKjR7Zw5p2li7/cve', 'teacher', NULL, NULL),
(10, 'rutika', 'rutikanaik809@gmail.com', '$2y$10$9mOvQMmyEVbGFVY1LGheEegflCX2224KGk8ar/hN2BX25qQNVchta', 'student', NULL, NULL),
(11, 'Prasad(HOD)', 'prasad@gmail.com', '$2y$10$lMSLvrdmbpmFHg0detZF0Oiqt25nkGIlbthoO/ikvMpDGaJ1lG7Fu', 'teacher', NULL, NULL),
(12, 'Prasad(HOD)', 'prasad121@gmail.com', '$2y$10$jrnBSioaExMLBTuIu/acl..B1mSIq3biT8BfIaMdGbXVf5uYVVqaO', 'teacher', NULL, NULL),
(13, 'Sumith S Suvarna', 'sumithsuvarna@gmail.com', '$2y$10$kKJLI6Jri0ztbjaPu5amMeB3svgKYwDoX9j5RBo/PcLVjmVWJ91r6', 'student', '637741', '2026-05-21 12:59:05'),
(14, 'SUMITH S SUVARNA', 'botStud@gmail.com', '$2y$10$FqXdMf4NOm42LKeL.LcPcePeQKhtIT1WtbQ1B.mQAlyi7IbtJeGxi', 'student', NULL, NULL),
(15, 'SUMITH S SUVARNA', 'botTeach@gmail.com', '$2y$10$UvYZ9pYXj/qa7yFtmLrV7eEikt/fsXq8HTjRWy9O8xhVlE2oHJX1K', 'teacher', NULL, NULL),
(16, 'SUMITH S SUVARNA', 'sumithsuvarna111@gmail.com', '$2y$10$.7kYHR/w.yik3D8cG6zS2uf4KSxUsb8KugFfhdHeVg03ZG8xhpmlC', 'student', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_assignments`
--
ALTER TABLE `student_assignments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `student_assignments`
--
ALTER TABLE `student_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
