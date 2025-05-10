-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 10, 2025 at 03:18 AM
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
-- Database: `attendance_management`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `ApprovePayroll` (IN `p_payroll_id` INT, IN `p_manager_id` INT)   BEGIN
    UPDATE payroll_info
    SET Status = 'Approved'
    WHERE PayrollID = p_payroll_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CheckEmployeeExists` (IN `emp_id` INT)   BEGIN
    SELECT 1 FROM employee_info WHERE EmployeeID = emp_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateEmployee` (IN `p_FirstName` VARCHAR(255), IN `p_LastName` VARCHAR(255), IN `p_ContactNumber` VARCHAR(20), IN `p_Email` VARCHAR(255), IN `p_Address` TEXT, IN `p_Position` VARCHAR(100), IN `p_HireDate` DATE, IN `p_Password` VARCHAR(255))   BEGIN
    INSERT INTO employee_info (
        FirstName,
        LastName,
        ContactNumber,
        Email,
        Address,
        Position,
        HireDate,
        Password
    ) VALUES (
        p_FirstName,
        p_LastName,
        p_ContactNumber,
        p_Email,
        p_Address,
        p_Position,
        p_HireDate,
        p_Password
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `GeneratePayroll` (IN `p_EmployeeID` INT, IN `p_StartDate` DATE, IN `p_EndDate` DATE)   BEGIN
    DECLARE v_TotalHours DECIMAL(10,2);
    DECLARE v_OTHours DECIMAL(10,2);
    DECLARE v_HourlyRate DECIMAL(10,2);
    DECLARE v_GrossPay DECIMAL(10,2);
    DECLARE v_Deductions DECIMAL(10,2);
    DECLARE v_NetPay DECIMAL(10,2);
    
    -- Get employee's hourly rate
    SELECT HourlyRate INTO v_HourlyRate
    FROM employee_info
    WHERE EmployeeID = p_EmployeeID;
    
    -- Calculate total hours and overtime from attendance
    SELECT 
        SUM(TIMESTAMPDIFF(HOUR, CheckIn, CASE WHEN CheckOut IS NULL THEN '23:59:59' ELSE CheckOut END)) INTO v_TotalHours
    FROM attendance_log
    WHERE EmployeeID = p_EmployeeID
    AND Date BETWEEN p_StartDate AND p_EndDate
    AND Status = 'Present';
    
    -- Calculate overtime hours (hours over 8 per day)
    SELECT 
        SUM(CASE 
            WHEN TIMESTAMPDIFF(HOUR, CheckIn, CASE WHEN CheckOut IS NULL THEN '23:59:59' ELSE CheckOut END) > 8 
            THEN TIMESTAMPDIFF(HOUR, CheckIn, CASE WHEN CheckOut IS NULL THEN '23:59:59' ELSE CheckOut END) - 8 
            ELSE 0 
        END) INTO v_OTHours
    FROM attendance_log
    WHERE EmployeeID = p_EmployeeID
    AND Date BETWEEN p_StartDate AND p_EndDate
    AND Status = 'Present';
    
    -- Set default values if NULL
    SET v_TotalHours = COALESCE(v_TotalHours, 0);
    SET v_OTHours = COALESCE(v_OTHours, 0);
    
    -- Calculate gross pay
    SET v_GrossPay = (v_TotalHours * v_HourlyRate) + (v_OTHours * (v_HourlyRate * 1.25));
    
    -- Calculate deductions (example: 10% for taxes)
    SET v_Deductions = v_GrossPay * 0.10;
    
    -- Calculate net pay
    SET v_NetPay = v_GrossPay - v_Deductions;
    
    -- Insert payroll record
    INSERT INTO payroll_info (
        EmployeeID,
        PeriodStart,
        PeriodEnd,
        TotalHoursWorked,
        OTHours,
        GrossPay,
        Deductions,
        NetPay,
        Status
    ) VALUES (
        p_EmployeeID,
        p_StartDate,
        p_EndDate,
        v_TotalHours,
        v_OTHours,
        v_GrossPay,
        v_Deductions,
        v_NetPay,
        'Pending'
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAllAttendanceLogs` (IN `empID` INT)   BEGIN
    SELECT * FROM attendance_log WHERE EmployeeID = empID AND EmployeeID IS NOT NULL;
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetEmployeePassword` (IN `emp_id` INT)   BEGIN
    SELECT Password FROM employee_info WHERE EmployeeID = emp_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetManagerByID` (IN `p_ManagerID` INT)   BEGIN
    SELECT ManagerID, FirstName, LastName, Email
    FROM manager_info
    WHERE ManagerID = p_ManagerID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetPayrollSummary` (IN `p_employee_id` INT, IN `p_start_date` DATE, IN `p_end_date` DATE)   BEGIN
    SELECT 
        e.FirstName,
        e.LastName,
        p.PayrollID,
        p.PeriodStart,
        p.PeriodEnd,
        p.TotalHoursWorked,
        p.OTHours,
        p.GrossPay,
        p.Deductions,
        p.NetPay,
        p.Status
    FROM payroll_info p
    JOIN employee_info e ON p.EmployeeID = e.EmployeeID
    WHERE p.EmployeeID = p_employee_id
    AND p.PeriodStart >= p_start_date
    AND p.PeriodEnd <= p_end_date
    ORDER BY p.PeriodStart DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetTodayAttendance` (IN `emp_id` INT, IN `log_date` DATE)   BEGIN
    SELECT CheckIn, CheckOut 
    FROM attendance_log 
    WHERE EmployeeID = emp_id AND Date = log_date;
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertTimeIn` (IN `emp_id` INT, IN `log_date` DATE, IN `time_in` TIME)   BEGIN
    INSERT INTO attendance_log (EmployeeID, Date, CheckIn, Status)
    VALUES (emp_id, log_date, time_in, 'Present');
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateTimeOut` (IN `emp_id` INT, IN `log_date` DATE, IN `time_out` TIME)   BEGIN
    UPDATE attendance_log 
    SET CheckOut = time_out 
    WHERE EmployeeID = emp_id AND Date = log_date;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ValidateEmployeeLogin` (IN `emp_id` INT, IN `emp_password` VARCHAR(255))   BEGIN
    SELECT * FROM employee_info 
    WHERE EmployeeID = emp_id AND Password = emp_password;
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

--
-- Dumping data for table `attendance_log`
--

INSERT INTO `attendance_log` (`EmployeeID`, `Date`, `CheckIn`, `CheckOut`, `Status`, `Remarks`) VALUES
(10009, '2025-05-09', '06:00:00', '13:00:00', 'Present', ''),
(10009, '2025-05-10', '01:00:21', '01:00:29', 'Present', NULL),
(10002, '2025-05-10', '01:06:50', '01:49:46', 'Present', NULL),
(10006, '2025-05-10', '01:33:49', '01:52:41', 'Present', NULL),
(10005, '2025-05-10', '02:14:03', '02:14:12', 'Present', NULL),
(10004, '2025-05-10', '08:56:40', '08:56:48', 'Present', NULL);

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
  `EmployeeID` int(11) NOT NULL,
  `FirstName` varchar(255) DEFAULT NULL,
  `LastName` varchar(255) DEFAULT NULL,
  `ContactNumber` varchar(20) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Address` text DEFAULT NULL,
  `Position` varchar(100) DEFAULT NULL,
  `HireDate` date DEFAULT NULL,
  `Password` varchar(5) NOT NULL,
  `HourlyRate` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_info`
--

INSERT INTO `employee_info` (`EmployeeID`, `FirstName`, `LastName`, `ContactNumber`, `Email`, `Address`, `Position`, `HireDate`, `Password`, `HourlyRate`) VALUES
(10002, 'RUBYYCHAN', 'HAIIII', '09181234567', 'maria.santos@example.com', 'San Juan Batangas', 'Janitor', '2023-11-15', '23456', NULL),
(10004, 'Ana', 'Lopez', '09201234567', 'ana.lopez@example.com', '321 Rizal Blvd., Pasig City', 'Cashier', '2021-09-30', '45678', NULL),
(10005, 'Carlos', 'Torres', '09211234567', 'carlos.torres@example.com', '654 Katipunan Ave., Manila', 'Burger', '2020-01-20', '56789', NULL),
(10006, 'Justin Kyle', 'Balubal', '09653527892', 'balubalcutie69@gmail.com', 'New York City, Philippines', 'Nuggets Station', '2025-05-02', '', NULL),
(10008, 'Gian', 'Diaz', '099365841', 'giandiaz@gmail.com', 'Monte Carlo', 'Mission', '2025-05-23', '$2y$1', NULL),
(10009, 'Julie Ann', 'Toledo', '099134567809', 'mtjuls41@gmail.com', 'MUNTING TUBIG', 'cashier', '2024-05-09', '$2y$1', NULL);

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
  `ManagerID` int(11) NOT NULL,
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
(2917, 'Cheska', 'De Castro', '09938309279', NULL, 'sean'),
(20001, 'Isabella', 'Garcia', '09981234567', 'isabella.garcia@company.com', 'bandito'),
(20002, 'Miguel', 'Navarro', '09992234567', 'miguel.navarro@company.com', 'tungtung'),
(2336324, 'Sean', 'Del Rosario', '09065816503', 'seanmdelrosario@gmail.com', 'manager');

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

--
-- Dumping data for table `payroll_info`
--

INSERT INTO `payroll_info` (`PayrollID`, `EmployeeID`, `PeriodStart`, `PeriodEnd`, `TotalHoursWorked`, `OTHours`, `HourlyRate`, `OTRate`, `GrossPay`, `Deductions`, `NetPay`, `Status`, `employee_id`) VALUES
(1, 10008, '2025-05-09', '2025-05-09', 0.00, 0.00, 60.00, 75.00, 0.00, 0.00, 0.00, 'Approved', NULL);

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
  MODIFY `EmployeeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10010;

--
-- AUTO_INCREMENT for table `employee_shift`
--
ALTER TABLE `employee_shift`
  MODIFY `ShiftID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `manager_info`
--
ALTER TABLE `manager_info`
  MODIFY `ManagerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2336326;

--
-- AUTO_INCREMENT for table `overtime_log`
--
ALTER TABLE `overtime_log`
  MODIFY `OTID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll_info`
--
ALTER TABLE `payroll_info`
  MODIFY `PayrollID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
