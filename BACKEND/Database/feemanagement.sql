-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 03, 2026 at 05:01 PM
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
-- Database: `feemanagement`
--

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE `class` (
  `id` int(11) NOT NULL,
  `class_name` varchar(25) DEFAULT NULL,
  `class_sec` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`id`, `class_name`, `class_sec`) VALUES
(11, '', ''),
(6, '1', 'A'),
(8, '1', 'B'),
(9, '1', 'D'),
(10, '7', 'D'),
(3, 'play group', 'P.G');

-- --------------------------------------------------------

--
-- Table structure for table `signup`
--

CREATE TABLE `signup` (
  `id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phonenumber` varchar(35) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `type` varchar(10) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `signup`
--

INSERT INTO `signup` (`id`, `username`, `email`, `phonenumber`, `Password`, `type`) VALUES
(1, 'rooshan15', 'rooshanrizwan@gmail.com', '03116594981', '$2y$10$jHfO/n8b8nHHu92Qhk5E7e065U4X3uPLV022tHZtE2nS.8AYSIyai', 'admin'),
(2, 'Humna Ali', 'humnali@gmail.com', '0311111111', '$2y$10$dlvpJRTOdI3S15TJ75l67eB5p7o/JRf8XjcpS6lIsuXXnLbzVyooW', 'user'),
(5, 'Humna Ali', 'humnaali@gmail.com', '0311111111', '$2y$10$5lDVJH0S3T1AcpXnE3nS..cYugmsBeDFa8FGGRWYoB7L6H0pbfQyq', 'user'),
(6, 'norain ', 'nony@gmail.com', '03162485821', '$2y$10$Kik0B1.JYmU.txRanpXVuOL688.T6xjR1jklyFNwSC46.nW54kcE2', 'admin'),
(7, 'arham', 'arhamrizwan@gmail.com', '031111', '$2y$10$Pm88ELdOKe9mkrsfNRDrQeukqUBaL9jbNX5lpJNJmd1k.nAJ6A2N2', 'user'),
(10, 'arham', 'arhamrizwan11@gmail.com', '031111', '$2y$10$VTp07BK2WfGiBD89eCNOxuMqU9cMkhnacmW2FzVUg.ixZvuXo9g7W', 'user'),
(12, '18975', 'xxxxxx@gmail.com', 'xxxxxxx', '$2y$10$G9at4NojpIe8RaU/KxPha.cDMI9NagTGUXDUp2pVC/naCkWGbOY0K', 'user'),
(13, 'Arham', 'r@gmail.com', 'xxxxxx', '$2y$10$8jP7.N8h0GO705qbA76QWeSX6i2Z7XYh6p3XVw3iXmbTrTspSvWYq', 'user'),
(14, 'xxxx', 'a@gmail.com', 'xxxxx', '$2y$10$VhU0rrZyVIp9cvYMyuco3.6TRURhyiOw.EirpVJA7Fgw8bB8f.Am6', 'user'),
(16, '1=1', 'aaaaa@gmail.com', '1=1', '$2y$10$7.Z5e9mtSi3yAxTdgRhider5lmJhDvVAnPsWXx6mZkKb1Z49Bmyd6', 'user');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `id` int(11) NOT NULL,
  `DAS` varchar(255) DEFAULT NULL,
  `student_name` varchar(100) NOT NULL,
  `father_name` varchar(100) NOT NULL,
  `contact_number` varchar(100) NOT NULL,
  `class_id` varchar(100) DEFAULT NULL,
  `T_Fee` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`id`, `DAS`, `student_name`, `father_name`, `contact_number`, `class_id`, `T_Fee`, `is_deleted`) VALUES
