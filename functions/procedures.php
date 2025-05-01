<?php
require_once '../functions/db_connection.php';

class Procedures {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
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

    // Fetch all employees
    public function fetchAllEmployees() {
        $result = $this->callProcedure('GetAllEmployees');
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Fetch attendance records
    public function fetchAttendanceRecords() {
        $result = $this->callProcedure('GetAttendanceRecords');
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Add a new employee
    public function addEmployee($firstName, $lastName, $contactNumber, $email, $address, $position, $hireDate) {
        $this->callProcedure('CreateEmployee', [$firstName, $lastName, $contactNumber, $email, $address, $position, $hireDate]);
    }

    // Add a new shift
    public function addShift($employeeID, $date, $duty, $timeIn, $timeOut) {
        $this->callProcedure('CreateEmployeeShift', [$employeeID, $date, $duty, $timeIn, $timeOut]);
    }

    // Fetch manager details
    public function fetchManagerDetails($managerID) {
        $result = $this->callProcedure('GetManagerByID', [$managerID]);
        return $result->fetch_assoc();
    }
}
?>