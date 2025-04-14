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

-- CRUD Procedure for Employee_Info
CREATE PROCEDURE CrudEmployeeInfo (
    IN p_action VARCHAR(10),
    IN p_employee_id INT,
    IN p_first_name VARCHAR(100),
    IN p_last_name VARCHAR(100),
    IN p_contact_number VARCHAR(20),
    IN p_email VARCHAR(100),
    IN p_address TEXT,
    IN p_position VARCHAR(50),
    IN p_hire_date DATE
)
BEGIN
    -- CREATE (Insert New Employee)
    IF p_action = 'CREATE' THEN
        INSERT INTO Employee_Info (FirstName, LastName, ContactNumber, Email, Address, Position, HireDate)
        VALUES (p_first_name, p_last_name, p_contact_number, p_email, p_address, p_position, p_hire_date);

    -- READ (Get All Employees)
    ELSEIF p_action = 'READ' THEN
        SELECT * FROM Employee_Info;

    -- UPDATE (Modify Employee Record)
    ELSEIF p_action = 'UPDATE' THEN
        UPDATE Employee_Info
        SET FirstName = p_first_name, LastName = p_last_name, ContactNumber = p_contact_number,
            Email = p_email, Address = p_address, Position = p_position, HireDate = p_hire_date
        WHERE EmployeeID = p_employee_id;

    -- DELETE (Remove Employee Record)
    ELSEIF p_action = 'DELETE' THEN
        DELETE FROM Employee_Info WHERE EmployeeID = p_employee_id;
    END IF;
END$$

-- CRUD Procedure for Manager_Info
CREATE PROCEDURE CrudManagerInfo (
    IN p_action VARCHAR(10),
    IN p_manager_id INT,
    IN p_first_name VARCHAR(100),
    IN p_last_name VARCHAR(100),
    IN p_contact_number VARCHAR(20),
    IN p_email VARCHAR(100)
)
BEGIN
    -- CREATE (Insert New Manager)
    IF p_action = 'CREATE' THEN
        INSERT INTO Manager_Info (FirstName, LastName, ContactNumber, Email)
        VALUES (p_first_name, p_last_name, p_contact_number, p_email);

    -- READ (Get All Managers)
    ELSEIF p_action = 'READ' THEN
        SELECT * FROM Manager_Info;

    -- UPDATE (Modify Manager Record)
    ELSEIF p_action = 'UPDATE' THEN
        UPDATE Manager_Info
        SET FirstName = p_first_name, LastName = p_last_name, ContactNumber = p_contact_number, Email = p_email
        WHERE ManagerID = p_manager_id;

    -- DELETE (Remove Manager Record)
    ELSEIF p_action = 'DELETE' THEN
        DELETE FROM Manager_Info WHERE ManagerID = p_manager_id;
    END IF;
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

-- --------------------------------------------------------

--
-- Table structure for table `Employee_Info`
--

CREATE TABLE Employee_Info (
    EmployeeID INT AUTO_INCREMENT PRIMARY KEY,
    FirstName VARCHAR(100),
    LastName VARCHAR(100),
    ContactNumber VARCHAR(20),
    Email VARCHAR(100),
    Address TEXT,
    Position VARCHAR(50),
    HireDate DATE
);

-- --------------------------------------------------------

--
-- Table structure for table `Manager_Info`
--

CREATE TABLE Manager_Info (
    ManagerID INT AUTO_INCREMENT PRIMARY KEY,
    FirstName VARCHAR(100),
    LastName VARCHAR(100),
    ContactNumber VARCHAR(20),
    Email VARCHAR(100)
);

-- --------------------------------------------------------

--
-- Table structure for table `Shift_Duty_Info`
--

CREATE TABLE Shift_Duty_Info (
    DutyID INT AUTO_INCREMENT PRIMARY KEY,
    DutyName VARCHAR(100),
    Description TEXT
);

-- --------------------------------------------------------

--
-- Table structure for table `Employee_Shift`
--

CREATE TABLE Employee_Shift (
    ShiftID INT AUTO_INCREMENT PRIMARY KEY,
    EmployeeID INT,
    Date DATE,
    Duty VARCHAR(100),
    TimeIn DATETIME,
    TimeOut DATETIME,
    FOREIGN KEY (EmployeeID) REFERENCES Employee_Info(EmployeeID)
);

-- --------------------------------------------------------

--
-- Table structure for table `Attendance_Log`
--

CREATE TABLE Attendance_Log (
    LogID INT AUTO_INCREMENT PRIMARY KEY,
    ShiftID INT,
    Status VARCHAR(50),
    LateMinutes INT,
    Notes TEXT,
    VerifiedBy INT,
    FOREIGN KEY (ShiftID) REFERENCES Employee_Shift(ShiftID),
    FOREIGN KEY (VerifiedBy) REFERENCES Manager_Info(ManagerID)
);

-- --------------------------------------------------------

--
-- Table structure for table `Employee_Fixed_Schedule`
--

CREATE TABLE Employee_Fixed_Schedule (
    ScheduleID INT AUTO_INCREMENT PRIMARY KEY,
    EmployeeID INT,
    DutyID INT,
    DayOfWeek VARCHAR(20),
    StartTime TIME,
    EndTime TIME,
    FOREIGN KEY (EmployeeID) REFERENCES Employee_Info(EmployeeID),
    FOREIGN KEY (DutyID) REFERENCES Shift_Duty_Info(DutyID)
);

-- --------------------------------------------------------

--
-- Table structure for table `Requests`
--

CREATE TABLE Requests (
    RequestID INT AUTO_INCREMENT PRIMARY KEY,
    EmployeeID INT,
    RequestType VARCHAR(50),
    Reason TEXT,
    DateRequested DATE,
    Status VARCHAR(50),
    ManagerID INT,
    FOREIGN KEY (EmployeeID) REFERENCES Employee_Info(EmployeeID),
    FOREIGN KEY (ManagerID) REFERENCES Manager_Info(ManagerID)
);

-- --------------------------------------------------------

--
-- Table structure for table `Overtime_Log`
--

CREATE TABLE Overtime_Log (
    OTID INT AUTO_INCREMENT PRIMARY KEY,
    EmployeeID INT,
    Date DATE,
    StartTime TIME,
    EndTime TIME,
    HoursRendered DECIMAL(5,2),
    Reason TEXT,
    ApprovedBy INT,
    FOREIGN KEY (EmployeeID) REFERENCES Employee_Info(EmployeeID),
    FOREIGN KEY (ApprovedBy) REFERENCES Manager_Info(ManagerID)
);

-- --------------------------------------------------------

--
-- Table structure for table `Payroll_Info`
--

CREATE TABLE Payroll_Info (
    PayrollID INT AUTO_INCREMENT PRIMARY KEY,
    EmployeeID INT,
    PeriodStart DATE,
    PeriodEnd DATE,
    TotalHoursWorked DECIMAL(6,2),
    OTHours DECIMAL(6,2),
    HourlyRate DECIMAL(6,2) DEFAULT 60,
    OTRate DECIMAL(6,2),
    GrossPay DECIMAL(8,2),
    Deductions DECIMAL(8,2),
    NetPay DECIMAL(8,2),
    Status VARCHAR(50),
    FOREIGN KEY (EmployeeID) REFERENCES Employee_Info(EmployeeID)
);

-- --------------------------------------------------------

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
