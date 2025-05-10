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

          //  try {
          //      $procedures->createEmployee($firstName, $lastName, $contactNumber, $email, $address, $position, $hireDate);
          //      echo "Employee created successfully!";
          //  } catch (Exception $e) {
          //      echo "Error: " . $e->getMessage();
           // }
           // break;

      //  case 'read':
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

        case 'deleteManager':
            $managerID = intval($_POST['manager_id']);
            try {
                $procedures->deleteManager($managerID);
                echo "Manager deleted successfully!";
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
            break;
    }
    
}
?>

<script>
// JavaScript code for handling the deleteManager action
function deleteManager(managerID) {
    fetch('crud.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'deleteManager',
            manager_id: managerID
        })
    })
    .then(response => response.text())
    .then(data => {
        console.log('Response from server:', data); // Debugging: Log the server response
        Swal.fire({
            title: 'Deleted!',
            text: 'The manager has been deleted.',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(() => {
            location.reload(); // Reload the page to reflect changes
        });
    })
    .catch(error => {
        Swal.fire({
            title: 'Error!',
            text: 'An error occurred while deleting the manager.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        console.error('Error:', error);
    });
}
</script>