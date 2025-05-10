-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 10, 2025 at 04:35 PM
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateEmployeeWithProfilePicture` (IN `p_EmployeeID` VARCHAR(10), IN `p_ProfilePicture` VARCHAR(255), IN `p_FirstName` VARCHAR(100), IN `p_LastName` VARCHAR(100), IN `p_ContactNumber` VARCHAR(15), IN `p_Email` VARCHAR(100), IN `p_Address` TEXT, IN `p_Position` VARCHAR(100), IN `p_HireDate` DATE, IN `p_Password` VARCHAR(255))   BEGIN
    INSERT INTO employee_info (
        EmployeeID, ProfilePicture, FirstName, LastName, ContactNumber, Email, Address, Position, HireDate, Password
    ) VALUES (
        p_EmployeeID, p_ProfilePicture, p_FirstName, p_LastName, p_ContactNumber, p_Email, p_Address, p_Position, p_HireDate, p_Password
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateManager` (IN `p_ManagerID` VARCHAR(7), IN `p_FirstName` VARCHAR(100), IN `p_LastName` VARCHAR(100), IN `p_ContactNumber` VARCHAR(15), IN `p_Email` VARCHAR(100), IN `p_Password` VARCHAR(255), IN `p_ProfilePicture` VARCHAR(255))   BEGIN
    INSERT INTO manager_info (
        ManagerID, FirstName, LastName, ContactNumber, Email, Password, ProfilePicture
    ) VALUES (
        p_ManagerID, p_FirstName, p_LastName, p_ContactNumber, p_Email, p_Password, p_ProfilePicture
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateManagerWithProfilePicture` (IN `p_ProfilePicture` VARCHAR(255), IN `p_FirstName` VARCHAR(100), IN `p_LastName` VARCHAR(100), IN `p_Email` VARCHAR(100), IN `p_Password` VARCHAR(255))   BEGIN
    INSERT INTO manager_info (ProfilePicture, FirstName, LastName, Email, Password)
    VALUES (p_ProfilePicture, p_FirstName, p_LastName, p_Email, p_Password);
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAllEmployees` ()   BEGIN
    SELECT
    	ProfilePicture,
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
    SELECT 
        ManagerID, 
        FirstName, 
        LastName, 
        Email, 
        ProfilePicture
    FROM manager_info;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAttendanceLogs` ()   BEGIN
    SELECT 
        attendance_log.EmployeeID, 
        attendance_log.Date, 
        attendance_log.CheckIn, 
        attendance_log.CheckOut, 
        attendance_log.Status, 
        employee_info.ProfilePicture
    FROM attendance_log
    INNER JOIN employee_info ON attendance_log.EmployeeID = employee_info.EmployeeID
    ORDER BY attendance_log.Date DESC, 
             attendance_log.CheckOut DESC, 
             attendance_log.CheckIn DESC;
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
    SELECT 
        ManagerID, 
        FirstName, 
        LastName, 
        ContactNumber, 
        Email, 
        ProfilePicture
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertRequest` (IN `p_employee_id` INT, IN `p_request_type` ENUM('Change Shift','Sick Leave','Vacation Leave'), IN `p_details` TEXT)   BEGIN
    INSERT INTO requests (EmployeeID, RequestType, Details, CreatedAt)
    VALUES (p_employee_id, p_request_type, p_details, NOW());
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `SearchEmployees` (IN `search_term` VARCHAR(255))   BEGIN
    SELECT 
        EmployeeID,
        ProfilePicture,
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
        FirstName LIKE CONCAT('%', search_term, '%') OR
        LastName LIKE CONCAT('%', search_term, '%') OR
        Email LIKE CONCAT('%', search_term, '%') OR
        EmployeeID LIKE CONCAT('%', search_term, '%');
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SearchManagers` (IN `search_term` VARCHAR(255))   BEGIN
    SELECT 
        ManagerID,
        ProfilePicture,
        FirstName,
        LastName,
        Email
    FROM 
        manager_info
    WHERE 
        FirstName LIKE CONCAT('%', search_term, '%') OR
        LastName LIKE CONCAT('%', search_term, '%') OR
        Email LIKE CONCAT('%', search_term, '%') OR
        ManagerID LIKE CONCAT('%', search_term, '%');
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateEmployeeWithoutPassword` (IN `p_EmployeeID` INT, IN `p_ProfilePicture` VARCHAR(255), IN `p_FirstName` VARCHAR(100), IN `p_LastName` VARCHAR(100), IN `p_ContactNumber` VARCHAR(15), IN `p_Email` VARCHAR(100), IN `p_Address` TEXT, IN `p_Position` VARCHAR(100), IN `p_HireDate` DATE)   BEGIN
    UPDATE employee_info
    SET 
        ProfilePicture = p_ProfilePicture,
        FirstName = p_FirstName,
        LastName = p_LastName,
        ContactNumber = p_ContactNumber,
        Email = p_Email,
        Address = p_Address,
        Position = p_Position,
        HireDate = p_HireDate
    WHERE EmployeeID = p_EmployeeID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateEmployeeWithPassword` (IN `p_EmployeeID` INT, IN `p_ProfilePicture` VARCHAR(255), IN `p_FirstName` VARCHAR(100), IN `p_LastName` VARCHAR(100), IN `p_ContactNumber` VARCHAR(15), IN `p_Email` VARCHAR(100), IN `p_Address` TEXT, IN `p_Position` VARCHAR(100), IN `p_HireDate` DATE, IN `p_Password` VARCHAR(255))   BEGIN
    UPDATE employee_info
    SET 
        ProfilePicture = p_ProfilePicture,
        FirstName = p_FirstName,
        LastName = p_LastName,
        ContactNumber = p_ContactNumber,
        Email = p_Email,
        Address = p_Address,
        Position = p_Position,
        HireDate = p_HireDate,
        Password = p_Password
    WHERE EmployeeID = p_EmployeeID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateManager` (IN `p_ManagerID` INT, IN `p_FirstName` VARCHAR(255), IN `p_LastName` VARCHAR(255), IN `p_Email` VARCHAR(255))   BEGIN
    UPDATE manager_info
    SET FirstName = p_FirstName,
        LastName = p_LastName,
        Email = p_Email
    WHERE ManagerID = p_ManagerID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateManagerWithoutPassword` (IN `p_OriginalManagerID` INT, IN `p_ManagerID` VARCHAR(7), IN `p_ProfilePicture` VARCHAR(255), IN `p_FirstName` VARCHAR(100), IN `p_LastName` VARCHAR(100), IN `p_ContactNumber` VARCHAR(15), IN `p_Email` VARCHAR(100))   BEGIN
    UPDATE manager_info
    SET 
        ManagerID = p_ManagerID,
        ProfilePicture = p_ProfilePicture,
        FirstName = p_FirstName,
        LastName = p_LastName,
        ContactNumber = p_ContactNumber,
        Email = p_Email
    WHERE ManagerID = p_OriginalManagerID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateManagerWithPassword` (IN `p_OriginalManagerID` INT, IN `p_ManagerID` VARCHAR(7), IN `p_ProfilePicture` VARCHAR(255), IN `p_FirstName` VARCHAR(100), IN `p_LastName` VARCHAR(100), IN `p_ContactNumber` VARCHAR(15), IN `p_Email` VARCHAR(100), IN `p_Password` VARCHAR(255))   BEGIN
    UPDATE manager_info
    SET 
        ManagerID = p_ManagerID,
        ProfilePicture = p_ProfilePicture,
        FirstName = p_FirstName,
        LastName = p_LastName,
        ContactNumber = p_ContactNumber,
        Email = p_Email,
        Password = p_Password
    WHERE ManagerID = p_OriginalManagerID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateManagerWithProfilePicture` (IN `p_ManagerID` INT, IN `p_ProfilePicture` VARCHAR(255), IN `p_FirstName` VARCHAR(100), IN `p_LastName` VARCHAR(100), IN `p_Email` VARCHAR(100))   BEGIN
    UPDATE manager_info
    SET 
        ProfilePicture = p_ProfilePicture,
        FirstName = p_FirstName,
        LastName = p_LastName,
        Email = p_Email
    WHERE ManagerID = p_ManagerID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateTimeOut` (IN `emp_id` INT, IN `log_date` DATE, IN `time_out` TIME)   BEGIN
    UPDATE attendance_log 
    SET CheckOut = time_out 
    WHERE EmployeeID = emp_id AND Date = log_date;
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
  `Status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_log`
--

INSERT INTO `attendance_log` (`EmployeeID`, `Date`, `CheckIn`, `CheckOut`, `Status`) VALUES
(10009, '2025-05-09', '06:00:00', '13:00:00', 'Present'),
(10009, '2025-05-10', '01:00:21', '01:00:29', 'Present'),
(10002, '2025-05-10', '01:06:50', '01:49:46', 'Present'),
(10006, '2025-05-10', '01:33:49', '01:52:41', 'Present'),
(10005, '2025-05-10', '10:03:39', '11:35:32', 'Present'),
(10004, '2025-05-10', '14:23:08', '14:25:08', 'Present');

-- --------------------------------------------------------

--
-- Table structure for table `employee_info`
--

CREATE TABLE `employee_info` (
  `EmployeeID` int(5) NOT NULL,
  `ProfilePicture` varchar(255) DEFAULT NULL,
  `FirstName` varchar(255) DEFAULT NULL,
  `LastName` varchar(255) DEFAULT NULL,
  `ContactNumber` varchar(20) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Address` text DEFAULT NULL,
  `Position` varchar(100) DEFAULT NULL,
  `HireDate` date DEFAULT NULL,
  `Password` varchar(5) NOT NULL,
  `shifts` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_info`
--

INSERT INTO `employee_info` (`EmployeeID`, `ProfilePicture`, `FirstName`, `LastName`, `ContactNumber`, `Email`, `Address`, `Position`, `HireDate`, `Password`, `shifts`) VALUES
(10002, '../photos/del rosario sean.webp', 'Sean Martin', 'Del Rosario', '09065816503', 'seanmdelrosario@gmail.com', 'Lipa City Batangas', 'Cook', '2023-11-15', '$2y$1', NULL),
(10004, '../photos/talas abby.webp', 'Abby', 'Talas', '09201234567', 'ana.lopez@example.com', '321 Rizal Blvd., Pasig City', 'Cashier', '2021-09-30', '45678', NULL),
(10005, '../photos/paulite jarell.webp', 'Jarell', 'Paulite', '09211234567', 'carlos.torres@example.com', '654 Katipunan Ave., Manila', 'Burger', '2020-01-20', '56789', NULL),
(10006, '../photos/angelo corcega.webp', 'Angelo', 'Corcega', '09653527892', 'bembabys@gmail.com', 'New York City, Philippines', 'Nuggets Cook', '2025-05-02', '$2y$1', NULL),
(10008, '../photos/image_2025-05-10_092622601.png', 'Janna', 'Baluyot', '099365841', 'baluyot@gmail.com', 'Monte Carlo', 'Janitor', '2025-05-23', '$2y$1', NULL),
(10009, '../photos/rodrigez jamma.webp', 'Jamma', 'Rodrigez', '099134567809', 'mtjuls41@gmail.com', 'Munting Tubig', 'cashier', '2024-05-09', '$2y$1', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `manager_info`
--

CREATE TABLE `manager_info` (
  `ManagerID` int(7) NOT NULL,
  `ProfilePicture` varchar(255) DEFAULT NULL,
  `FirstName` varchar(255) DEFAULT NULL,
  `LastName` varchar(255) DEFAULT NULL,
  `ContactNumber` varchar(20) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manager_info`
--

INSERT INTO `manager_info` (`ManagerID`, `ProfilePicture`, `FirstName`, `LastName`, `ContactNumber`, `Email`, `Password`) VALUES
(1234567, '../photos/image_2025-05-10_110445256.png', 'Justine Kyle', 'Balubal', '09669477669', 'balubalcutie69@gmail.com', '$2y$10$8ACI7gLNbpNtlyycbyG3/ODRu0nKgJbOvrqrEzNsB0Y2dLyjCG3nW'),
(2917714, '../photos/image_2025-05-10_102516144.png', 'Cheska', 'De Castro', '09938309279', 'cheskadecastro00@gmail.com', '$2y$10$m9VBJP2Q9xsu.XSidjPc9e6yM9LBE.Jd1wsZtVYXdQjLtK5tZEvZK');

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`role_id`, `role_name`) VALUES
(1, 'Cashier'),
(2, 'Cook'),
(3, 'Dishwasher'),
(4, 'Janitor'),
(5, 'Server');

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `RequestID` int(11) NOT NULL,
  `EmployeeID` int(11) NOT NULL,
  `RequestType` enum('Change Shift','Sick Leave','Vacation Leave') NOT NULL,
  `Details` text DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`RequestID`, `EmployeeID`, `RequestType`, `Details`, `CreatedAt`) VALUES
(1, 10002, 'Change Shift', '{\"current_shift\":\"Day Shift (8AM-5PM)\",\"requested_shift\":\"Night Shift (10PM-7AM)\",\"shift_change_date\":\"2025-05-15\"}', '2025-05-10 14:25:52'),
(2, 10002, 'Sick Leave', '{\"reason\":\"I am sick Coff Coff\"}', '2025-05-10 14:26:02'),
(3, 10006, 'Sick Leave', '{\"reason\":\"I am sickkk COFF COFFF\"}', '2025-05-10 14:27:44'),
(4, 10005, 'Vacation Leave', '{\"reason\":\"Gusto ko na mag dagat\"}', '2025-05-10 14:28:44');

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `shift_id` int(11) NOT NULL,
  `shift_name` varchar(50) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`shift_id`, `shift_name`, `start_time`, `end_time`) VALUES
(1, 'Day Duty', '08:00:00', '17:00:00'),
(2, 'Night Duty', '18:00:00', '00:00:00'),
(3, 'Graveyard Shift', '01:00:00', '08:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance_log`
--
ALTER TABLE `attendance_log`
  ADD KEY `fk_attendance_employee` (`EmployeeID`);

--
-- Indexes for table `employee_info`
--
ALTER TABLE `employee_info`
  ADD PRIMARY KEY (`EmployeeID`);

--
-- Indexes for table `manager_info`
--
ALTER TABLE `manager_info`
  ADD PRIMARY KEY (`ManagerID`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`RequestID`),
  ADD KEY `EmployeeID` (`EmployeeID`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`shift_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `employee_info`
--
ALTER TABLE `employee_info`
  MODIFY `EmployeeID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10010;

--
-- AUTO_INCREMENT for table `manager_info`
--
ALTER TABLE `manager_info`
  MODIFY `ManagerID` int(7) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2917716;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `RequestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `shift_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance_log`
--
ALTER TABLE `attendance_log`
  ADD CONSTRAINT `fk_attendance_employee` FOREIGN KEY (`EmployeeID`) REFERENCES `employee_info` (`EmployeeID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`EmployeeID`) REFERENCES `employee_info` (`EmployeeID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
