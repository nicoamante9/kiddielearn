-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 06, 2026 at 08:35 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kiddielearn`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp(),
  `status` enum('Not Graded','Graded') NOT NULL DEFAULT 'Not Graded'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`id`, `parent_id`, `teacher_id`, `title`, `file_path`, `uploaded_at`, `status`) VALUES
(9, 36, 29, 'Shape Matching Type', 'uploads/worksheets/painted_1765541992_26.png', '2025-12-12 20:20:05', 'Not Graded'),
(10, 36, 29, '123', 'uploads/worksheets/painted_1765542105_25.png', '2025-12-12 20:21:51', 'Graded');

-- --------------------------------------------------------

--
-- Table structure for table `children`
--

CREATE TABLE `children` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `age` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `children`
--

INSERT INTO `children` (`id`, `parent_id`, `first_name`, `last_name`, `age`) VALUES
(17, 35, 'Chrisitian', 'Sagun', 5),
(18, 36, 'Erika', 'Llarena', 4),
(19, 37, 'Leigh', 'Carsocho', 6),
(20, 38, 'Aira', 'Galang', 5),
(21, 39, 'Angel', 'Santos', 6),
(22, 40, 'Jacob', 'Reyes', 5),
(23, 41, 'Bianca', 'Villanueva', 7),
(24, 42, 'Liam', 'Diaz', 4),
(25, 43, 'Sofia', 'Lim', 5),
(26, 44, 'Nathan', 'Rivera', 6),
(27, 45, 'Ava', 'Flores', 5),
(28, 46, 'Ethan', 'De Leon', 4),
(29, 47, 'Chloe', 'Torres', 6),
(30, 48, 'Patricia', 'Cruz', 6),
(31, 49, 'Daniela', 'Mendoza', 5),
(32, 51, 'Leboy', 'Works', 5);

-- --------------------------------------------------------

--
-- Table structure for table `graded_activities`
--

CREATE TABLE `graded_activities` (
  `id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `grade` varchar(5) NOT NULL,
  `comment` text DEFAULT NULL,
  `graded_at` datetime NOT NULL DEFAULT current_timestamp(),
  `graded_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `graded_activities`
--

INSERT INTO `graded_activities` (`id`, `activity_id`, `grade`, `comment`, `graded_at`, `graded_by`) VALUES
(1, 10, 'A+', 'WOW', '2025-12-12 20:40:14', 29),
(2, 10, 'A', 'good', '2025-12-12 20:49:03', 29),
(3, 10, 'A+', 'NICE', '2025-12-12 21:02:08', 29),
(4, 10, 'A+', 'WOW', '2025-12-12 21:02:42', 29);

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `lesson_name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`id`, `teacher_id`, `lesson_name`, `created_at`) VALUES
(1, 29, 'Animal Sounds', '2025-11-23 10:22:54'),
(14, 29, 'Learn Alphabet', '2025-11-23 13:29:18'),
(16, 29, 'Learn Numbers', '2025-11-24 12:47:17'),
(17, 29, 'Learn Shapes', '2025-11-24 12:48:07'),
(19, 29, 'Body Parts', '2025-11-24 13:05:16'),
(22, 29, 'Learn Colors', '2025-12-10 15:33:38');

-- --------------------------------------------------------

--
-- Table structure for table `lesson_items`
--

