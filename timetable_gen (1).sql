-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2025 at 03:37 AM
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
(1, 'Lab-4', 'lab'),
(2, '2', 'theory'),
(3, '5', 'theory'),
(6, '4', 'theory'),
(7, '3', 'theory'),
(8, 'Lab-3', 'lab'),
(9, 'C-Lab 1', 'lab'),
(10, 'C-Lab-2', 'lab'),
(11, '7', 'theory'),
(12, '10', 'theory'),
(13, '11', 'theory'),
(14, '1', 'theory'),
(15, '6', 'theory'),
(16, '12', 'theory'),
(17, '8', 'theory'),
(18, '9', 'theory');

-- --------------------------------------------------------

--
-- Table structure for table `combinations`
--

CREATE TABLE `combinations` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `semester` int(11) NOT NULL,
  `sections` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sections`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `combinations`
--

INSERT INTO `combinations` (`id`, `name`, `department`, `semester`, `sections`) VALUES
(47, 'Bcom', 'Commerce', 6, '[\"A\"]'),
(48, 'BCA', 'BCA', 6, '[\"A\",\"B\"]'),
(50, 'BSc', 'computer science', 6, '[\"A\",\"B\",\"C\"]');

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` int(225) NOT NULL,
  `requester_type` enum('teacher','student') NOT NULL,
  `teacher_id` int(225) DEFAULT NULL,
  `student_id` int(225) DEFAULT NULL,
  `request_type` enum('class_change') NOT NULL DEFAULT 'class_change',
  `existing_timetable_id` int(11) NOT NULL,
  `proposed_subject_id` int(11) DEFAULT NULL,
  `proposed_teacher_id` int(11) DEFAULT NULL,
  `proposed_classroom_id` int(11) DEFAULT NULL,
  `proposed_day` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') DEFAULT NULL,
  `proposed_start_time` time DEFAULT NULL,
  `proposed_end_time` time DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `request_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`id`, `requester_type`, `teacher_id`, `student_id`, `request_type`, `existing_timetable_id`, `proposed_subject_id`, `proposed_teacher_id`, `proposed_classroom_id`, `proposed_day`, `proposed_start_time`, `proposed_end_time`, `reason`, `request_date`, `status_id`) VALUES
(9, 'teacher', 12, NULL, 'class_change', 5927, 50, 10, 13, 'Tuesday', '10:00:00', '11:00:00', 'i cannot come college early', '2025-05-07 06:47:02', 1);

-- --------------------------------------------------------

--
-- Table structure for table `request_statuses`
--

CREATE TABLE `request_statuses` (
  `id` int(11) NOT NULL,
  `status_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `request_statuses`
--

INSERT INTO `request_statuses` (`id`, `status_name`) VALUES
(3, 'Admitted'),
(4, 'Cancelled'),
(1, 'Pending'),
(2, 'Received');

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
  `section` varchar(225) NOT NULL,
  `phone_no` varchar(15) NOT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `email`, `password`, `combination_id`, `section`, `phone_no`, `address`) VALUES
