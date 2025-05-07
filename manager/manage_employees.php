<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Employee</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<?php
session_start();
require_once '../functions/db_connection.php';
require_once '../functions/procedures.php';

// Logout logic
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: manager_login.php");
    exit;
}

// Check if the manager is logged in
if (!isset($_SESSION['manager_id'])) {
    header("Location: manager_login.php");
    exit;
}

// Initialize database and procedures
$database = new Database();
$db = $database->getConnection();
$procedures = new Procedures($db);

try {
    // Fetch all employees
    $employees = $procedures->getAllEmployees();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Handle form submissions for editing or deleting employees
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    try {
        if ($action === 'update') {
            $employeeID = intval($_POST['employee_id']);
            $firstName = htmlspecialchars(trim($_POST['first_name']));
            $lastName = htmlspecialchars(trim($_POST['last_name']));
            $contactNumber = htmlspecialchars(trim($_POST['contact_number']));
            $email = htmlspecialchars(trim($_POST['email']));
            $address = htmlspecialchars(trim($_POST['address']));
            $position = htmlspecialchars(trim($_POST['position']));
            $hireDate = htmlspecialchars(trim($_POST['hire_date']));

            $procedures->updateEmployee($employeeID, $firstName, $lastName, $contactNumber, $email, $address, $position, $hireDate);
            echo "<script>
                Swal.fire({
                    title: 'Success!',
                    text: 'Employee updated successfully!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'manage_employees.php';
                });
            </script>";
        } elseif ($action === 'delete') {
            $employeeID = intval($_POST['employee_id']);
            $procedures->deleteEmployee($employeeID);
            echo "<script>
                Swal.fire({
                    title: 'Deleted!',
                    text: 'Employee deleted successfully!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'manage_employees.php';
                });
            </script>";
        }
    } catch (Exception $e) {
        echo "<script>
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred: " . $e->getMessage() . "',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>";
    }
}
?>

<header class="header">
    <img src="../photos/logo.png" alt="ChooksToJarell Logo" class="logo">
    <a href="?action=logout" class="btn btn-danger float-right">Logout</a>
</header>

<div class="container mt-5">
    <h2 class="text-center">Manager Dashboard</h2>

    <!-- Employees Section -->
    <h3>Employees</h3>
    <button class="btn btn-primary mb-3" onclick="window.location.href='manage_add_employee.php'">Add Employee</button>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Employee ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Position</th>
                <th>Contact</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            
                <tr>
                    <td><?php echo htmlspecialchars($employee['EmployeeID']); ?></td>
                    <td><?php echo htmlspecialchars($employee['FirstName']); ?></td>
                    <td><?php echo htmlspecialchars($employee['LastName']); ?></td>
                    <td><?php echo htmlspecialchars($employee['Position']); ?></td>
                    <td><?php echo htmlspecialchars($employee['ContactNumber']); ?></td>
                    <td><?php echo htmlspecialchars($employee['Email']); ?></td>
                    <td>
                        <a href="manage_edit_employees.php?employee_id=<?php echo $employee['EmployeeID']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $employee['EmployeeID']; ?>)">Delete</button>
                    </td>
                </tr>
    
        </tbody>
    </table>
</div>

<script>
    function confirmDelete(employeeID) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('manage_employees.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'delete',
                        employee_id: employeeID
                    })
                })
                .then(response => response.text())
                .then(data => {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'The employee has been deleted.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while deleting the employee.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    console.error('Error:', error);
                });
            }
        });
    }
</script>

<footer class="footer mt-5">
    <p>&copy; 2025 ChooksToJarell. All Rights Reserved.</p>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>