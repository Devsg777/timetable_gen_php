-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 15, 2025 at 06:14 AM
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
-- Database: `timetable_gen`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`) VALUES
(1, 'Admin', 'admin@gmail.com', '$2y$10$o.y77qJGdlfQqPWzWJ8WqOW08UXKzIVyS6vQdxgdgVYmnZRBszUXq');

-- --------------------------------------------------------

--
-- Table structure for table `classrooms`
--

CREATE TABLE `classrooms` (
  `id` int(11) NOT NULL,
  `room_no` varchar(10) NOT NULL,
  `type` enum('theory','lab') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classrooms`
--

INSERT INTO `classrooms` (`id`, `room_no`, `type`) VALUES
(1, 'Digital LA', 'lab'),
(2, '2', 'theory'),
(3, '5', 'theory'),
(6, '4', 'theory'),
(7, '3', 'theory'),
(8, '44', 'lab');

-- --------------------------------------------------------

--
-- Table structure for table `combinations`
--

CREATE TABLE `combinations` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `semester` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `combinations`
--

INSERT INTO `combinations` (`id`, `name`, `department`, `semester`) VALUES
(6, 'BCA', 'Computer Applications', 1),
(7, 'BCA', 'Computer Applications', 2),
(8, 'BCA', 'Computer Applications', 3),
(9, 'BCA', 'Computer Applications', 4),
(10, 'BCA', 'Computer Applications', 5),
(11, 'BCA', 'Computer Applications', 6),
(12, 'PCM', 'Science', 1),
(13, 'PCM', 'Science', 2),
(14, 'PCM', 'Science', 3),
(15, 'PCM', 'Science', 4),
(16, 'PCM', 'Science', 5),
(17, 'PCM', 'Science', 6),
(18, 'CBZ', 'Science', 1),
(19, 'CBZ', 'Science', 2),
(20, 'CBZ', 'Science', 3),
(21, 'CBZ', 'Science', 4),
(22, 'CBZ', 'Science', 5),
(23, 'CBZ', 'Science', 6),
(24, 'HES', 'Arts', 1),
(25, 'HES', 'Arts', 2),
(26, 'HES', 'Arts', 3),
(27, 'HES', 'Arts', 4),
(28, 'HES', 'Arts', 5),
(29, 'HES', 'Arts', 6),
(30, 'KES', 'Arts', 1),
(31, 'KES', 'Arts', 2),
(32, 'KES', 'Arts', 3),
(33, 'KES', 'Arts', 4),
(34, 'KES', 'Arts', 5),
(35, 'KES', 'Arts', 6),
(36, 'B.Com', 'Commerce', 1),
(37, 'B.Com', 'Commerce', 2),
(38, 'B.Com', 'Commerce', 3),
(39, 'B.Com', 'Commerce', 4),
(41, 'B.Com', 'Commerce', 6),
(43, 'CBZT', 'Bio', 1),
(44, 'CBZT', 'Bio', 1),
(45, 'BMBT', 'Biology', 1);

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` int(225) NOT NULL,
  `requester_id` int(225) NOT NULL,
  `entry_id` int(225) NOT NULL,
  `request_type` varchar(225) NOT NULL,
  `description` varchar(225) NOT NULL,
  `requester` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `combination_id` int(11) NOT NULL,
  `phone_no` varchar(15) NOT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `email`, `password`, `combination_id`, `phone_no`, `address`) VALUES
(2, 'Dev S G', 'devsg777@gmail.com', '$2y$10$rK0c8uJOqgRKFCbCc.6xGeNTczHTtYK.a4qwEfvywqUXkYr7bIRvq', 6, '09141189941', 'hassan');

-- --------------------------------------------------------

--
-- Table structure for table `student_timetable`
--

CREATE TABLE `student_timetable` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `timetable_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `min_classes_per_week` int(11) NOT NULL,
  `type` enum('theory','lab') NOT NULL,
  `combination_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `min_classes_per_week`, `type`, `combination_id`) VALUES
(12, 'DBMS', 3, 'theory', 9),
(13, 'Operating System', 3, 'theory', 9),
(14, 'Mathematics', 3, 'theory', 9),
(15, 'Biology', 3, 'theory', 19),
(16, 'Zoology', 3, 'theory', 19),
(17, 'Botany', 3, 'theory', 19),
(18, 'History', 3, 'theory', 19),
(19, 'Economics', 3, 'theory', 19),
(20, 'Sociology', 3, 'theory', 19),
(21, 'Political Science', 3, 'theory', 41),
(22, 'Economics', 3, 'theory', 41),
(23, 'Statistics', 3, 'theory', 41),
(24, 'Accountancy Lab', 4, 'lab', 41),
(25, 'Financial Management', 4, 'theory', 41),
(26, 'Business Law', 4, 'theory', 41),
(28, 'History', 4, 'theory', 41),
(29, 'PHP Lab', 2, 'lab', 9),
(30, 'Java Programming Lab', 2, 'lab', 9),
(31, 'Botany Lab', 2, 'lab', 19),
(32, 'Web Technology', 4, 'theory', 9),
(33, 'Java Theory', 3, 'theory', 9),
(34, 'PHP Theory', 4, 'theory', 9);

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_no` varchar(15) NOT NULL,
  `address` text DEFAULT NULL,
  `min_class_hours_week` int(11) NOT NULL,
  `min_lab_hours_week` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `name`, `department`, `email`, `password`, `phone_no`, `address`, `min_class_hours_week`, `min_lab_hours_week`) VALUES
