-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 10, 2025 at 08:37 PM
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
CREATE DEFINER=`root`@`localhost` PROCEDURE `CheckEmployeeExists` (IN `emp_id` INT)   BEGIN
    SELECT 1 FROM employee_info WHERE EmployeeID = emp_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateEmployee` (IN `p_FirstName` VARCHAR(255), IN `p_LastName` VARCHAR(255), IN `p_ContactNumber` VARCHAR(20), IN `p_Email` VARCHAR(255), IN `p_Address` TEXT, IN `p_role_id` INT, IN `p_shift_id` INT, IN `p_HireDate` DATE, IN `p_Password` VARCHAR(255))   BEGIN
    INSERT INTO employee_info (
        FirstName,
        LastName,
        ContactNumber,
        Email,
        Address,
        role_id,
        shift_id,
        HireDate,
        Password
    ) VALUES (
        p_FirstName,
        p_LastName,
        p_ContactNumber,
        p_Email,
        p_Address,
        p_role_id,
        p_shift_id,
        p_HireDate,
        p_Password
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateEmployeeWithProfilePicture` (IN `p_EmployeeID` INT, IN `p_ProfilePicture` VARCHAR(255), IN `p_FirstName` VARCHAR(100), IN `p_LastName` VARCHAR(100), IN `p_ContactNumber` VARCHAR(15), IN `p_Email` VARCHAR(100), IN `p_Address` TEXT, IN `p_role_id` INT, IN `p_shift_id` INT, IN `p_HireDate` DATE, IN `p_Password` VARCHAR(255))   BEGIN
    INSERT INTO employee_info (
        EmployeeID,
        ProfilePicture,
        FirstName,
        LastName,
        ContactNumber,
        Email,
        Address,
        role_id,
        shift_id,
        HireDate,
        Password
    ) VALUES (
        p_EmployeeID,
        p_ProfilePicture,
        p_FirstName,
        p_LastName,
        p_ContactNumber,
        p_Email,
        p_Address,
        p_role_id,
        p_shift_id,
        p_HireDate,
        p_Password
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAllEmployees` ()   BEGIN
    SELECT 
        e.EmployeeID,
        e.FirstName,
        e.LastName,
        e.ContactNumber,
        e.Email,
        e.Address,
        e.role_id,
        e.shift_id,
        e.HireDate,
        e.ProfilePicture,
        p.role_name AS Position,
        s.shift_name AS Shift
    FROM employee_info e
    LEFT JOIN positions p ON e.role_id = p.role_id
    LEFT JOIN shifts s ON e.shift_id = s.shift_id;
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAllPositions` ()   BEGIN
    SELECT role_id, role_name FROM positions;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAllRequests` ()   BEGIN
    SELECT 
        RequestID,
        EmployeeID,
        RequestType,
        Details,
        CreatedAt
    FROM requests;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAllShifts` ()   BEGIN
    SELECT shift_id, shift_name FROM shifts;
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetEmployeeByID` (IN `emp_id` INT)   BEGIN
    SELECT 
        e.EmployeeID,
        e.FirstName,
        e.LastName,
        e.ContactNumber,
        e.Email,
        e.Address,
        e.role_id,
        e.shift_id,
        e.HireDate,
        e.ProfilePicture,
        p.role_name AS Position,
        s.shift_name AS Shift
    FROM employee_info e
    LEFT JOIN positions p ON e.role_id = p.role_id
    LEFT JOIN shifts s ON e.shift_id = s.shift_id
    WHERE e.EmployeeID = emp_id;
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertRequest` (IN `p_EmployeeID` INT, IN `p_RequestType` VARCHAR(50), IN `p_Details` TEXT, IN `p_CreatedAt` DATE)   BEGIN
    INSERT INTO requests (EmployeeID, RequestType, Details, CreatedAt)
    VALUES (p_EmployeeID, p_RequestType, p_Details, p_CreatedAt);
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `NoteRequest` (IN `p_RequestID` INT)   BEGIN
    DELETE FROM requests WHERE RequestID = p_RequestID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SearchEmployees` (IN `searchTerm` VARCHAR(255))   BEGIN
    SELECT
    e.ProfilePicture,
        e.EmployeeID,
        e.FirstName,
        e.LastName,
        e.ContactNumber,
        e.Email,
        e.Address,
        e.HireDate,
        p.role_name AS Position,
        s.shift_name AS Shift
    FROM employee_info e
    LEFT JOIN positions p ON e.role_id = p.role_id
    LEFT JOIN shifts s ON e.shift_id = s.shift_id
    WHERE 
        e.FirstName LIKE CONCAT('%', searchTerm, '%') OR
        e.LastName LIKE CONCAT('%', searchTerm, '%') OR
        p.role_name LIKE CONCAT('%', searchTerm, '%') OR
        s.shift_name LIKE CONCAT('%', searchTerm, '%');
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateEmployee` (IN `p_EmployeeID` INT, IN `p_FirstName` VARCHAR(255), IN `p_LastName` VARCHAR(255), IN `p_ContactNumber` VARCHAR(20), IN `p_Email` VARCHAR(255), IN `p_Address` TEXT, IN `p_role_id` INT, IN `p_shift_id` INT, IN `p_HireDate` DATE)   BEGIN
    UPDATE employee_info
    SET 
        FirstName = p_FirstName,
        LastName = p_LastName,
        ContactNumber = p_ContactNumber,
        Email = p_Email,
        Address = p_Address,
        role_id = p_role_id,
        shift_id = p_shift_id,
        HireDate = p_HireDate
    WHERE 
        EmployeeID = p_EmployeeID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateEmployeeWithoutPassword` (IN `p_EmployeeID` INT, IN `p_ProfilePicture` VARCHAR(255), IN `p_FirstName` VARCHAR(100), IN `p_LastName` VARCHAR(100), IN `p_ContactNumber` VARCHAR(15), IN `p_Email` VARCHAR(100), IN `p_Address` TEXT, IN `p_role_id` INT, IN `p_shift_id` INT, IN `p_HireDate` DATE)   BEGIN
    UPDATE employee_info
    SET 
        ProfilePicture = p_ProfilePicture,
        FirstName = p_FirstName,
        LastName = p_LastName,
        ContactNumber = p_ContactNumber,
        Email = p_Email,
        Address = p_Address,
        role_id = p_role_id,
        shift_id = p_shift_id,
        HireDate = p_HireDate
    WHERE EmployeeID = p_EmployeeID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateEmployeeWithPassword` (IN `p_EmployeeID` INT, IN `p_ProfilePicture` VARCHAR(255), IN `p_FirstName` VARCHAR(100), IN `p_LastName` VARCHAR(100), IN `p_ContactNumber` VARCHAR(15), IN `p_Email` VARCHAR(100), IN `p_Address` TEXT, IN `p_role_id` INT, IN `p_shift_id` INT, IN `p_HireDate` DATE, IN `p_Password` VARCHAR(255))   BEGIN
    UPDATE employee_info
    SET 
        ProfilePicture = p_ProfilePicture,
        FirstName = p_FirstName,
        LastName = p_LastName,
        ContactNumber = p_ContactNumber,
        Email = p_Email,
        Address = p_Address,
        role_id = p_role_id,
        shift_id = p_shift_id,
        HireDate = p_HireDate,
        Password = p_Password
    WHERE EmployeeID = p_EmployeeID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateEmployeeWithProfilePicture` (IN `p_EmployeeID` INT, IN `p_ProfilePicture` VARCHAR(255), IN `p_FirstName` VARCHAR(100), IN `p_LastName` VARCHAR(100), IN `p_ContactNumber` VARCHAR(15), IN `p_Email` VARCHAR(100), IN `p_Address` TEXT, IN `p_role_id` INT, IN `p_shift_id` INT, IN `p_HireDate` DATE)   BEGIN
    UPDATE employee_info
    SET 
        ProfilePicture = p_ProfilePicture,
        FirstName = p_FirstName,
        LastName = p_LastName,
        ContactNumber = p_ContactNumber,
        Email = p_Email,
        Address = p_Address,
        role_id = p_role_id,
        shift_id = p_shift_id,
        HireDate = p_HireDate
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
(10004, '2025-05-10', '14:23:08', '14:25:08', 'Present'),
(10001, '2025-05-11', '00:43:19', '00:43:49', 'Present'),
(10008, '2025-05-11', '02:31:25', '02:31:42', 'Present');

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
  `role_id` int(11) NOT NULL,
  `HireDate` date DEFAULT NULL,
  `Password` varchar(255) NOT NULL,
  `shift_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_info`
--

INSERT INTO `employee_info` (`EmployeeID`, `ProfilePicture`, `FirstName`, `LastName`, `ContactNumber`, `Email`, `Address`, `role_id`, `HireDate`, `Password`, `shift_id`) VALUES
(10001, '../photos/489848246_1876535986218160_5401810271376473472_n.jpg', 'Gian Carlo', 'Diaz', '09968574147', 'giandiaz@gmail.com', 'Sabang, Monte Carlo, Lipa City', 4, '2025-05-11', '$2y$10$RfDWHAfhS6Y7wxK/kCuIAuImMbfViAs.kNJXkvWRxI0nUZHZMZ.UK', 2),
(10002, '../photos/del rosario sean.webp', 'Sean Martin', 'Del Rosario', '09065816503', 'seanmdelrosario@gmail.com', 'Lipa City Batangas', 1, '2023-11-15', '$2y$10$Xm3GIivkTDs8jPlsQFQ7yeApe.un1f.9CDz4yAFml8Z0Hhb9lXRo.', 1),
(10004, '../photos/talas abby.webp', 'Abby', 'Talas', '09201234567', 'ana.lopez@example.com', '321 Rizal Blvd., Pasig City', 2, '2021-09-30', '$2y$10$LWReXOM81inrtpklfZEt6epbqHHpiWtZ7aViMl/z/OdYtq.k86/FC', 1),
(10005, '../photos/paulite jarell.webp', 'Jarell', 'Paulite', '09211234567', 'carlos.torres@example.com', '654 Katipunan Ave., Manila', 3, '2020-01-20', '$2y$10$KAJtBTTy3Ib4czhmz3MRMe3M3PeAd3L7jzP1a2DX8DHqwr8hu0phO', 1),
(10006, '../photos/angelo corcega.webp', 'Angelo', 'Corcega', '09653527892', 'bembabys@gmail.com', 'New York City, Philippines', 4, '2025-05-02', '$2y$10$A0BbwW1JMIW0A4bZyQ0DuO6E0UbALNUkJs5vMTNKsWi2uoXn4O3cS', 2),
(10008, '../photos/image_2025-05-10_092622601.png', 'Janna', 'Baluyot', '099365841', 'baluyot@gmail.com', 'Monte Carlo', 5, '2025-05-23', '$2y$10$Z7ffCWUIDYI6Ch8x/l5O7u3QZncBGAc7FoirUvGawxjABAMIcg1Zu', 1),
(10009, '../photos/rodrigez jamma.webp', 'Jamma', 'Rodrigez', '099134567809', 'mtjuls41@gmail.com', 'Munting Tubig', 1, '2024-05-09', '$2y$10$oyVA6PS1L33BCE2j1ZH.1OSKzC3vWE2G4eJhw5pVSXC9w1CIkGfja', 1);

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
  `CreatedAt` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(2, 'Night Duty', '18:00:00', '04:00:00');

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
  MODIFY `EmployeeID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11112;

--
-- AUTO_INCREMENT for table `manager_info`
--
ALTER TABLE `manager_info`
  MODIFY `ManagerID` int(7) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7894562;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `RequestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

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
