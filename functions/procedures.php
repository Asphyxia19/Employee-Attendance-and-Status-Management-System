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
            die("Failed to prepare statement.");
        }

        foreach ($params as $index => $param) {
            $stmt->bindValue($index + 1, $param);
        }

        if (!$stmt->execute()) {
            die("Failed to execute procedure.");
        }

        return $stmt;
    }

    public function getAllEmployees() {
    $stmt = $this->callProcedure('GetAllEmployees');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function getEmployeeByID($employeeID) {
    $stmt = $this->callProcedure('GetEmployeeByID', [$employeeID]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

    public function updateEmployee($employeeID, $firstName, $lastName, $contactNumber, $email, $address, $role_id, $shift_id, $hireDate) {
        $this->callProcedure('UpdateEmployee', [$employeeID, $firstName, $lastName, $contactNumber, $email, $address, $role_id, $shift_id, $hireDate]);
    }

    public function deleteEmployee($employeeID) {
        $this->callProcedure('DeleteEmployee', [$employeeID]);
    }

    public function getAttendanceLogs() {
        $stmt = $this->callProcedure('GetAttendanceLogs', []);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function loginManagerByID($managerID, $password) {
        $stmt = $this->callProcedure('LoginManagerByID', [$managerID, $password]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createEmployee($firstName, $lastName, $contactNumber, $email, $address, $role_id, $shift_id, $hireDate, $password) {
        $this->callProcedure('CreateEmployee', [$firstName, $lastName, $contactNumber, $email, $address, $role_id, $shift_id, $hireDate, $password]);
    }

    public function insertAttendanceLog($employeeID, $date, $checkIn, $checkOut, $status, $remarks) {
        $this->callProcedure('InsertAttendanceLog', [$employeeID, $date, $checkIn, $checkOut, $status, $remarks]);
    }

    public function getAllManagers() {
        $stmt = $this->callProcedure('GetAllManagers', []);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteManager($managerID) {
        $this->callProcedure('DeleteManager', [$managerID]);
    }

    public function createManager($managerID, $firstName, $lastName, $contactNumber, $email, $password, $profilePicture) {
        $this->callProcedure('CreateManager', [
            $managerID, $firstName, $lastName, $contactNumber, $email, $password, $profilePicture
        ]);
    }

    public function getManagerByID($managerID) {
        $stmt = $this->callProcedure('GetManagerByID', [$managerID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateManager($managerID, $firstName, $lastName, $email) {
        $this->callProcedure('UpdateManager', [$managerID, $firstName, $lastName, $email]);
    }

    public function getEmployeesWithoutAttendance($date) {
        $stmt = $this->callProcedure('GetEmployeesWithoutAttendance', [$date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLateEmployees($date) {
        $stmt = $this->callProcedure('GetLateEmployees', [$date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEmployeeAttendancePercentage($employeeID) {
        $stmt = $this->callProcedure('GetEmployeeAttendancePercentage', [$employeeID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateManagerContact($managerID, $contactNumber, $email) {
        $this->callProcedure('UpdateManagerContact', [$managerID, $contactNumber, $email]);
    }

    public function getAttendanceSummary($startDate, $endDate) {
        $stmt = $this->callProcedure('GetAttendanceSummary', [$startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEmployeeByEmail($email) {
        $stmt = $this->callProcedure('GetEmployeeByEmail', [$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getManagerByEmail($email) {
        $stmt = $this->callProcedure('GetManagerByEmail', [$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateAttendanceLog($employeeID, $date, $checkIn, $checkOut, $status, $remarks) {
        $this->callProcedure('UpdateAttendanceLog', [$employeeID, $date, $checkIn, $checkOut, $status, $remarks]);
    }

    public function deleteAttendanceLog($employeeID, $date) {
        $this->callProcedure('DeleteAttendanceLog', [$employeeID, $date]);
    }

    public function getManagerCount() {
        $stmt = $this->callProcedure('GetManagerCount');
        return $stmt->fetchColumn();
    }

    public function getEmployeeCount() {
        $stmt = $this->callProcedure('GetEmployeeCount');
        return $stmt->fetchColumn();
    }

    public function resetManagerPassword($managerID, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $this->callProcedure('ResetManagerPassword', [$managerID, $hashedPassword]);
    }

    public function markEmployeeAbsent($employeeID, $date, $remarks) {
        $this->callProcedure('MarkEmployeeAbsent', [$employeeID, $date, $remarks]);
    }

    public function getAttendanceLogsByEmployeeAndDate($employeeID, $date) {
        $stmt = $this->callProcedure('GetAttendanceLogsByEmployeeAndDate', [$employeeID, $date]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAttendanceLogsByDate($date) {
        $stmt = $this->callProcedure('GetAttendanceLogsByDate', [$date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateEmployeeWithPassword($employeeID, $profilePicture, $firstName, $lastName, $contactNumber, $email, $address, $role_id, $shift_id, $hireDate, $password) {
        $this->callProcedure('UpdateEmployeeWithPassword', [$employeeID, $profilePicture, $firstName, $lastName, $contactNumber, $email, $address, $role_id, $shift_id, $hireDate, $password]);
    }

    public function updateEmployeeWithoutPassword($employeeID, $profilePicture, $firstName, $lastName, $contactNumber, $email, $address, $role_id, $shift_id, $hireDate) {
        $this->callProcedure('UpdateEmployeeWithoutPassword', [$employeeID, $profilePicture, $firstName, $lastName, $contactNumber, $email, $address, $role_id, $shift_id, $hireDate]);
    }

    public function updateManagerWithPassword($originalManagerID, $managerID, $profilePicture, $firstName, $lastName, $contactNumber, $email, $password) {
        $this->callProcedure('UpdateManagerWithPassword', [$originalManagerID, $managerID, $profilePicture, $firstName, $lastName, $contactNumber, $email, $password]);
    }
    
    public function updateManagerWithoutPassword($originalManagerID, $managerID, $profilePicture, $firstName, $lastName, $contactNumber, $email) {
        $this->callProcedure('UpdateManagerWithoutPassword', [$originalManagerID, $managerID, $profilePicture, $firstName, $lastName, $contactNumber, $email]);
    }

    public function createEmployeeWithProfilePicture($employeeID, $profilePicture, $firstName, $lastName, $contactNumber, $email, $address, $role_id, $shift_id, $hireDate, $password) {
        $this->callProcedure('CreateEmployeeWithProfilePicture', [
            $employeeID, $profilePicture, $firstName, $lastName, $contactNumber, $email, $address, $role_id, $shift_id, $hireDate, $password
        ]);
    }

    public function createManagerWithProfilePicture($profilePicture, $firstName, $lastName, $email, $password) {
        $this->callProcedure('CreateManagerWithProfilePicture', [$profilePicture, $firstName, $lastName, $email, $password]);
    }

    public function searchEmployees($searchTerm) {
        $stmt = $this->callProcedure('SearchEmployees', [$searchTerm]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchManagers($searchTerm) {
        $stmt = $this->callProcedure('SearchManagers', [$searchTerm]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertRequest($employeeId, $requestType, $details) {
    try {
        $stmt = $this->conn->prepare("CALL InsertRequest(:employee_id, :request_type, :details)");
        $stmt->bindParam(':employee_id', $employeeId, PDO::PARAM_INT);
        $stmt->bindParam(':request_type', $requestType, PDO::PARAM_STR);
        $stmt->bindParam(':details', $details, PDO::PARAM_STR);
        $stmt->execute();
        return true; // Return true if the procedure executes successfully
    } catch (PDOException $e) {
        throw new Exception("Error calling InsertRequest procedure: " . $e->getMessage());
    }

    
    }
    public function getAllShifts() {
        $stmt = $this->callProcedure('GetAllShifts');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAllPositions() {
        $stmt = $this->callProcedure('GetAllPositions');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
?>