CREATE TABLE `lesson_items` (
  `id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `audio_path` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lesson_items`
--

INSERT INTO `lesson_items` (`id`, `lesson_id`, `title`, `image_path`, `audio_path`, `created_at`) VALUES
(10, 14, 'A', 'uploads_lessons/lesson_items/1763876280_A.jpg', 'uploads_lessons/lesson_items/1765532107_A.mp3', '2025-11-23 13:38:00'),
(18, 14, 'B', 'uploads_lessons/lesson_items/1763943553_B.jpg', NULL, '2025-11-24 08:19:13'),
(19, 14, 'C', 'uploads_lessons/lesson_items/1763943560_C.jpg', NULL, '2025-11-24 08:19:20'),
(21, 14, 'D', 'uploads_lessons/lesson_items/1763943642_D.jpg', NULL, '2025-11-24 08:20:42'),
(22, 14, 'E', 'uploads_lessons/lesson_items/1763943652_E.jpg', NULL, '2025-11-24 08:20:52'),
(23, 14, 'F', 'uploads_lessons/lesson_items/1763943661_F.jpg', NULL, '2025-11-24 08:21:01'),
(24, 16, '1', 'uploads_lessons/lesson_items/1763959667_1.jpg', NULL, '2025-11-24 12:47:47'),
(25, 17, 'Heart', 'uploads_lessons/lesson_items/1763959763_Heart.jpg', NULL, '2025-11-24 12:49:23'),
(26, 1, 'Dog', 'uploads_lessons/lesson_items/1763960366_dog.jpg', 'uploads_lessons/lesson_items/1763960366_free-dog-bark-419014.mp3', '2025-11-24 12:59:26'),
(27, 1, 'Cat', 'uploads_lessons/lesson_items/1763960407_catsss.jpg', 'uploads_lessons/lesson_items/1763960407_cat-meow-401729.mp3', '2025-11-24 13:00:07'),
(28, 1, 'Lion', 'uploads_lessons/lesson_items/1763960459_lion.jpg', NULL, '2025-11-24 13:00:59'),
(29, 1, 'Bird', 'uploads_lessons/lesson_items/1763960651_bird.jpg', 'uploads_lessons/lesson_items/1763960651_bird-sound-370342.mp3', '2025-11-24 13:04:11'),
(30, 1, 'Monkey', 'uploads_lessons/lesson_items/1763960677_monkey.jpg', 'uploads_lessons/lesson_items/1763960677_monkey-128368.mp3', '2025-11-24 13:04:37'),
(31, 19, 'Mouth', 'uploads_lessons/lesson_items/1763960769_mouth.jpg', NULL, '2025-11-24 13:06:09'),
(32, 14, 'G', 'uploads_lessons/lesson_items/1765350363_G.jpg', NULL, '2025-12-10 15:06:03'),
(33, 14, 'H', 'uploads_lessons/lesson_items/1765350371_H.jpg', NULL, '2025-12-10 15:06:11'),
(34, 14, 'I', 'uploads_lessons/lesson_items/1765350380_I.jpg', NULL, '2025-12-10 15:06:20'),
(35, 14, 'J', 'uploads_lessons/lesson_items/1765350387_J.jpg', NULL, '2025-12-10 15:06:27'),
(36, 14, 'K', 'uploads_lessons/lesson_items/1765350400_K.jpg', NULL, '2025-12-10 15:06:40'),
(37, 14, 'L', 'uploads_lessons/lesson_items/1765350412_L.jpg', NULL, '2025-12-10 15:06:52'),
(38, 14, 'M', 'uploads_lessons/lesson_items/1765350418_M.jpg', NULL, '2025-12-10 15:06:58'),
(39, 14, 'N', 'uploads_lessons/lesson_items/1765350424_N.jpg', NULL, '2025-12-10 15:07:04'),
(40, 14, 'O', 'uploads_lessons/lesson_items/1765350431_O.jpg', NULL, '2025-12-10 15:07:11'),
(41, 14, 'P', 'uploads_lessons/lesson_items/1765350509_P.jpg', NULL, '2025-12-10 15:08:29'),
(42, 14, 'Q', 'uploads_lessons/lesson_items/1765350517_Q.jpg', NULL, '2025-12-10 15:08:37'),
(43, 14, 'R', 'uploads_lessons/lesson_items/1765350524_R.jpg', NULL, '2025-12-10 15:08:44'),
(44, 14, 'S', 'uploads_lessons/lesson_items/1765350530_S.jpg', NULL, '2025-12-10 15:08:50'),
(45, 14, 'T', 'uploads_lessons/lesson_items/1765350538_T.jpg', NULL, '2025-12-10 15:08:58'),
(46, 14, 'U', 'uploads_lessons/lesson_items/1765350547_U.jpg', NULL, '2025-12-10 15:09:07'),
(47, 14, 'V', 'uploads_lessons/lesson_items/1765350556_V.jpg', NULL, '2025-12-10 15:09:16'),
(48, 14, 'W', 'uploads_lessons/lesson_items/1765350564_W.jpg', NULL, '2025-12-10 15:09:24'),
(49, 14, 'X', 'uploads_lessons/lesson_items/1765350572_X.jpg', NULL, '2025-12-10 15:09:32'),
(50, 14, 'Y', 'uploads_lessons/lesson_items/1765350583_Y.jpg', NULL, '2025-12-10 15:09:43'),
(51, 14, 'Z', 'uploads_lessons/lesson_items/1765350592_Z.jpg', NULL, '2025-12-10 15:09:52'),
(52, 17, 'Circle', 'uploads_lessons/lesson_items/1765350627_Circle.jpg', NULL, '2025-12-10 15:10:27'),
(53, 17, 'Diamond', 'uploads_lessons/lesson_items/1765350638_Diamond.jpg', NULL, '2025-12-10 15:10:38'),
(54, 17, 'Oval', 'uploads_lessons/lesson_items/1765350647_Oval.jpg', NULL, '2025-12-10 15:10:47'),
(55, 17, 'Rectangle', 'uploads_lessons/lesson_items/1765350660_Rectangle.jpg', NULL, '2025-12-10 15:11:00'),
(56, 17, 'Square', 'uploads_lessons/lesson_items/1765350674_Square.jpg', NULL, '2025-12-10 15:11:14'),
(57, 17, 'Star', 'uploads_lessons/lesson_items/1765350681_Star.jpg', NULL, '2025-12-10 15:11:21'),
(58, 17, 'Triangle', 'uploads_lessons/lesson_items/1765350691_Triangle.jpg', NULL, '2025-12-10 15:11:31'),
(59, 16, '2', 'uploads_lessons/lesson_items/1765350713_2.jpg', NULL, '2025-12-10 15:11:53'),
(60, 16, '3', 'uploads_lessons/lesson_items/1765350719_3.jpg', NULL, '2025-12-10 15:11:59'),
(61, 16, '4', 'uploads_lessons/lesson_items/1765350726_4.jpg', NULL, '2025-12-10 15:12:06'),
(62, 16, '5', 'uploads_lessons/lesson_items/1765350734_5.jpg', NULL, '2025-12-10 15:12:14'),
(63, 16, '6', 'uploads_lessons/lesson_items/1765350742_6.jpg', NULL, '2025-12-10 15:12:22'),
(64, 16, '7', 'uploads_lessons/lesson_items/1765350750_7.jpg', NULL, '2025-12-10 15:12:30'),
(65, 16, '8', 'uploads_lessons/lesson_items/1765350758_8.jpg', NULL, '2025-12-10 15:12:38'),
(66, 16, '9', 'uploads_lessons/lesson_items/1765350766_9.jpg', NULL, '2025-12-10 15:12:46'),
(67, 16, '10', 'uploads_lessons/lesson_items/1765350774_10.jpg', NULL, '2025-12-10 15:12:54');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `sent_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `is_read`, `sent_at`) VALUES
(77, 29, 35, 'Hi', 1, '2025-12-10 15:03:43'),
(78, 29, 35, 'Hello', 1, '2025-12-12 17:36:29'),
(79, 35, 29, 'OK', 1, '2025-12-12 17:38:18'),
(80, 29, 35, 'okay', 1, '2025-12-12 17:39:55'),
(81, 29, 35, 'gege', 0, '2025-12-12 17:41:30'),
(82, 29, 35, 'Hi duyz', 0, '2025-12-12 20:27:15'),
(83, 29, 36, 'Hi', 0, '2025-12-12 20:27:27');

-- --------------------------------------------------------

--
-- Table structure for table `progress`
--

CREATE TABLE `progress` (
  `id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `topic` varchar(50) NOT NULL,
  `grade` varchar(5) NOT NULL,
  `graded_at` datetime DEFAULT current_timestamp(),
  `graded_by` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `progress`
--

INSERT INTO `progress` (`id`, `child_id`, `topic`, `grade`, `graded_at`, `graded_by`, `comment`) VALUES
(16, 17, 'Learn Alphabet', 'A', '2025-12-10 15:13:45', 29, 'Very Good!!!'),
(17, 18, 'Animal Sounds', 'A+', '2025-12-12 17:45:38', 29, 'good');

-- --------------------------------------------------------

--
-- Table structure for table `student_activities`
--

CREATE TABLE `student_activities` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','teacher','parent') DEFAULT 'parent',
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `reset_token`, `reset_token_expires`, `email`, `first_name`, `last_name`) VALUES
(29, 'dominicamante', '$2y$10$T/HtLrwHqlhgb0DLQr9NJOlX6hH6zAPZmaJRI2c39x2PcbEGWsqsm', 'teacher', NULL, NULL, 'nicoamante90@gmail.com', 'Dominic', 'Amante'),
(35, 'Parent1', '$2y$10$D.LO6CN34q4QtXc1TSSLcORWnHqH/LIkgfLWYajVgLUWLu8Gdlhsa', 'parent', NULL, NULL, 'example@gmail.com', 'Daniel', 'Sagun'),
(36, 'Parent2', '$2y$10$gBIA/2PubG1u2ti4n.fw1.fMmqdC/1.g6GbnRZi.l4cWGJVlgSXv2', 'parent', NULL, NULL, 'example@gmail.com', 'Bea', 'Llarena'),
(37, 'Parent3', '$2y$10$6tTgPVT4AhxRQWQKRIVHGOXD7S2lPxn/QKhhQnDwhRQheuwV22mVu', 'parent', NULL, NULL, 'example@gmail.com', 'Mark Justin', 'Carsocho'),
(38, 'Parent4', '$2y$10$IzIDUFH3P.4hH/vz/ZSShepvhfbH/RFA1WIVfaJ.SyXcsoNW0WxE.', 'parent', NULL, NULL, 'example@gmail.com', 'Mae', 'Galang'),
(39, 'Parent5', '$2y$10$TuF2LavOhCJwzdO1HfUaPuyrGlmDVkN3E6dzvAQGG9uUczUtnOdhu', 'parent', NULL, NULL, 'maria.santos@example.com', 'Maria', 'Santos'),
(40, 'Parent6', '$2y$10$fttxEwb2upUKsBXnFEPF2uGvh3O05VW/qeTwx.oasbBt9VhQcLcGu', 'parent', NULL, NULL, 'example@gmail.com', 'John', 'Reyes'),
(41, 'Parent7', '$2y$10$wmHnLOap3BiPIcWIsIeFeOp4iy4aZVpKWFttXw7KkZWRK79JybSM2', 'parent', NULL, NULL, 'example@gmail.com', 'Lorna', 'Villanueva'),
(42, 'Parent8', '$2y$10$NZrHp4yodn6Cv6hiOgdwTe.KNL33p2thS2Ig0gSsIqNcz4GptQD6C', 'parent', NULL, NULL, 'example@gmail.com', 'Paolo', 'Diaz'),
(43, 'Parent9', '$2y$10$3C8fMwzvLsIyZ9cIVM8p5Oe6BOsIefLA1Ib2KuiPYEXaSnggR0qI6', 'parent', NULL, NULL, 'example@gmail.com', 'Cath', 'Lim'),
(44, 'Parent10', '$2y$10$ZiTXSHYv61jIk.EhR.0KiuoB3IiZobnaBnFJNcHaZYfNmJ4v4rrkK', 'parent', NULL, NULL, 'example@gmail.com', 'Mark', 'Rivera'),
(45, 'Parent11', '$2y$10$qcBkKP8CfK/HdHCfMSZKYu2ph4j7LbwjD9yT96z0pKgqmMWvXctKW', 'parent', NULL, NULL, 'example@gmail.com', 'Jasmine', 'Flores'),
(46, 'Parent12', '$2y$10$oliKrHLtHyD0JSs1dBL6a.EWlXpJhfMnfDDdk8jvLgvgRDicy4NnO', 'parent', NULL, NULL, 'example@gmail.com', 'Ryan', 'De Leon'),
(47, 'Parent13', '$2y$10$/bpJ2Wdjdc7T6x4gjiJKn.Q8Szro8JHD/eu7xdgSQ3SZ2ZwbZy0k.', 'parent', NULL, NULL, 'example@gmail.com', 'Grace', 'Torres'),
(48, 'Parent14', '$2y$10$4g/SdroKVEXkajOrcN2QIuQWG4t9bKVHlInxxiSqXd7ArRNfmZt.m', 'parent', NULL, NULL, 'example@gmail.com', 'Joseph', 'Cruz'),
(49, 'Parent15', '$2y$10$aXREkyiOWqKg6Klg/EG2cegz8mozlScYPdtPyBVwPZvce9XAlMZE2', 'parent', NULL, NULL, 'example@gmail.com', 'Elaine', 'Mendoza'),
(51, 'borns2002', '$2y$10$9TfLHTRUq1SOedGsaC7tdeUm6g79SSxZSzDt5wYvEhYz.ezh5M9tm', 'parent', NULL, NULL, 'nicoamante9@gmail.com', 'Dom', 'Amante');

