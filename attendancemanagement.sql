-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 01, 2025 at 03:32 PM
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
-- Database: `attendancemanagement`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `CrudEmployeeAttendance` (IN `p_action` VARCHAR(10), IN `p_attendance_id` INT, IN `p_employee_id` INT, IN `p_shift_id` INT, IN `p_attendance_date` DATE, IN `p_check_in` TIME, IN `p_check_out` TIME, IN `p_status` ENUM('Present','Absent','Late','On Leave'), IN `p_remarks` VARCHAR(255))   BEGIN
    -- CREATE (Insert New Attendance Record)
    IF p_action = 'CREATE' THEN
        INSERT INTO employee_attendance (employee_id, shift_id, attendance_date, check_in, check_out, status, remarks) 
        VALUES (p_employee_id, p_shift_id, p_attendance_date, p_check_in, p_check_out, p_status, p_remarks);
    
    -- READ (Get All Attendance Records)
    ELSEIF p_action = 'READ' THEN
        SELECT ea.attendance_id, ei.first_name, ei.last_name, es.shift_date, es.shift_type, 
               ea.attendance_date, ea.status, ea.check_in, ea.check_out, ea.remarks
        FROM employee_attendance ea
        JOIN employee_info ei ON ea.employee_id = ei.employee_id
        JOIN employee_shift es ON ea.shift_id = es.id
        ORDER BY ea.attendance_date DESC;

    -- UPDATE (Modify Attendance Record)
    ELSEIF p_action = 'UPDATE' THEN
        UPDATE employee_attendance 
        SET employee_id = p_employee_id, shift_id = p_shift_id, attendance_date = p_attendance_date,
            check_in = p_check_in, check_out = p_check_out, status = p_status, remarks = p_remarks
        WHERE attendance_id = p_attendance_id;

    -- DELETE (Remove Attendance Record)
    ELSEIF p_action = 'DELETE' THEN
        DELETE FROM employee_attendance WHERE attendance_id = p_attendance_id;
    
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `employee_attendance`
--

CREATE TABLE `employee_attendance` (
  `attendance_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `status` enum('Present','Absent','Late','On Leave') NOT NULL DEFAULT 'Absent',
  `remarks` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_attendance`
--

INSERT INTO `employee_attendance` (`attendance_id`, `employee_id`, `shift_id`, `attendance_date`, `check_in`, `check_out`, `status`, `remarks`) VALUES
(1, 1, 1, '2024-04-01', '09:05:00', '17:00:00', 'Late', 'Traffic delay'),
(2, 2, 2, '2024-04-01', NULL, NULL, 'Absent', 'Did not report to work'),
(3, 3, 3, '2024-04-02', '21:55:00', '06:10:00', 'Present', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employee_info`
--

CREATE TABLE `employee_info` (
  `employee_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `department` varchar(50) DEFAULT NULL,
  `hire_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_info`
--

INSERT INTO `employee_info` (`employee_id`, `first_name`, `last_name`, `email`, `phone_number`, `department`, `hire_date`) VALUES
(1, 'John', 'Doe', 'john.doe@example.com', '1234567890', 'IT', '2023-05-10'),
(2, 'Jane', 'Smith', 'jane.smith@example.com', '0987654321', 'HR', '2022-09-15'),
(3, 'Alice', 'Brown', 'alice.brown@example.com', '1122334455', 'Finance', '2021-07-22');

-- --------------------------------------------------------

--
-- Table structure for table `employee_shift`
--

CREATE TABLE `employee_shift` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `shift_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `shift_type` enum('Morning','Afternoon','Night') NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_shift`
--

INSERT INTO `employee_shift` (`id`, `employee_id`, `shift_date`, `start_time`, `end_time`, `shift_type`, `status`) VALUES
(1, 1, '2024-04-01', '09:00:00', '17:00:00', 'Morning', 'Active'),
(2, 2, '2024-04-01', '13:00:00', '21:00:00', 'Afternoon', 'Active'),
(3, 3, '2024-04-02', '22:00:00', '06:00:00', 'Night', 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `employee_attendance`
--
ALTER TABLE `employee_attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `shift_id` (`shift_id`);

--
-- Indexes for table `employee_info`
--
ALTER TABLE `employee_info`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `employee_shift`
--
ALTER TABLE `employee_shift`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_employee` (`employee_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `employee_attendance`
--
ALTER TABLE `employee_attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `employee_info`
--
ALTER TABLE `employee_info`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `employee_shift`
--
ALTER TABLE `employee_shift`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employee_attendance`
--
ALTER TABLE `employee_attendance`
  ADD CONSTRAINT `employee_attendance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employee_info` (`employee_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_attendance_ibfk_2` FOREIGN KEY (`shift_id`) REFERENCES `employee_shift` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_shift`
--
ALTER TABLE `employee_shift`
  ADD CONSTRAINT `fk_employee` FOREIGN KEY (`employee_id`) REFERENCES `employee_info` (`employee_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
