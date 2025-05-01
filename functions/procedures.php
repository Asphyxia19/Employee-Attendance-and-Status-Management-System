<?php
require_once 'db_connection.php';

class Procedures {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    // Check if ManagerID exists
    public function managerIDExists($managerID) {
        $query = "SELECT ManagerID FROM manager_info WHERE ManagerID = ?";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            die("Failed to prepare statement: " . $this->conn->error);
        }

        $stmt->bind_param("s", $managerID);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }

    // Create a new manager
    public function createManager($managerID, $firstName, $lastName, $password) {
        $query = "INSERT INTO manager_info (ManagerID, FirstName, LastName, Password) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            die("Failed to prepare statement: " . $this->conn->error);
        }

        $stmt->bind_param("ssss", $managerID, $firstName, $lastName, $password);
        return $stmt->execute();
    }

    // Login method for managers
    public function loginManager($managerID, $password) {
        $query = "SELECT ManagerID, FirstName, LastName, Password FROM manager_info WHERE ManagerID = ?";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            die("Failed to prepare statement: " . $this->conn->error);
        }

        $stmt->bind_param("s", $managerID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $manager = $result->fetch_assoc();

            // Verify the password
            if (password_verify($password, $manager['Password'])) {
                return $manager; // Return manager details if login is successful
            }
        }

        return false; // Return false if login fails
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