(1, '824', 'M.Abdullah', 'Ali Hasnain', '0317-4248973', '6', '1500', 1),
(2, '825', 'Hania Amir', 'Khanarja Amir', '0334-5023292', '3', '2200', 1),
(3, '826', 'Zimal Fatima', 'Syed Abdul samal', '0315-1236525-0333-5321509', '3', '2500', 1),
(4, '827', 'Alishba Asif', 'Rana M.Asif', '0333-5395574/0321-5879450', '3', '2500', 1),
(5, '828', 'Hifsa Asif', 'Rana M.Asif', '0333-5395574/0321-5879450', '3', '2500', 1),
(6, '829', 'Shanzay Fatima', 'Danish Mehmood', '0332-0563300/0315-5310095', '6', '2500', 1),
(7, '830', 'Irfa yasir', 'YASIR', '0301-5003657', '3', '3000', 1),
(8, '831', 'Huzaifa', 'M.Shoaib', '0336-2565151', '3', '2500', 0),
(9, '832', 'M.Abdullah', 'Ali Hadir', '0321-8837329/0326-5646360', '3', '3000', 0),
(10, '833', 'Mirha Hadir', 'Shabih Hadir', '0336-9749515', '3', '2500', 0),
(11, '834', 'M.Ahmed', 'M.Zeeshan', '0346-5493878/0334-9556658', '3', '2600', 0),
(12, '835', 'M.Hashir Ilyas', 'M.Ilyas', '0317-5467058', '3', '2500', 0),
(13, '836', 'Waqas Ahmed', 'Manzoor ullah', '0370-5501455/0324-9837753', '3', '2000', 0),
(14, '837', 'Ahmed Ali', 'M.Ali', '0315-0598447/0321-5811375', '3', '3000', 0),
(15, '838', 'M.Mujtaba', 'Mehtab Khan', '0335-3822278/0307-2137035', '3', '2500', 0),
(16, '839', 'Amal Tariq', 'M.Tariq Mughal', '0321-5000485', '3', '2700', 0),
(17, '840', 'Noor-e-Arham', 'Fawad ', '0315-5406081/0348-5568958', '3', '2500', 0),
(18, '841', 'Rimsha Shahzad', 'M.Shahzad Khan', '0370-5501455', '3', '2700', 0),
(19, '842', 'Waqar Ahmed', 'Farhan Khan', '0370-5501455', '3', '2700', 0),
(20, '843', 'Haris Ali', 'M.Waseem', '0316-1586140', '3', '2500', 0),
(21, '844', 'Noor e haram', 'M.Nazir Khan', '0347-5102811', '3', '2500', 0),
(22, '845', 'M.Umer', 'M.Nazir Khan', '0347-5102811', '3', '2500', 0),
(23, '846', 'M.Husnain Ali', 'Junaid Jawad ', '0341-2349088/0333-8256659', '3', '2500', 0),
(24, '847', 'Waleeja Umer', 'M.Umer', '0341-2349088/0310-9049203', '3', '2000', 0),
(25, '848', 'Mozan', 'Sami ullah', '0311-5181477', '3', '2000', 0),
(26, '849', 'Aqsa Noor', 'Mukhtar Sultan', '0312-0500173', '3', '2000', 0),
(27, '850', 'Momina Akeel', 'Akeel Ahmed', '0315-5033017', '3', '2000', 0),
(28, '851', 'Mashal Kaleem', 'Kaleem Ullah', '0303-4342325/0321-7169651', '3', '2000', 0),
(29, '852', 'M.Ahil Kaleem', 'Kaleem ullah', '0303-4342325/0321-7169651', '3', '2000', 0),
(30, '853', 'M.Sami ullah', 'M.Waqar', '0315-5148144', '3', '2500', 0),
(31, '854', 'Azlan Shoukat', 'Khawaja Shoukat Hussain', '0334-5058853', '3', '2500', 0),
(32, '855', 'M.Ibraheem', 'Raja Amir Azam', '0347-5391363/0348-1051363', '3', '2000', 0),
(33, '856', 'Zimal ', 'M.Changaiz Khan', '0336-5035180/0336-5479515', '3', '2500', 0),
(34, '8623', 'M.Hammad', 'Imran Ayub', '0333-0864018/0336-5080977', '3', '3000', 0),
(35, '863', 'Abdul Raffay', 'M.Sahkeel', '0311-5256898/0312-0047331', '3', '', 0),
(36, '864', 'Zainab  Inam', 'Inam ul Haq', '0300-5679117/0313-5679117', '3', '2000', 0),
(37, '', '', '', '', '', '', 0),
(43, '121', 'Rooshan', 'Rizwan', '03116594981', '', '1000', 0),
(49, '1000000', 'Humna', 'Ali', '03116594981', '9', '1000', 0);

-- --------------------------------------------------------

--
-- Table structure for table `student_fee`
--

CREATE TABLE `student_fee` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `fee_month` tinyint(4) NOT NULL,
  `fee_year` year(4) DEFAULT NULL,
  `status` enum('paid','unpaid') DEFAULT 'unpaid',
  `paid_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_fee`
--

