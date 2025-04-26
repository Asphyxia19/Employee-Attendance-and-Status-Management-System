-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 15, 2025 at 12:15 AM
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
CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateAttendanceLog` (IN `p_ShiftID` INT, IN `p_Status` VARCHAR(50), IN `p_LateMinutes` INT, IN `p_Notes` TEXT, IN `p_VerifiedBy` INT)   BEGIN
    INSERT INTO Attendance_Log (ShiftID, Status, LateMinutes, Notes, VerifiedBy)
    VALUES (p_ShiftID, p_Status, p_LateMinutes, p_Notes, p_VerifiedBy);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateEmployee` (IN `p_FirstName` VARCHAR(255), IN `p_LastName` VARCHAR(255), IN `p_ContactNumber` VARCHAR(20), IN `p_Email` VARCHAR(255), IN `p_Address` TEXT, IN `p_Position` VARCHAR(100), IN `p_HireDate` DATE)   BEGIN
    INSERT INTO Employee_Info (
        FirstName, LastName, ContactNumber, Email, Address, Position, HireDate
    ) VALUES (
        p_FirstName, p_LastName, p_ContactNumber, p_Email, p_Address, p_Position, p_HireDate
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateEmployeeShift` (IN `p_EmployeeID` INT, IN `p_Date` DATE, IN `p_Duty` VARCHAR(100), IN `p_TimeIn` DATETIME, IN `p_TimeOut` DATETIME)   BEGIN
    INSERT INTO Employee_Shift (EmployeeID, Date, Duty, TimeIn, TimeOut)
    VALUES (p_EmployeeID, p_Date, p_Duty, p_TimeIn, p_TimeOut);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateFixedSchedule` (IN `p_EmployeeID` INT, IN `p_DutyID` INT, IN `p_DayOfWeek` VARCHAR(20), IN `p_StartTime` TIME, IN `p_EndTime` TIME)   BEGIN
    INSERT INTO Employee_Fixed_Schedule (EmployeeID, DutyID, DayOfWeek, StartTime, EndTime)
    VALUES (p_EmployeeID, p_DutyID, p_DayOfWeek, p_StartTime, p_EndTime);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateManager` (IN `p_FirstName` VARCHAR(255), IN `p_LastName` VARCHAR(255), IN `p_ContactNumber` VARCHAR(20), IN `p_Email` VARCHAR(255))   BEGIN
    INSERT INTO Manager_Info (FirstName, LastName, ContactNumber, Email)
    VALUES (p_FirstName, p_LastName, p_ContactNumber, p_Email);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateOvertimeLog` (IN `p_EmployeeID` INT, IN `p_Date` DATE, IN `p_StartTime` TIME, IN `p_EndTime` TIME, IN `p_HoursRendered` DECIMAL(5,2), IN `p_Reason` TEXT, IN `p_ApprovedBy` INT)   BEGIN
    INSERT INTO Overtime_Log (EmployeeID, Date, StartTime, EndTime, HoursRendered, Reason, ApprovedBy)
    VALUES (p_EmployeeID, p_Date, p_StartTime, p_EndTime, p_HoursRendered, p_Reason, p_ApprovedBy);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreatePayroll` (IN `p_EmployeeID` INT, IN `p_PeriodStart` DATE, IN `p_PeriodEnd` DATE, IN `p_TotalHoursWorked` DECIMAL(6,2), IN `p_OTHours` DECIMAL(6,2), IN `p_HourlyRate` DECIMAL(10,2), IN `p_OTRate` DECIMAL(10,2), IN `p_GrossPay` DECIMAL(10,2), IN `p_Deductions` DECIMAL(10,2), IN `p_NetPay` DECIMAL(10,2), IN `p_Status` VARCHAR(50))   BEGIN
    INSERT INTO Payroll_Info (EmployeeID, PeriodStart, PeriodEnd, TotalHoursWorked, OTHours, HourlyRate, OTRate, GrossPay, Deductions, NetPay, Status)
    VALUES (p_EmployeeID, p_PeriodStart, p_PeriodEnd, p_TotalHoursWorked, p_OTHours, p_HourlyRate, p_OTRate, p_GrossPay, p_Deductions, p_NetPay, p_Status);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateRequest` (IN `p_EmployeeID` INT, IN `p_RequestType` VARCHAR(100), IN `p_Reason` TEXT, IN `p_DateRequested` DATE, IN `p_Status` VARCHAR(50), IN `p_ManagerID` INT)   BEGIN
    INSERT INTO Requests (EmployeeID, RequestType, Reason, DateRequested, Status, ManagerID)
    VALUES (p_EmployeeID, p_RequestType, p_Reason, p_DateRequested, p_Status, p_ManagerID);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateShiftDuty` (IN `p_DutyName` VARCHAR(100), IN `p_Description` TEXT)   BEGIN
    INSERT INTO Shift_Duty_Info (DutyName, Description)
    VALUES (p_DutyName, p_Description);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteAttendanceLog` (IN `p_LogID` INT)   BEGIN
    DELETE FROM Attendance_Log WHERE LogID = p_LogID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteEmployee` (IN `p_EmployeeID` INT)   BEGIN
    DELETE FROM Employee_Info WHERE EmployeeID = p_EmployeeID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteEmployeeShift` (IN `p_ShiftID` INT)   BEGIN
    DELETE FROM Employee_Shift WHERE ShiftID = p_ShiftID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteFixedSchedule` (IN `p_ScheduleID` INT)   BEGIN
    DELETE FROM Employee_Fixed_Schedule WHERE ScheduleID = p_ScheduleID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteManager` (IN `p_ManagerID` INT)   BEGIN
    DELETE FROM Manager_Info WHERE ManagerID = p_ManagerID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteOvertimeLog` (IN `p_OTID` INT)   BEGIN
    DELETE FROM Overtime_Log WHERE OTID = p_OTID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeletePayroll` (IN `p_PayrollID` INT)   BEGIN
    DELETE FROM Payroll_Info WHERE PayrollID = p_PayrollID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteRequest` (IN `p_RequestID` INT)   BEGIN
    DELETE FROM Requests WHERE RequestID = p_RequestID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteShiftDuty` (IN `p_DutyID` INT)   BEGIN
    DELETE FROM Shift_Duty_Info WHERE DutyID = p_DutyID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAttendanceLogByID` (IN `p_LogID` INT)   BEGIN
    SELECT * FROM Attendance_Log WHERE LogID = p_LogID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetEmployeeByID` (IN `p_EmployeeID` INT)   BEGIN
    SELECT * FROM Employee_Info WHERE EmployeeID = p_EmployeeID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetEmployeeShiftByID` (IN `p_ShiftID` INT)   BEGIN
    SELECT * FROM Employee_Shift WHERE ShiftID = p_ShiftID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetFixedScheduleByID` (IN `p_ScheduleID` INT)   BEGIN
    SELECT * FROM Employee_Fixed_Schedule WHERE ScheduleID = p_ScheduleID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetManagerByID` (IN `p_ManagerID` INT)   BEGIN
    SELECT * FROM Manager_Info WHERE ManagerID = p_ManagerID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetOvertimeLogByID` (IN `p_OTID` INT)   BEGIN
    SELECT * FROM Overtime_Log WHERE OTID = p_OTID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetPayrollByID` (IN `p_PayrollID` INT)   BEGIN
    SELECT * FROM Payroll_Info WHERE PayrollID = p_PayrollID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetRequestByID` (IN `p_RequestID` INT)   BEGIN
    SELECT * FROM Requests WHERE RequestID = p_RequestID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetShiftDutyByID` (IN `p_DutyID` INT)   BEGIN
    SELECT * FROM Shift_Duty_Info WHERE DutyID = p_DutyID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateAttendanceLog` (IN `p_LogID` INT, IN `p_ShiftID` INT, IN `p_Status` VARCHAR(50), IN `p_LateMinutes` INT, IN `p_Notes` TEXT, IN `p_VerifiedBy` INT)   BEGIN
    UPDATE Attendance_Log SET
        ShiftID = p_ShiftID,
        Status = p_Status,
        LateMinutes = p_LateMinutes,
        Notes = p_Notes,
        VerifiedBy = p_VerifiedBy
    WHERE LogID = p_LogID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateEmployee` (IN `p_EmployeeID` INT, IN `p_FirstName` VARCHAR(255), IN `p_LastName` VARCHAR(255), IN `p_ContactNumber` VARCHAR(20), IN `p_Email` VARCHAR(255), IN `p_Address` TEXT, IN `p_Position` VARCHAR(100), IN `p_HireDate` DATE)   BEGIN
    UPDATE Employee_Info SET
        FirstName = p_FirstName,
        LastName = p_LastName,
        ContactNumber = p_ContactNumber,
        Email = p_Email,
        Address = p_Address,
        Position = p_Position,
        HireDate = p_HireDate
    WHERE EmployeeID = p_EmployeeID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateEmployeeShift` (IN `p_ShiftID` INT, IN `p_EmployeeID` INT, IN `p_Date` DATE, IN `p_Duty` VARCHAR(100), IN `p_TimeIn` DATETIME, IN `p_TimeOut` DATETIME)   BEGIN
    UPDATE Employee_Shift SET
        EmployeeID = p_EmployeeID,
        Date = p_Date,
        Duty = p_Duty,
        TimeIn = p_TimeIn,
        TimeOut = p_TimeOut
    WHERE ShiftID = p_ShiftID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateFixedSchedule` (IN `p_ScheduleID` INT, IN `p_EmployeeID` INT, IN `p_DutyID` INT, IN `p_DayOfWeek` VARCHAR(20), IN `p_StartTime` TIME, IN `p_EndTime` TIME)   BEGIN
    UPDATE Employee_Fixed_Schedule SET
        EmployeeID = p_EmployeeID,
        DutyID = p_DutyID,
        DayOfWeek = p_DayOfWeek,
        StartTime = p_StartTime,
        EndTime = p_EndTime
    WHERE ScheduleID = p_ScheduleID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateManager` (IN `p_ManagerID` INT, IN `p_FirstName` VARCHAR(255), IN `p_LastName` VARCHAR(255), IN `p_ContactNumber` VARCHAR(20), IN `p_Email` VARCHAR(255))   BEGIN
    UPDATE Manager_Info SET
        FirstName = p_FirstName,
        LastName = p_LastName,
        ContactNumber = p_ContactNumber,
        Email = p_Email
    WHERE ManagerID = p_ManagerID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateOvertimeLog` (IN `p_OTID` INT, IN `p_EmployeeID` INT, IN `p_Date` DATE, IN `p_StartTime` TIME, IN `p_EndTime` TIME, IN `p_HoursRendered` DECIMAL(5,2), IN `p_Reason` TEXT, IN `p_ApprovedBy` INT)   BEGIN
    UPDATE Overtime_Log SET
        EmployeeID = p_EmployeeID,
        Date = p_Date,
        StartTime = p_StartTime,
        EndTime = p_EndTime,
        HoursRendered = p_HoursRendered,
        Reason = p_Reason,
        ApprovedBy = p_ApprovedBy
    WHERE OTID = p_OTID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdatePayroll` (IN `p_PayrollID` INT, IN `p_EmployeeID` INT, IN `p_PeriodStart` DATE, IN `p_PeriodEnd` DATE, IN `p_TotalHoursWorked` DECIMAL(6,2), IN `p_OTHours` DECIMAL(6,2), IN `p_HourlyRate` DECIMAL(10,2), IN `p_OTRate` DECIMAL(10,2), IN `p_GrossPay` DECIMAL(10,2), IN `p_Deductions` DECIMAL(10,2), IN `p_NetPay` DECIMAL(10,2), IN `p_Status` VARCHAR(50))   BEGIN
    UPDATE Payroll_Info SET
        EmployeeID = p_EmployeeID,
        PeriodStart = p_PeriodStart,
        PeriodEnd = p_PeriodEnd,
        TotalHoursWorked = p_TotalHoursWorked,
        OTHours = p_OTHours,
        HourlyRate = p_HourlyRate,
        OTRate = p_OTRate,
        GrossPay = p_GrossPay,
        Deductions = p_Deductions,
        NetPay = p_NetPay,
        Status = p_Status
    WHERE PayrollID = p_PayrollID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateRequest` (IN `p_RequestID` INT, IN `p_EmployeeID` INT, IN `p_RequestType` VARCHAR(100), IN `p_Reason` TEXT, IN `p_DateRequested` DATE, IN `p_Status` VARCHAR(50), IN `p_ManagerID` INT)   BEGIN
    UPDATE Requests SET
        EmployeeID = p_EmployeeID,
        RequestType = p_RequestType,
        Reason = p_Reason,
        DateRequested = p_DateRequested,
        Status = p_Status,
        ManagerID = p_ManagerID
    WHERE RequestID = p_RequestID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateShiftDuty` (IN `p_DutyID` INT, IN `p_DutyName` VARCHAR(100), IN `p_Description` TEXT)   BEGIN
    UPDATE Shift_Duty_Info SET
        DutyName = p_DutyName,
        Description = p_Description
    WHERE DutyID = p_DutyID;
END$$

DELIMITER $$
CREATE PROCEDURE CrudEmployeeAttendance(
    IN action VARCHAR(10),
    IN p_id INT,
    IN p_employee_id INT,
    IN p_shift_id INT,
    IN p_attendance_date DATE,
    IN p_check_in TIME,
    IN p_check_out TIME,
    IN p_status VARCHAR(20),
    IN p_remarks TEXT
)
BEGIN
    IF action = 'CREATE' THEN
        INSERT INTO employee_attendance (employee_id, shift_id, attendance_date, check_in, check_out, status, remarks)
        VALUES (p_employee_id, p_shift_id, p_attendance_date, p_check_in, p_check_out, p_status, p_remarks);
    ELSEIF action = 'READ' THEN
        SELECT * FROM employee_attendance WHERE id = p_id;
    ELSEIF action = 'UPDATE' THEN
        UPDATE employee_attendance
        SET employee_id = p_employee_id, shift_id = p_shift_id, attendance_date = p_attendance_date,
            check_in = p_check_in, check_out = p_check_out, status = p_status, remarks = p_remarks
        WHERE id = p_id;
    ELSEIF action = 'DELETE' THEN
        DELETE FROM employee_attendance WHERE id = p_id;
    END IF;
END$$

CREATE PROCEDURE SubmitEmployeeRequest(
    IN p_employee_id INT,
    IN p_request_message TEXT
)
BEGIN
    INSERT INTO employee_requests (employee_id, request_message)
    VALUES (p_employee_id, p_request_message);
END$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_log`
--

CREATE TABLE `attendance_log` (
  `LogID` int(11) NOT NULL,
  `ShiftID` int(11) DEFAULT NULL,
  `Status` varchar(50) DEFAULT NULL,
  `LateMinutes` int(11) DEFAULT NULL,
  `Notes` text DEFAULT NULL,
  `VerifiedBy` int(11) DEFAULT NULL
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
  `EndTime` time DEFAULT NULL
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
  `HireDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `TimeOut` datetime DEFAULT NULL
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
  `Email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `ApprovedBy` int(11) DEFAULT NULL
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
  `Status` varchar(50) DEFAULT NULL
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
  `ManagerID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shift_duty_info`
--

CREATE TABLE `shift_duty_info` (
  `DutyID` int(11) NOT NULL,
  `DutyName` varchar(100) DEFAULT NULL,
  `Description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance_log`
--
ALTER TABLE `attendance_log`
  ADD PRIMARY KEY (`LogID`),
  ADD KEY `ShiftID` (`ShiftID`),
  ADD KEY `VerifiedBy` (`VerifiedBy`);

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
-- AUTO_INCREMENT for table `attendance_log`
--
ALTER TABLE `attendance_log`
  MODIFY `LogID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_fixed_schedule`
--
ALTER TABLE `employee_fixed_schedule`
  MODIFY `ScheduleID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_info`
--
ALTER TABLE `employee_info`
  MODIFY `EmployeeID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_shift`
--
ALTER TABLE `employee_shift`
  MODIFY `ShiftID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `manager_info`
--
ALTER TABLE `manager_info`
  MODIFY `ManagerID` int(11) NOT NULL AUTO_INCREMENT;

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
  ADD CONSTRAINT `attendance_log_ibfk_1` FOREIGN KEY (`ShiftID`) REFERENCES `employee_shift` (`ShiftID`),
  ADD CONSTRAINT `attendance_log_ibfk_2` FOREIGN KEY (`VerifiedBy`) REFERENCES `manager_info` (`ManagerID`);

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