(3, 'Dev S G', 'devsg777@gmail.com', '$2y$10$5ewWe92B8wo04MzoH01sHObxX38OotrxDSc.Gv5Nx4WdG8EHa5VDu', 48, 'A', '09141189941', 'Hassan');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `min_classes_per_week` int(11) NOT NULL,
  `type` enum('theory','lab') NOT NULL,
  `duration` int(11) UNSIGNED DEFAULT NULL,
  `combination_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `min_classes_per_week`, `type`, `duration`, `combination_id`) VALUES
(43, 'Linux Lab', 1, 'lab', 3, 48),
(44, 'Cyber Security', 3, 'theory', 1, 48),
(45, 'Advance Networking', 4, 'theory', 1, 48),
(46, 'Java Programming ', 4, 'theory', 1, 48),
(47, 'Web Technology', 5, 'theory', 1, 48),
(48, 'PHP Lab', 1, 'lab', 3, 48),
(49, 'PHP Theory', 4, 'theory', 1, 48),
(50, 'Operating System', 3, 'theory', 1, 48),
(51, 'computer network ', 2, 'theory', 1, 50),
(52, 'c', 6, 'theory', 1, 50);

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
(7, 'Pro. Prashant Neel', '', '', '$2y$10$bQ1k6SDonyXjT0zTTJCs1OPFKv.sJ3YqGIV3m5XBvM971q0mct2YO', '12345678901', 'Arakalagud Hassan', 6, 0),
(8, 'Dr. Sudha Shetty ', 'BCA', 'sudha@gmail.com', '$2y$10$cfmbZ1U5cg4QRKZ3Zv6YieRk.R1yzbfUeADvh/TI9QAVgfEB4veHu', '9383734345', 'Bengaluru', 10, 1),
(9, 'Mis. Prakruthi ', 'BCA', 'prakruthi@gmail.com', '$2y$10$ThTNS4v6bz/gz7FrgYrNn.ostjSsI.jXa3PCcBRibuump4v3TRB2G', '9876378374', 'Hassan, Karnataka', 8, 2),
(10, 'Prof. Suchitra ', 'BCA', 'suchitra@gmail.com', '$2y$10$0Qkzo4ce7zR98bu2YJnrgedeffsNkrlqS2k1oem8jr/srn5za0afG', '9876378374', 'Hassan', 10, 2),
(11, 'Elon musk', 'business department', 'Elonmusk143@gmail.com', '$2y$10$/HH6RmclFZFwSx8VEWV3YuId1OzoHOpnCHPP2NKuOfNOBfY9aCitC', '9900445566', 'mumbai', 1, 2),
(12, 'ambani', 'business department', 'ambhani1234@gmail.com', '$2y$10$EGUkmAP0LkmPzP4biKWpx.qsdNMXOqieM0EdwLwH9J/qYh3F3ESHy', '9900445566', 'hassan', 1, 2);

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
(27, 10, 50),
(28, 9, 49),
(29, 8, 48),
(30, 7, 47),
(31, 6, 46),
(32, 5, 45),
(33, 4, 44),
(34, 3, 43),
(35, 12, 52),
(36, 12, 52),
(37, 12, 52);

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
  `combination_id` int(11) NOT NULL,
  `section` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timetable`
--