INSERT INTO `student_fee` (`id`, `student_id`, `fee_month`, `fee_year`, `status`, `paid_date`) VALUES
(1, 1, 4, '2025', 'paid', '2025-12-30'),
(2, 2, 4, '2025', 'paid', '2025-12-30'),
(3, 3, 4, '2025', 'paid', '2025-12-30'),
(4, 4, 4, '2025', 'paid', '2025-12-30'),
(5, 5, 4, '2025', 'paid', '2025-12-30'),
(6, 6, 4, '2025', 'unpaid', NULL),
(7, 7, 4, '2025', 'unpaid', NULL),
(8, 8, 4, '2025', 'unpaid', NULL),
(9, 9, 4, '2025', 'unpaid', NULL),
(10, 10, 4, '2025', 'unpaid', NULL),
(11, 11, 4, '2025', 'unpaid', NULL),
(12, 12, 4, '2025', 'unpaid', NULL),
(13, 13, 4, '2025', 'unpaid', NULL),
(14, 14, 4, '2025', 'unpaid', NULL),
(15, 15, 4, '2025', 'unpaid', NULL),
(16, 16, 4, '2025', 'unpaid', NULL),
(17, 17, 4, '2025', 'unpaid', NULL),
(18, 18, 4, '2025', 'unpaid', NULL),
(19, 19, 4, '2025', 'unpaid', NULL),
(20, 20, 4, '2025', 'unpaid', NULL),
(21, 21, 4, '2025', 'unpaid', NULL),
(22, 22, 4, '2025', 'unpaid', NULL),
(23, 23, 4, '2025', 'unpaid', NULL),
(24, 24, 4, '2025', 'unpaid', NULL),
(25, 25, 4, '2025', 'unpaid', NULL),
(26, 26, 4, '2025', 'unpaid', NULL),
(27, 27, 4, '2025', 'unpaid', NULL),
(28, 28, 4, '2025', 'unpaid', NULL),
(29, 29, 4, '2025', 'unpaid', NULL),
(30, 30, 4, '2025', 'unpaid', NULL),
(31, 31, 4, '2025', 'unpaid', NULL),
(32, 32, 4, '2025', 'unpaid', NULL),
(33, 33, 4, '2025', 'unpaid', NULL),
(34, 34, 4, '2025', 'unpaid', NULL),
(35, 35, 4, '2025', 'unpaid', NULL),
(36, 36, 4, '2025', 'unpaid', NULL),
(37, 1, 1, '2025', 'paid', '2025-12-30'),
(38, 2, 1, '2025', 'paid', '2025-12-30'),
(39, 3, 1, '2025', 'paid', '2025-12-30'),
(40, 4, 1, '2025', 'paid', '2025-12-30'),
(41, 5, 1, '2025', 'unpaid', NULL),
(42, 6, 1, '2025', 'unpaid', NULL),
(43, 7, 1, '2025', 'unpaid', NULL),
(44, 8, 1, '2025', 'unpaid', NULL),
(45, 9, 1, '2025', 'unpaid', NULL),
(46, 10, 1, '2025', 'unpaid', NULL),
(47, 11, 1, '2025', 'unpaid', NULL),
(48, 12, 1, '2025', 'unpaid', NULL),
(49, 13, 1, '2025', 'unpaid', NULL),
(50, 14, 1, '2025', 'unpaid', NULL),
(51, 15, 1, '2025', 'unpaid', NULL),
(52, 16, 1, '2025', 'unpaid', NULL),
(53, 17, 1, '2025', 'unpaid', NULL),
(54, 18, 1, '2025', 'unpaid', NULL),
(55, 19, 1, '2025', 'unpaid', NULL),
(56, 20, 1, '2025', 'unpaid', NULL),
(57, 21, 1, '2025', 'unpaid', NULL),
(58, 22, 1, '2025', 'unpaid', NULL),
(59, 23, 1, '2025', 'unpaid', NULL),
(60, 24, 1, '2025', 'unpaid', NULL),
(61, 25, 1, '2025', 'unpaid', NULL),
(62, 26, 1, '2025', 'unpaid', NULL),
(63, 27, 1, '2025', 'unpaid', NULL),
(64, 28, 1, '2025', 'unpaid', NULL),
(65, 29, 1, '2025', 'unpaid', NULL),
(66, 30, 1, '2025', 'unpaid', NULL),
(67, 31, 1, '2025', 'unpaid', NULL),
(68, 32, 1, '2025', 'unpaid', NULL),
(69, 33, 1, '2025', 'unpaid', NULL),
(70, 34, 1, '2025', 'unpaid', NULL),
(71, 35, 1, '2025', 'unpaid', NULL),
(72, 36, 1, '2025', 'unpaid', NULL),
(73, 1, 3, '2028', 'unpaid', NULL),
(74, 2, 3, '2028', 'unpaid', NULL),
(75, 3, 3, '2028', 'unpaid', NULL),
(76, 4, 3, '2028', 'unpaid', NULL),
(77, 5, 3, '2028', 'unpaid', NULL),
(78, 6, 3, '2028', 'unpaid', NULL),
(79, 7, 3, '2028', 'unpaid', NULL),
(80, 8, 3, '2028', 'unpaid', NULL),
(81, 9, 3, '2028', 'unpaid', NULL),
(82, 10, 3, '2028', 'unpaid', NULL),
(83, 11, 3, '2028', 'unpaid', NULL),
(84, 12, 3, '2028', 'unpaid', NULL),
(85, 13, 3, '2028', 'unpaid', NULL),
(86, 14, 3, '2028', 'unpaid', NULL),
(87, 15, 3, '2028', 'unpaid', NULL),
(88, 16, 3, '2028', 'unpaid', NULL),
(89, 17, 3, '2028', 'unpaid', NULL),
(90, 18, 3, '2028', 'unpaid', NULL),
(91, 19, 3, '2028', 'unpaid', NULL),
(92, 20, 3, '2028', 'unpaid', NULL),
(93, 21, 3, '2028', 'unpaid', NULL),
(94, 22, 3, '2028', 'unpaid', NULL),
(95, 23, 3, '2028', 'unpaid', NULL),
(96, 24, 3, '2028', 'unpaid', NULL),
(97, 25, 3, '2028', 'unpaid', NULL),
(98, 26, 3, '2028', 'unpaid', NULL),
(99, 27, 3, '2028', 'unpaid', NULL),
(100, 28, 3, '2028', 'unpaid', NULL),
(101, 29, 3, '2028', 'unpaid', NULL),
(102, 30, 3, '2028', 'unpaid', NULL),
(103, 31, 3, '2028', 'unpaid', NULL),
(104, 32, 3, '2028', 'unpaid', NULL),
(105, 33, 3, '2028', 'unpaid', NULL),
(106, 34, 3, '2028', 'unpaid', NULL),
(107, 35, 3, '2028', 'unpaid', NULL),
(108, 36, 3, '2028', 'unpaid', NULL),
(109, 2, 1, '2026', 'paid', '2026-01-02'),
(110, 3, 1, '2026', 'paid', '2026-01-02'),
(111, 4, 1, '2026', 'paid', '2026-01-02'),
(112, 5, 1, '2026', 'unpaid', NULL),
(113, 7, 1, '2026', 'unpaid', NULL),
(114, 8, 1, '2026', 'unpaid', NULL),
(115, 9, 1, '2026', 'unpaid', NULL),
(116, 10, 1, '2026', 'unpaid', NULL),
(117, 11, 1, '2026', 'unpaid', NULL),
(118, 12, 1, '2026', 'unpaid', NULL),
(119, 13, 1, '2026', 'unpaid', NULL),
(120, 14, 1, '2026', 'unpaid', NULL),
(121, 15, 1, '2026', 'unpaid', NULL),
(122, 16, 1, '2026', 'unpaid', NULL),
(123, 17, 1, '2026', 'unpaid', NULL),
(124, 18, 1, '2026', 'unpaid', NULL),
(125, 19, 1, '2026', 'unpaid', NULL),
(126, 20, 1, '2026', 'unpaid', NULL),
(127, 21, 1, '2026', 'unpaid', NULL),
(128, 22, 1, '2026', 'unpaid', NULL),
(129, 23, 1, '2026', 'unpaid', NULL),
(130, 24, 1, '2026', 'unpaid', NULL),
(131, 25, 1, '2026', 'unpaid', NULL),
(132, 26, 1, '2026', 'unpaid', NULL),
(133, 27, 1, '2026', 'unpaid', NULL),
(134, 28, 1, '2026', 'unpaid', NULL),
(135, 29, 1, '2026', 'unpaid', NULL),
(136, 30, 1, '2026', 'unpaid', NULL),
(137, 31, 1, '2026', 'unpaid', NULL),
(138, 32, 1, '2026', 'unpaid', NULL),
(139, 33, 1, '2026', 'unpaid', NULL),
(140, 34, 1, '2026', 'unpaid', NULL),
(141, 35, 1, '2026', 'unpaid', NULL),
(142, 36, 1, '2026', 'unpaid', NULL),
(143, 1, 1, '2026', 'paid', '2026-01-01'),
(144, 6, 1, '2026', 'paid', '2026-01-01');

--
-- Triggers `student_fee`
--
DELIMITER $$
CREATE TRIGGER `before_student_fee_insert` BEFORE INSERT ON `student_fee` FOR EACH ROW BEGIN
    IF NEW.status = 'paid' THEN
        SET NEW.paid_date = CURDATE();
    ELSE
        SET NEW.paid_date = NULL;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_student_fee_update` BEFORE UPDATE ON `student_fee` FOR EACH ROW BEGIN
    IF NEW.status = 'paid' THEN
        SET NEW.paid_date = CURDATE();
    ELSE
        SET NEW.paid_date = NULL;
    END IF;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `class`
--
ALTER TABLE `class`
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `unique_class_section` (`class_name`,`class_sec`);

--
-- Indexes for table `signup`
--
ALTER TABLE `signup`
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `DAS` (`DAS`);

--
-- Indexes for table `student_fee`
--
ALTER TABLE `student_fee`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `class`
--
ALTER TABLE `class`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `signup`
--
ALTER TABLE `signup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `student_fee`
--
ALTER TABLE `student_fee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=145;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `student_fee`
--
ALTER TABLE `student_fee`
  ADD CONSTRAINT `student_fee_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
