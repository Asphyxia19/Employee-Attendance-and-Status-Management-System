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
            die("Failed to prepare statement: " . $this->conn->errorInfo()[2]);
        }

        foreach ($params as $index => $param) {
            $stmt->bindValue($index + 1, $param);
        }

        if (!$stmt->execute()) {
            die("Failed to execute procedure: " . $stmt->errorInfo()[2]);
        }

        $stmt->closeCursor();
    }

    public function getAllEmployees() {
        $result = $this->callProcedure('GetAllEmployees');
        return $result; // Already fetched as an associative array
    }

    // Get employee by ID
    public function getEmployeeByID($employeeID) {
        $result = $this->callProcedure('GetEmployeeByID', [$employeeID]);
        return $result[0] ?? null; // Return the first result or null if empty
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
        return $result; // Already fetched as an associative array
    }

    public function loginManagerByID($managerID, $password) {
        $result = $this->callProcedure('LoginManagerByID', [$managerID, $password]);
        return $result[0] ?? null; // Return the first result or null if empty
    }

    public function createEmployee($firstName, $lastName, $contactNumber, $email, $address, $position, $hireDate, $password) {
        $this->callProcedure('CreateEmployee', [$firstName, $lastName, $contactNumber, $email, $address, $position, $hireDate, $password]);
    }

    public function insertAttendanceLog($employeeID, $date, $checkIn, $checkOut, $status, $remarks) {
        $this->callProcedure('InsertAttendanceLog', [$employeeID, $date, $checkIn, $checkOut, $status, $remarks]);
    }

    public function getAllManagers() {
        return $this->callProcedure('GetAllManagers');
    }

    public function deleteManager($managerID) {
        $this->callProcedure('DeleteManager', [$managerID]);
    }

    public function createManager($firstName, $lastName, $contactNumber, $email, $password) {
        $this->callProcedure('CreateManager', [$firstName, $lastName, $contactNumber, $email, $password]);
    }
    
    public function getManagerByID($managerID) {
        $result = $this->callProcedure('GetManagerByID', [$managerID]);
        return $result[0] ?? null; // Return the first result or null if empty
    }

    public function updateManager($managerID, $firstName, $lastName, $contactNumber, $email, $password = null) {
        if ($password) {
            $this->callProcedure('UpdateManager', [$managerID, $firstName, $lastName, $contactNumber, $email, $password]);
        } else {
            $this->callProcedure('UpdateManager', [$managerID, $firstName, $lastName, $contactNumber, $email, null]);
        }
    }

    public function updateManagerWithID($originalManagerID, $newManagerID, $firstName, $lastName, $email) {
        $this->callProcedure('UpdateManagerWithID', [$originalManagerID, $newManagerID, $firstName, $lastName, $email]);
    }

    public function updateEmployeeWithoutPassword($employeeID, $firstName, $lastName, $contactNumber, $email, $address, $position, $hireDate) {
        $this->callProcedure('UpdateEmployeeWithoutPassword', [$employeeID, $firstName, $lastName, $contactNumber, $email, $address, $position, $hireDate]);
    }

    public function updateEmployeeWithPassword($employeeID, $firstName, $lastName, $contactNumber, $email, $address, $position, $hireDate, $password) {
        $this->callProcedure('UpdateEmployeeWithPassword', [$employeeID, $firstName, $lastName, $contactNumber, $email, $address, $position, $hireDate, $password]);
    }

}
?>