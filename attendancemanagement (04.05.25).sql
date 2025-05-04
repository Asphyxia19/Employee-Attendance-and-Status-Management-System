-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 04, 2025 at 02:54 PM
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
CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateEmployee` (IN `p_FirstName` VARCHAR(255), IN `p_LastName` VARCHAR(255), IN `p_ContactNumber` VARCHAR(20), IN `p_Email` VARCHAR(255), IN `p_Address` TEXT, IN `p_Position` VARCHAR(100), IN `p_HireDate` DATE, IN `p_Password` VARCHAR(255))   BEGIN
    INSERT INTO employee_info (
        FirstName,
        LastName,
        ContactNumber,
        Email,
        Address,
        Position,
        HireDate,
        Password -- Include the Password field
    ) VALUES (
        p_FirstName,
        p_LastName,
        p_ContactNumber,
        p_Email,
        p_Address,
        p_Position,
        p_HireDate,
        p_Password -- Insert the Password value
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateManager` (IN `p_FirstName` VARCHAR(255), IN `p_LastName` VARCHAR(255), IN `p_Email` VARCHAR(255), IN `p_Password` VARCHAR(255))   BEGIN
    INSERT INTO manager_info (FirstName, LastName, Email, Password)
    VALUES (p_FirstName, p_LastName, p_Email, p_Password);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteEmployee` (IN `p_EmployeeID` INT)   BEGIN
    DELETE FROM employee_info
    WHERE EmployeeID = p_EmployeeID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteManager` (IN `p_ManagerID` INT)   BEGIN
    DELETE FROM manager_info WHERE ManagerID = p_ManagerID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAllAttendanceLogs` (IN `p_EmployeeID` INT)   BEGIN
    SELECT 
       EmployeeID, 
        Date, 
        CheckIn, 
        CheckOut, 
        Status, 
        Remarks
    FROM 
        attendance_log
    WHERE 
        EmployeeID = p_EmployeeID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAllEmployees` ()   BEGIN
    SELECT 
        EmployeeID, 
        FirstName, 
        LastName, 
        ContactNumber, 
        Email, 
        Address, 
        Position, 
        HireDate
    FROM 
        employee_info;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAllManagers` ()   BEGIN
    SELECT ManagerID, FirstName, LastName, Email FROM manager_info;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetEmployeeByID` (IN `p_EmployeeID` INT)   BEGIN
    SELECT 
        EmployeeID, 
        FirstName, 
        LastName, 
        ContactNumber, 
        Email, 
        Address, 
        Position, 
        HireDate
    FROM 
        employee_info
    WHERE 
        EmployeeID = p_EmployeeID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetManagerByID` (IN `p_ManagerID` INT)   BEGIN
    SELECT ManagerID, FirstName, LastName, Email
    FROM manager_info
    WHERE ManagerID = p_ManagerID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertAttendanceLog` (IN `p_EmployeeID` INT, IN `p_Date` DATE, IN `p_CheckIn` TIME, IN `p_CheckOut` TIME, IN `p_Status` VARCHAR(20), IN `p_Remarks` TEXT)   BEGIN
    INSERT INTO attendance_log (
        EmployeeID,
        Date,
        CheckIn,
        CheckOut,
        Status,
        Remarks
    ) VALUES (
        p_EmployeeID,
        p_Date,
        p_CheckIn,
        p_CheckOut,
        p_Status,
        p_Remarks
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `LoginManagerByID` (IN `p_ManagerID` INT, IN `p_Password` VARCHAR(255))   BEGIN
    SELECT 
        ManagerID, 
        FirstName, 
        LastName, 
        Email 
    FROM 
        manager_info
    WHERE 
        ManagerID = p_ManagerID AND Password = p_Password;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateEmployee` (IN `p_EmployeeID` INT, IN `p_FirstName` VARCHAR(255), IN `p_LastName` VARCHAR(255), IN `p_ContactNumber` VARCHAR(20), IN `p_Email` VARCHAR(255), IN `p_Address` TEXT, IN `p_Position` VARCHAR(100), IN `p_HireDate` DATE)   BEGIN
    UPDATE employee_info
    SET 
        FirstName = p_FirstName,
        LastName = p_LastName,
        ContactNumber = p_ContactNumber,
        Email = p_Email,
        Address = p_Address,
        Position = p_Position,
        HireDate = p_HireDate
    WHERE 
        EmployeeID = p_EmployeeID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateManager` (IN `p_ManagerID` INT, IN `p_FirstName` VARCHAR(255), IN `p_LastName` VARCHAR(255), IN `p_Email` VARCHAR(255))   BEGIN
    UPDATE manager_info
    SET FirstName = p_FirstName,
        LastName = p_LastName,
        Email = p_Email
    WHERE ManagerID = p_ManagerID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateManagerWithID` (IN `p_OriginalManagerID` INT, IN `p_NewManagerID` INT, IN `p_FirstName` VARCHAR(255), IN `p_LastName` VARCHAR(255), IN `p_Email` VARCHAR(255))   BEGIN
    UPDATE manager_info
    SET ManagerID = p_NewManagerID,
        FirstName = p_FirstName,
        LastName = p_LastName,
        Email = p_Email
    WHERE ManagerID = p_OriginalManagerID;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_log`
--

CREATE TABLE `attendance_log` (
  `EmployeeID` int(11) DEFAULT NULL,
  `Date` date NOT NULL,
  `CheckIn` time DEFAULT NULL,
  `CheckOut` time DEFAULT NULL,
  `Status` varchar(20) DEFAULT NULL,
  `Remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_fixed_schedule`
--

CREATE TABLE `employee_fixed_schedule` (
  `ScheduleID` int(11) NOT NULL,
  `EmployeeID` int(11) DEFAULT NULL,
  `DutyID` int(11) DEFAULT NULL,
  `DayOfWeek` varchar(20) DEFAULT NULL,
  `StartTime` time DEFAULT NULL,
  `EndTime` time DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_info`
--

CREATE TABLE `employee_info` (
  `EmployeeID` int(5) NOT NULL,
  `FirstName` varchar(255) DEFAULT NULL,
  `LastName` varchar(255) DEFAULT NULL,
  `ContactNumber` varchar(20) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Address` text DEFAULT NULL,
  `Position` varchar(100) DEFAULT NULL,
  `HireDate` date DEFAULT NULL,
  `Password` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_info`
--

INSERT INTO `employee_info` (`EmployeeID`, `FirstName`, `LastName`, `ContactNumber`, `Email`, `Address`, `Position`, `HireDate`, `Password`) VALUES
(10002, 'RUBYYCHAN', 'HAIIII', '09181234567', 'maria.santos@example.com', 'San Juan Batangas', 'Janitor', '2023-11-15', '23456'),
(10004, 'Ana', 'Lopez', '09201234567', 'ana.lopez@example.com', '321 Rizal Blvd., Pasig City', 'Cashier', '2021-09-30', '45678'),
(10005, 'Carlos', 'Torres', '09211234567', 'carlos.torres@example.com', '654 Katipunan Ave., Manila', 'Burger', '2020-01-20', '56789'),
(10006, 'Justin Kyle', 'Balubal', '09653527892', 'balubalcutie69@gmail.com', 'New York City, Philippines', 'Nuggets Station', '2025-05-02', ''),
(10008, 'Gian', 'Diaz', '099365841', 'giandiaz@gmail.com', 'Monte Carlo', 'Mission', '2025-05-23', '$2y$1');

-- --------------------------------------------------------

--
-- Table structure for table `employee_shift`
--

CREATE TABLE `employee_shift` (
  `ShiftID` int(11) NOT NULL,
  `EmployeeID` int(11) DEFAULT NULL,
  `Date` date DEFAULT NULL,
  `Duty` varchar(100) DEFAULT NULL,
  `TimeIn` datetime DEFAULT NULL,
  `TimeOut` datetime DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `manager_info`
--

CREATE TABLE `manager_info` (
  `ManagerID` int(7) NOT NULL,
  `FirstName` varchar(255) DEFAULT NULL,
  `LastName` varchar(255) DEFAULT NULL,
  `ContactNumber` varchar(20) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manager_info`
--

INSERT INTO `manager_info` (`ManagerID`, `FirstName`, `LastName`, `ContactNumber`, `Email`, `Password`) VALUES
(2336324, 'Sean', 'Del Rosario', '09065816503', 'seanmdelrosario@gmail.com', 'manager'),
(2356781, 'Isabella', 'Garcia', '09981234567', 'isabella.garcia@gmail.com', 'bandito'),
(2648537, 'Miguel', 'Navarro', '09992234567', 'miguel.navarro@company.com', 'tungtung'),
(2917714, 'Cheska', 'De Castro', '09938309279', 'cheskadecastro00@gmail.com', 'sean');

-- --------------------------------------------------------

--
-- Table structure for table `overtime_log`
--

CREATE TABLE `overtime_log` (
  `OTID` int(11) NOT NULL,
  `EmployeeID` int(11) DEFAULT NULL,
  `Date` date DEFAULT NULL,
  `StartTime` time DEFAULT NULL,
  `EndTime` time DEFAULT NULL,
  `HoursRendered` decimal(5,2) DEFAULT NULL,
  `Reason` text DEFAULT NULL,
  `ApprovedBy` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_info`
--

CREATE TABLE `payroll_info` (
  `PayrollID` int(11) NOT NULL,
  `EmployeeID` int(11) DEFAULT NULL,
  `PeriodStart` date DEFAULT NULL,
  `PeriodEnd` date DEFAULT NULL,
  `TotalHoursWorked` decimal(6,2) DEFAULT NULL,
  `OTHours` decimal(6,2) DEFAULT NULL,
  `HourlyRate` decimal(10,2) DEFAULT 60.00,
  `OTRate` decimal(10,2) DEFAULT NULL,
  `GrossPay` decimal(10,2) DEFAULT NULL,
  `Deductions` decimal(10,2) DEFAULT NULL,
  `NetPay` decimal(10,2) DEFAULT NULL,
  `Status` varchar(50) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `RequestID` int(11) NOT NULL,
  `EmployeeID` int(11) DEFAULT NULL,
  `RequestType` varchar(100) DEFAULT NULL,
  `Reason` text DEFAULT NULL,
  `DateRequested` date DEFAULT NULL,
  `Status` varchar(50) DEFAULT NULL,
  `ManagerID` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shift_duty_info`
--

CREATE TABLE `shift_duty_info` (
  `DutyID` int(11) NOT NULL,
  `DutyName` varchar(100) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance_log`
--
ALTER TABLE `attendance_log`
  ADD KEY `fk_attendance_employee` (`EmployeeID`);

--
-- Indexes for table `employee_fixed_schedule`
--
ALTER TABLE `employee_fixed_schedule`
  ADD PRIMARY KEY (`ScheduleID`),
  ADD KEY `EmployeeID` (`EmployeeID`),
  ADD KEY `DutyID` (`DutyID`);

--
-- Indexes for table `employee_info`
--
ALTER TABLE `employee_info`
  ADD PRIMARY KEY (`EmployeeID`);

--
-- Indexes for table `employee_shift`
--
ALTER TABLE `employee_shift`
  ADD PRIMARY KEY (`ShiftID`),
  ADD KEY `EmployeeID` (`EmployeeID`);

--
-- Indexes for table `manager_info`
--
ALTER TABLE `manager_info`
  ADD PRIMARY KEY (`ManagerID`);

--
-- Indexes for table `overtime_log`
--
ALTER TABLE `overtime_log`
  ADD PRIMARY KEY (`OTID`),
  ADD KEY `EmployeeID` (`EmployeeID`),
  ADD KEY `ApprovedBy` (`ApprovedBy`);

--
-- Indexes for table `payroll_info`
--
ALTER TABLE `payroll_info`
  ADD PRIMARY KEY (`PayrollID`),
  ADD KEY `EmployeeID` (`EmployeeID`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`RequestID`),
  ADD KEY `EmployeeID` (`EmployeeID`),
  ADD KEY `ManagerID` (`ManagerID`);

--
-- Indexes for table `shift_duty_info`
--
ALTER TABLE `shift_duty_info`
  ADD PRIMARY KEY (`DutyID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `employee_fixed_schedule`
--
ALTER TABLE `employee_fixed_schedule`
  MODIFY `ScheduleID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_info`
--
ALTER TABLE `employee_info`
  MODIFY `EmployeeID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10009;

--
-- AUTO_INCREMENT for table `employee_shift`
--
ALTER TABLE `employee_shift`
  MODIFY `ShiftID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `manager_info`
--
ALTER TABLE `manager_info`
  MODIFY `ManagerID` int(7) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2147483648;

--
-- AUTO_INCREMENT for table `overtime_log`
--
ALTER TABLE `overtime_log`
  MODIFY `OTID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll_info`
--
ALTER TABLE `payroll_info`
  MODIFY `PayrollID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `RequestID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shift_duty_info`
--
ALTER TABLE `shift_duty_info`
  MODIFY `DutyID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance_log`
--
ALTER TABLE `attendance_log`
  ADD CONSTRAINT `fk_attendance_employee` FOREIGN KEY (`EmployeeID`) REFERENCES `employee_info` (`EmployeeID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `employee_fixed_schedule`
--
ALTER TABLE `employee_fixed_schedule`
  ADD CONSTRAINT `employee_fixed_schedule_ibfk_1` FOREIGN KEY (`EmployeeID`) REFERENCES `employee_info` (`EmployeeID`),
  ADD CONSTRAINT `employee_fixed_schedule_ibfk_2` FOREIGN KEY (`DutyID`) REFERENCES `shift_duty_info` (`DutyID`);

--
-- Constraints for table `employee_shift`
--
ALTER TABLE `employee_shift`
  ADD CONSTRAINT `employee_shift_ibfk_1` FOREIGN KEY (`EmployeeID`) REFERENCES `employee_info` (`EmployeeID`);

--
-- Constraints for table `overtime_log`
--
ALTER TABLE `overtime_log`
  ADD CONSTRAINT `overtime_log_ibfk_1` FOREIGN KEY (`EmployeeID`) REFERENCES `employee_info` (`EmployeeID`),
  ADD CONSTRAINT `overtime_log_ibfk_2` FOREIGN KEY (`ApprovedBy`) REFERENCES `manager_info` (`ManagerID`);

--
-- Constraints for table `payroll_info`
--
ALTER TABLE `payroll_info`
  ADD CONSTRAINT `payroll_info_ibfk_1` FOREIGN KEY (`EmployeeID`) REFERENCES `employee_info` (`EmployeeID`);

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`EmployeeID`) REFERENCES `employee_info` (`EmployeeID`),
  ADD CONSTRAINT `requests_ibfk_2` FOREIGN KEY (`ManagerID`) REFERENCES `manager_info` (`ManagerID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