-- --------------------------------------------------------

--
-- Table structure for table `weeks`
--

CREATE TABLE `weeks` (
  `id` int(11) NOT NULL,
  `week_name` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `weeks`
--

INSERT INTO `weeks` (`id`, `week_name`, `created_at`) VALUES
(1, 'Week 1', '2025-12-10 14:27:09'),
(2, 'Week 2', '2025-12-10 14:27:33'),
(3, 'Week 3', '2025-12-10 14:28:36'),
(4, 'Week 4', '2025-12-10 14:35:43');

-- --------------------------------------------------------

--
-- Table structure for table `worksheets`
--

CREATE TABLE `worksheets` (
  `id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `week` varchar(20) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` datetime NOT NULL,
  `feedback` text DEFAULT NULL,
  `painted_file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `worksheets`
--

INSERT INTO `worksheets` (`id`, `child_id`, `title`, `week`, `file_path`, `uploaded_at`, `feedback`, `painted_file_path`) VALUES
(13, 18, 'TRACING', 'Week 2', '1765533722_finger.jpg', '2025-12-12 18:02:02', 'sample', NULL),
(25, 18, 'Matching Type', 'Week 1', '1765541931_6491_parts.jpg', '2025-12-12 20:18:51', 'Please answer the Activity, Good Luck', 'uploads/worksheets/painted_1765545799_25.png'),
(26, 18, 'Matching Type', 'Week 1', '1765541931_1473_5c340c42cf1610b58904b3bce3646a6e.jpg', '2025-12-12 20:18:51', 'Please answer the Activity, Good Luck', 'uploads/worksheets/painted_1765541992_26.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `children`
--
ALTER TABLE `children`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `graded_activities`
--
ALTER TABLE `graded_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_id` (`activity_id`),
  ADD KEY `graded_by` (`graded_by`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `lesson_items`
--
ALTER TABLE `lesson_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lesson_id` (`lesson_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `progress`
--
ALTER TABLE `progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `child_id` (`child_id`),
  ADD KEY `fk_graded_by` (`graded_by`);

--
-- Indexes for table `student_activities`
--
ALTER TABLE `student_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `weeks`
--
ALTER TABLE `weeks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `worksheets`
--
ALTER TABLE `worksheets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `child_id` (`child_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `children`
--
ALTER TABLE `children`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `graded_activities`
--
ALTER TABLE `graded_activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `lesson_items`
--
ALTER TABLE `lesson_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `progress`
--
ALTER TABLE `progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `student_activities`
--
ALTER TABLE `student_activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `weeks`
--
ALTER TABLE `weeks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `worksheets`
--
ALTER TABLE `worksheets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `activities_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `children`
--
ALTER TABLE `children`
  ADD CONSTRAINT `children_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `graded_activities`
--
ALTER TABLE `graded_activities`
  ADD CONSTRAINT `graded_activities_ibfk_1` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `graded_activities_ibfk_2` FOREIGN KEY (`graded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `lessons_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lesson_items`
--
ALTER TABLE `lesson_items`
  ADD CONSTRAINT `lesson_items_ibfk_1` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `progress`
--
ALTER TABLE `progress`
  ADD CONSTRAINT `fk_graded_by` FOREIGN KEY (`graded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `progress_ibfk_1` FOREIGN KEY (`child_id`) REFERENCES `children` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_activities`
--
ALTER TABLE `student_activities`
  ADD CONSTRAINT `student_activities_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_activities_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `worksheets`
--
ALTER TABLE `worksheets`
  ADD CONSTRAINT `worksheets_ibfk_1` FOREIGN KEY (`child_id`) REFERENCES `children` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
