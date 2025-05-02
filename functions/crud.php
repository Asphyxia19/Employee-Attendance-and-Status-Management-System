<?php
require_once '../functions/db_connection.php';
require_once '../functions/procedures.php';

$database = new Database();
$db = $database->getConnection();
$procedures = new Procedures($db);

if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {
    // Determine the action (create, read, update, delete)
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

    switch ($action) {
        case 'create':
            // Create a new employee
            $firstName = htmlspecialchars(trim($_POST['first_name']));
            $lastName = htmlspecialchars(trim($_POST['last_name']));
            $contactNumber = htmlspecialchars(trim($_POST['contact_number']));
            $email = htmlspecialchars(trim($_POST['email']));
            $address = htmlspecialchars(trim($_POST['address']));
            $position = htmlspecialchars(trim($_POST['position']));
            $hireDate = htmlspecialchars(trim($_POST['hire_date']));

            try {
                $procedures->createEmployee($firstName, $lastName, $contactNumber, $email, $address, $position, $hireDate);
                echo "Employee created successfully!";
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
            break;

        case 'read':
            // Read all employees
            try {
                $employees = $procedures->getAllEmployees();
                echo json_encode($employees); // Return employees as JSON
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
            break;

        case 'readById':
            // Read a specific employee by ID
            $employeeID = intval($_GET['employee_id']);
            try {
                $employee = $procedures->getEmployeeByID($employeeID);
                echo json_encode($employee); // Return employee as JSON
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
            break;

        case 'update':
            // Update an employee
            $employeeID = intval($_POST['employee_id']);
            $firstName = htmlspecialchars(trim($_POST['first_name']));
            $lastName = htmlspecialchars(trim($_POST['last_name']));
            $contactNumber = htmlspecialchars(trim($_POST['contact_number']));
            $email = htmlspecialchars(trim($_POST['email']));
            $address = htmlspecialchars(trim($_POST['address']));
            $position = htmlspecialchars(trim($_POST['position']));
            $hireDate = htmlspecialchars(trim($_POST['hire_date']));

            try {
                $procedures->updateEmployee($employeeID, $firstName, $lastName, $contactNumber, $email, $address, $position, $hireDate);
                echo "Employee updated successfully!";
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
            break;

        case 'delete':
            // Delete an employee
            $employeeID = intval($_POST['employee_id']);
            try {
                $procedures->deleteEmployee($employeeID);
                echo "Employee deleted successfully!";
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
            break;

        default:
            echo "Invalid action specified.";
            break;
    }
}
?>