INSERT INTO `timetable` (`id`, `subject_id`, `teacher_id`, `classroom_id`, `day`, `start_time`, `end_time`, `combination_id`, `section`) VALUES
(5875, 50, 10, 15, 'Monday', '11:00:00', '12:00:00', 48, 'B'),
(5877, 50, 10, 15, 'Monday', '04:00:00', '05:00:00', 48, 'A'),
(5878, 50, 10, 15, 'Monday', '12:00:00', '13:00:00', 48, 'A'),
(5879, 50, 10, 15, 'Monday', '05:00:00', '06:00:00', 48, 'B'),
(5881, 43, 3, 8, 'Tuesday', '09:00:00', '12:00:00', 48, 'B'),
(5882, 47, 9, 3, 'Monday', '11:00:00', '12:00:00', 48, 'A'),
(5883, 47, 7, 7, 'Monday', '03:00:00', '04:00:00', 48, 'B'),
(5884, 47, 7, 7, 'Tuesday', '05:00:00', '06:00:00', 48, 'A'),
(5885, 47, 7, 7, 'Tuesday', '02:00:00', '03:00:00', 48, 'B'),
(5886, 47, 7, 7, 'Tuesday', '12:00:00', '13:00:00', 48, 'B'),
(5887, 47, 7, 7, 'Tuesday', '04:00:00', '05:00:00', 48, 'B'),
(5888, 47, 7, 7, 'Tuesday', '03:00:00', '04:00:00', 48, 'A'),
(5889, 47, 7, 7, 'Wednesday', '11:00:00', '12:00:00', 48, 'A'),
(5890, 47, 7, 7, 'Wednesday', '09:00:00', '10:00:00', 48, 'A'),
(5891, 47, 7, 7, 'Wednesday', '02:00:00', '03:00:00', 48, 'B'),
(5892, 44, 4, 14, 'Tuesday', '03:00:00', '04:00:00', 48, 'B'),
(5893, 44, 4, 14, 'Tuesday', '02:00:00', '03:00:00', 48, 'A'),
(5894, 44, 4, 14, 'Tuesday', '04:00:00', '05:00:00', 48, 'A'),
(5895, 44, 4, 14, 'Wednesday', '10:00:00', '11:00:00', 48, 'A'),
(5896, 44, 4, 14, 'Wednesday', '12:00:00', '13:00:00', 48, 'B'),
(5897, 44, 4, 14, 'Wednesday', '09:00:00', '10:00:00', 48, 'B'),
(5898, 43, 3, 8, 'Thursday', '09:00:00', '12:00:00', 48, 'A'),
(5899, 48, 8, 10, 'Thursday', '09:00:00', '12:00:00', 48, 'B'),
(5900, 49, 9, 18, 'Wednesday', '12:00:00', '13:00:00', 48, 'A'),
(5901, 49, 9, 18, 'Wednesday', '03:00:00', '04:00:00', 48, 'B'),
(5902, 49, 9, 18, 'Thursday', '03:00:00', '04:00:00', 48, 'A'),
(5903, 49, 9, 18, 'Thursday', '02:00:00', '03:00:00', 48, 'B'),
(5904, 49, 9, 18, 'Thursday', '05:00:00', '06:00:00', 48, 'A'),
(5905, 49, 9, 18, 'Thursday', '12:00:00', '13:00:00', 48, 'A'),
(5906, 49, 9, 18, 'Thursday', '04:00:00', '05:00:00', 48, 'B'),
(5907, 49, 9, 18, 'Friday', '10:00:00', '11:00:00', 48, 'B'),
(5908, 45, 5, 11, 'Thursday', '04:00:00', '05:00:00', 48, 'A'),
(5909, 45, 5, 11, 'Thursday', '12:00:00', '13:00:00', 48, 'B'),
(5910, 45, 5, 11, 'Thursday', '03:00:00', '04:00:00', 48, 'B'),
(5911, 45, 5, 11, 'Friday', '03:00:00', '04:00:00', 48, 'B'),
(5912, 45, 5, 11, 'Friday', '05:00:00', '06:00:00', 48, 'B'),
(5913, 45, 5, 11, 'Friday', '10:00:00', '11:00:00', 48, 'A'),
(5914, 45, 5, 11, 'Friday', '04:00:00', '05:00:00', 48, 'A'),
(5915, 45, 5, 11, 'Friday', '09:00:00', '10:00:00', 48, 'A'),
(5916, 46, 6, 15, 'Friday', '05:00:00', '06:00:00', 48, 'A'),
(5917, 46, 6, 15, 'Friday', '11:00:00', '12:00:00', 48, 'B'),
(5918, 46, 6, 15, 'Saturday', '03:00:00', '04:00:00', 48, 'B'),
(5919, 46, 6, 15, 'Saturday', '11:00:00', '12:00:00', 48, 'B'),
(5920, 46, 6, 15, 'Saturday', '12:00:00', '13:00:00', 48, 'A'),
(5921, 45, 9, 10, 'Saturday', '09:00:00', '10:00:00', 48, 'A'),
(5922, 46, 6, 15, 'Saturday', '10:00:00', '11:00:00', 48, 'A'),
(5923, 46, 6, 15, 'Saturday', '05:00:00', '06:00:00', 48, 'B'),
(5924, 48, 7, 9, 'Tuesday', '09:00:00', '11:00:00', 48, 'A'),
(5925, 45, 9, 7, 'Saturday', '02:00:00', '04:00:00', 48, 'A'),
(5926, 45, 9, 7, 'Saturday', '04:00:00', '06:00:00', 48, 'A'),
(5927, 52, 12, 11, 'Monday', '09:00:00', '10:00:00', 48, 'A'),
(5928, 52, 12, 13, 'Wednesday', '03:00:00', '04:00:00', 48, 'A'),
(5929, 52, 12, 6, 'Tuesday', '11:00:00', '12:00:00', 48, 'A'),
(5930, 52, 12, 16, 'Friday', '11:00:00', '12:00:00', 48, 'A');

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
  ADD KEY `existing_timetable_id` (`existing_timetable_id`),
  ADD KEY `proposed_subject_id` (`proposed_subject_id`),
  ADD KEY `proposed_teacher_id` (`proposed_teacher_id`),
  ADD KEY `proposed_classroom_id` (`proposed_classroom_id`),
  ADD KEY `status_id` (`status_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `request_statuses`
--
ALTER TABLE `request_statuses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `status_name` (`status_name`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `combination_id` (`combination_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `combinations`
--
ALTER TABLE `combinations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(225) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `request_statuses`
--
ALTER TABLE `request_statuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `timetable`
--
ALTER TABLE `timetable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5931;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_3` FOREIGN KEY (`existing_timetable_id`) REFERENCES `timetable` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `requests_ibfk_4` FOREIGN KEY (`proposed_subject_id`) REFERENCES `subjects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `requests_ibfk_5` FOREIGN KEY (`proposed_teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `requests_ibfk_6` FOREIGN KEY (`proposed_classroom_id`) REFERENCES `classrooms` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `requests_ibfk_7` FOREIGN KEY (`status_id`) REFERENCES `request_statuses` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requests_ibfk_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `requests_ibfk_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`combination_id`) REFERENCES `combinations` (`id`) ON DELETE CASCADE;

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
