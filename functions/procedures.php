<?php
require_once 'db_connection.php';

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

        if (!$stmt->execute()) {
            die("Failed to execute procedure: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }
    public function getAllEmployees() {
        $result = $this->callProcedure('GetAllEmployees');
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get employee by ID
    public function getEmployeeByID($employeeID) {
        $result = $this->callProcedure('GetEmployeeByID', [$employeeID]);
        return $result->fetch_assoc();
    }
    
    // Update employee
    public function updateEmployee($employeeID, $firstName, $lastName, $contactNumber, $email, $address, $position, $hireDate) {
        $this->callProcedure('UpdateEmployee', [$employeeID, $firstName, $lastName, $contactNumber, $email, $address, $position, $hireDate]);
    }
    
    // Delete employee
    public function deleteEmployee($employeeID) {
        $this->callProcedure('DeleteEmployee', [$employeeID]);
    }
    
    // Get all attendance logs
    public function getAllAttendanceLogs($employeeID) {
    $result = $this->callProcedure('GetAllAttendanceLogs', [$employeeID]);
    return $result->fetch_all(MYSQLI_ASSOC);
}

    public function loginManagerByID($managerID, $password) {
        $result = $this->callProcedure('LoginManagerByID', [$managerID, $password]);
        return $result->fetch_assoc();
    }

    public function createEmployee($firstName, $lastName, $contactNumber, $email, $address, $position, $hireDate, $password) {
        $this->callProcedure('CreateEmployee', [$firstName, $lastName, $contactNumber, $email, $address, $position, $hireDate, $password]);
    }
}
?>