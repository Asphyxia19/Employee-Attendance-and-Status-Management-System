<?php
require_once 'db_connection.php';

class Procedures {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }


    public function updateEmployee($employeeID, $firstName, $lastName, $contactNumber, $email, $address, $position, $hireDate) {
        $this->callProcedure('UpdateEmployee', [$employeeID, $firstName, $lastName, $contactNumber, $email, $address, $position, $hireDate]);
    }
    // Call a stored procedure
    private function callProcedure($procedureName, $params = []) {
        $placeholders = implode(',', array_fill(0, count($params), '?'));
        $query = "CALL $procedureName($placeholders)";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            die("Failed to prepare statement: " . $this->conn->error);
        }

        if (!empty($params)) {
            $types = str_repeat('s', count($params)); // Assuming all parameters are strings
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    // Create Attendance Log
    public function createAttendanceLog($shiftID, $status, $lateMinutes, $notes, $verifiedBy) {
        $this->callProcedure('CreateAttendanceLog', [$shiftID, $status, $lateMinutes, $notes, $verifiedBy]);
    }

    // Create Employee
    public function createEmployee($firstName, $lastName, $contactNumber, $email, $address, $position, $hireDate) {
        $this->callProcedure('CreateEmployee', [$firstName, $lastName, $contactNumber, $email, $address, $position, $hireDate]);
    }

    // Create Employee Shift
    public function createEmployeeShift($employeeID, $date, $duty, $timeIn, $timeOut) {
        $this->callProcedure('CreateEmployeeShift', [$employeeID, $date, $duty, $timeIn, $timeOut]);
    }

    // Create Fixed Schedule
    public function createFixedSchedule($employeeID, $dutyID, $dayOfWeek, $startTime, $endTime) {
        $this->callProcedure('CreateFixedSchedule', [$employeeID, $dutyID, $dayOfWeek, $startTime, $endTime]);
    }

    // Create Manager
    public function createManager($firstName, $lastName, $contactNumber, $email) {
        $this->callProcedure('CreateManager', [$firstName, $lastName, $contactNumber, $email]);
    }

    // Create Overtime Log
    public function createOvertimeLog($employeeID, $date, $startTime, $endTime, $hoursRendered, $reason, $approvedBy) {
        $this->callProcedure('CreateOvertimeLog', [$employeeID, $date, $startTime, $endTime, $hoursRendered, $reason, $approvedBy]);
    }

    // Create Payroll
    public function createPayroll($employeeID, $periodStart, $periodEnd, $totalHoursWorked, $otHours, $hourlyRate, $otRate, $grossPay, $deductions, $netPay, $status) {
        $this->callProcedure('CreatePayroll', [$employeeID, $periodStart, $periodEnd, $totalHoursWorked, $otHours, $hourlyRate, $otRate, $grossPay, $deductions, $netPay, $status]);
    }

    // Create Request
    public function createRequest($employeeID, $requestType, $reason, $dateRequested, $status, $managerID) {
        $this->callProcedure('CreateRequest', [$employeeID, $requestType, $reason, $dateRequested, $status, $managerID]);
    }

    // Create Shift Duty
    public function createShiftDuty($dutyName, $description) {
        $this->callProcedure('CreateShiftDuty', [$dutyName, $description]);
    }

    // Delete Attendance Log
    public function deleteAttendanceLog($logID) {
        $this->callProcedure('DeleteAttendanceLog', [$logID]);
    }

    // Delete Employee
    public function deleteEmployee($employeeID) {
        $this->callProcedure('DeleteEmployee', [$employeeID]);
    }

    // Delete Employee Shift
    public function deleteEmployeeShift($shiftID) {
        $this->callProcedure('DeleteEmployeeShift', [$shiftID]);
    }

    // Delete Fixed Schedule
    public function deleteFixedSchedule($scheduleID) {
        $this->callProcedure('DeleteFixedSchedule', [$scheduleID]);
    }

    // Delete Manager
    public function deleteManager($managerID) {
        $this->callProcedure('DeleteManager', [$managerID]);
    }

    // Delete Overtime Log
    public function deleteOvertimeLog($otID) {
        $this->callProcedure('DeleteOvertimeLog', [$otID]);
    }

    // Delete Payroll
    public function deletePayroll($payrollID) {
        $this->callProcedure('DeletePayroll', [$payrollID]);
    }

    // Delete Request
    public function deleteRequest($requestID) {
        $this->callProcedure('DeleteRequest', [$requestID]);
    }

    // Delete Shift Duty
    public function deleteShiftDuty($dutyID) {
        $this->callProcedure('DeleteShiftDuty', [$dutyID]);
    }

    // Get Attendance Log By ID
    public function getAttendanceLogByID($logID) {
        $result = $this->callProcedure('GetAttendanceLogByID', [$logID]);
        return $result->fetch_assoc();
    }

    // Get Employee By ID
    public function getEmployeeByID($employeeID) {
        $result = $this->callProcedure('GetEmployeeByID', [$employeeID]);
        return $result->fetch_assoc();
    }

    // Get Employee Shift By ID
    public function getEmployeeShiftByID($shiftID) {
        $result = $this->callProcedure('GetEmployeeShiftByID', [$shiftID]);
        return $result->fetch_assoc();
    }

    // Get Fixed Schedule By ID
    public function getFixedScheduleByID($scheduleID) {
        $result = $this->callProcedure('GetFixedScheduleByID', [$scheduleID]);
        return $result->fetch_assoc();
    }

    // Get Manager By ID
    public function getManagerByID($managerID) {
        $result = $this->callProcedure('GetManagerByID', [$managerID]);
        return $result->fetch_assoc();
    }

    // Get Overtime Log By ID
    public function getOvertimeLogByID($otID) {
        $result = $this->callProcedure('GetOvertimeLogByID', [$otID]);
        return $result->fetch_assoc();
    }

    // Get Payroll By ID
    public function getPayrollByID($payrollID) {
        $result = $this->callProcedure('GetPayrollByID', [$payrollID]);
        return $result->fetch_assoc();
    }

    // Get Request By ID
    public function getRequestByID($requestID) {
        $result = $this->callProcedure('GetRequestByID', [$requestID]);
        return $result->fetch_assoc();
    }

    // Get Shift Duty By ID
    public function getShiftDutyByID($dutyID) {
        $result = $this->callProcedure('GetShiftDutyByID', [$dutyID]);
        return $result->fetch_assoc();
    }
        // Get all employees
public function getAllEmployees() {
    $result = $this->callProcedure('GetAllEmployees');
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get all attendance logs
public function getAllAttendanceLogs($employeeID) {
    $result = $this->callProcedure('GetAllAttendanceLogs', [$employeeID]);
    return $result->fetch_all(MYSQLI_ASSOC);
}
public function loginManagerByFirstName($firstName, $password) {
    $query = "SELECT ManagerID, FirstName, LastName, Password FROM manager_info WHERE FirstName = ?";
    $stmt = $this->conn->prepare($query);

    if (!$stmt) {
        die("Failed to prepare statement: " . $this->conn->error);
    }

    $stmt->bind_param("s", $firstName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $manager = $result->fetch_assoc();

        // Compare the plain text password directly
        if ($password === $manager['Password']) {
            return $manager; // Return manager details if login is successful
        }
    }
}
function validateAttendance($conn, $employee_id, $attendance_date, $check_in, $check_out, $status) {
    // Check if the employee exists
    $query = "SELECT EmployeeID FROM employee_info WHERE EmployeeID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return "Employee ID does not exist.";
    }

    // Check if attendance date is valid
    if (strtotime($attendance_date) === false) {
        return "Invalid attendance date.";
    }

    // Check if check-in time is valid
    if (strtotime($check_in) === false) {
        return "Invalid check-in time.";
    }

    // Check if check-out time is valid (if provided)
    if (!empty($check_out) && strtotime($check_out) === false) {
        return "Invalid check-out time.";
    }

    // Check if check-out time is after check-in time
    if (!empty($check_out) && strtotime($check_out) <= strtotime($check_in)) {
        return "Check-out time must be after check-in time.";
    }

    // Check if status is valid
    $valid_statuses = ["Present", "Absent"];
    if (!in_array($status, $valid_statuses)) {
        return "Invalid status. Allowed values are 'Present' or 'Absent'.";
    }

    // If all validations pass
    return true;
}
}
?>