(3, 'Dr. Ramesh', 'Computer Applications', 'ramesh@college.com', 'password', '9876543210', 'Bangalore', 6, 2),
(4, 'Prof. Aditi', 'BSc', 'aditi@college.com', 'password', '9876543211', 'Mysore', 6, 2),
(5, 'Dr. Sameer', 'Physics', 'sameer@college.com', 'password', '9876543212', 'Hubli', 6, 2),
(6, 'Prof. Madan Lal', 'Chemistry', 'madanalal@gmail.com', '$2y$10$xtpbm1d3KDGHWw7Z9MDqz.D5cAVYC/Rqa7gckp2P3aFRa1DGxBFfm', '1234567890', 'No.74 17th main  sahukar chanaiyah road Saraswathipuram mysore\r\nMysore', 4, 2),
(7, 'Pro. Prashant Neel', 'Commerce', 'prashant@gmail.com', '$2y$10$JJ9onm75bI6TRIS0zQwVXuNU2k7vnbav1odBBTI3dH0cqe768sZha', '1234567890', 'Arakalagud Hassan', 4, 0),
(8, 'Dr. Sudha Shetty ', 'BCA', 'sudha@gmail.com', '$2y$10$cfmbZ1U5cg4QRKZ3Zv6YieRk.R1yzbfUeADvh/TI9QAVgfEB4veHu', '9383734345', 'Bengaluru', 5, 1),
(9, 'Mis. Prakruthi ', 'Commerce', 'prakruthi@gmail.com', '$2y$10$ThTNS4v6bz/gz7FrgYrNn.ostjSsI.jXa3PCcBRibuump4v3TRB2G', '9876378374', 'Hassan, Karnataka', 6, 0),
(10, 'Prof. Suchitra ', 'Commerce', 'suchitra@gmail.com', '$2y$10$0Qkzo4ce7zR98bu2YJnrgedeffsNkrlqS2k1oem8jr/srn5za0afG', '9876378374', 'Hassan', 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_subjects`
--

CREATE TABLE `teacher_subjects` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_subjects`
--

INSERT INTO `teacher_subjects` (`id`, `teacher_id`, `subject_id`) VALUES
(4, 5, 26),
(6, 9, 24),
(7, 9, 23),
(8, 8, 22),
(9, 7, 28),
(11, 4, 29),
(13, 4, 34),
(15, 10, 33),
(16, 10, 30),
(17, 6, 12),
(18, 3, 13);

-- --------------------------------------------------------

--
-- Table structure for table `timetable`
--

CREATE TABLE `timetable` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `classroom_id` int(11) NOT NULL,
  `day` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `combination_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timetable`
--

INSERT INTO `timetable` (`id`, `subject_id`, `teacher_id`, `classroom_id`, `day`, `start_time`, `end_time`, `combination_id`) VALUES
(1463, 29, 4, 8, 'Tuesday', '02:00:00', '05:00:00', 9),
(1464, 29, 4, 1, 'Friday', '10:00:00', '13:00:00', 9),
(1465, 30, 10, 1, 'Saturday', '10:00:00', '13:00:00', 9),
(1466, 28, 7, 3, 'Monday', '10:00:00', '11:00:00', 41),
(1467, 26, 5, 7, 'Wednesday', '04:00:00', '05:00:00', 41),
(1468, 12, 6, 2, 'Friday', '04:00:00', '05:00:00', 9),
(1469, 13, 3, 2, 'Tuesday', '04:00:00', '05:00:00', 9),
(1470, 33, 10, 3, 'Wednesday', '11:00:00', '12:00:00', 9),
(1471, 12, 6, 6, 'Thursday', '04:00:00', '05:00:00', 9),
(1472, 26, 5, 2, 'Monday', '12:00:00', '13:00:00', 41),
(1473, 23, 9, 3, 'Thursday', '10:00:00', '11:00:00', 41),
(1474, 22, 8, 7, 'Tuesday', '12:00:00', '13:00:00', 41),
(1475, 30, 10, 8, 'Tuesday', '10:00:00', '13:00:00', 9),
(1476, 12, 6, 7, 'Saturday', '03:00:00', '04:00:00', 9),
(1477, 33, 10, 6, 'Tuesday', '12:00:00', '13:00:00', 9),
(1478, 13, 3, 7, 'Wednesday', '12:00:00', '13:00:00', 9),
(1479, 28, 7, 7, 'Monday', '02:00:00', '03:00:00', 41),
(1480, 23, 9, 7, 'Tuesday', '03:00:00', '04:00:00', 41),
(1481, 24, 9, 8, 'Thursday', '02:00:00', '05:00:00', 41),
(1482, 34, 4, 3, 'Monday', '04:00:00', '05:00:00', 9),
(1483, 22, 8, 2, 'Thursday', '12:00:00', '13:00:00', 41),
(1484, 26, 5, 3, 'Wednesday', '02:00:00', '03:00:00', 41),
(1485, 23, 9, 2, 'Friday', '10:00:00', '11:00:00', 41),
(1486, 34, 4, 7, 'Wednesday', '10:00:00', '11:00:00', 9),
(1487, 33, 10, 7, 'Tuesday', '11:00:00', '12:00:00', 9),
(1488, 26, 5, 6, 'Thursday', '11:00:00', '12:00:00', 41),
(1489, 28, 7, 6, 'Thursday', '03:00:00', '04:00:00', 41),
(1490, 24, 9, 1, 'Tuesday', '02:00:00', '05:00:00', 41),
(1491, 22, 8, 6, 'Wednesday', '10:00:00', '11:00:00', 41),
(1492, 13, 3, 6, 'Tuesday', '03:00:00', '04:00:00', 9),
(1493, 34, 4, 6, 'Monday', '02:00:00', '03:00:00', 9),
(1494, 34, 4, 2, 'Saturday', '04:00:00', '05:00:00', 9),
(1495, 28, 7, 2, 'Saturday', '10:00:00', '11:00:00', 41);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `classrooms`
--
ALTER TABLE `classrooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_no` (`room_no`);

--
-- Indexes for table `combinations`
--
ALTER TABLE `combinations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `requester_id` (`requester_id`),
  ADD KEY `entry_id` (`entry_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `combination_id` (`combination_id`);

--
-- Indexes for table `student_timetable`
--
ALTER TABLE `student_timetable`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `timetable_id` (`timetable_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `combination_id` (`combination_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `timetable`
--
ALTER TABLE `timetable`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `classroom_id` (`classroom_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `classrooms`
--
ALTER TABLE `classrooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `combinations`
--
ALTER TABLE `combinations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(225) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `student_timetable`
--
ALTER TABLE `student_timetable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `timetable`
--
ALTER TABLE `timetable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1496;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`requester_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `requests_ibfk_2` FOREIGN KEY (`requester_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `requests_ibfk_3` FOREIGN KEY (`entry_id`) REFERENCES `timetable` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`combination_id`) REFERENCES `combinations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_timetable`
--
ALTER TABLE `student_timetable`
  ADD CONSTRAINT `student_timetable_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_timetable_ibfk_2` FOREIGN KEY (`timetable_id`) REFERENCES `timetable` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`combination_id`) REFERENCES `combinations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  ADD CONSTRAINT `teacher_subjects_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teacher_subjects_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `timetable`
--
ALTER TABLE `timetable`
  ADD CONSTRAINT `timetable_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `timetable_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `timetable_ibfk_3` FOREIGN KEY (`classroom_id`) REFERENCES `classrooms` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
