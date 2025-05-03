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

// Add this logout logic at the top of the file
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_start();
    session_destroy();
    header("Location: manager_login.php"); // Redirect to login page
    exit;
}

// Check if the manager is logged in
//if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
 //   header("Location: manager_login.php"); // Redirect to login page if not logged in
 //   exit;
//}

// Retrieve manager details from the session
//$managerID = isset($_SESSION['manager_id']) ? $_SESSION['manager_id'] : null;
//$managerName = isset($_SESSION['manager_name']) ? $_SESSION['manager_name'] : null;

// If session variables are missing, redirect to login
//if (!$managerID || !$managerName) {
 //   header("Location: manager_login.php");
  //  exit;
//}

// Fetch manager details, employees, and attendance records
$database = new Database();
$db = $database->getConnection();
$procedures = new Procedures($db);

try {
    // Fetch manager details using getManagerByID
   // $managerDetails = $procedures->getManagerByID($managerID);

    // Fetch all employees using getAllEmployees
    $employees = $procedures->getAllEmployees();

    // Fetch all attendance logs for a specific employee (replace 1 with the desired EmployeeID)
    $employeeID = isset($_GET['Employee_id']) ? intval($_GET['Employee_id']) : 1; // Default to 1 if not provided
    $attendanceRecords = $procedures->getAllAttendanceLogs($employeeID);
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Handle form submissions for editing or deleting employees
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

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
        echo "<script>alert('Employee updated successfully!'); window.location.href = 'manage_employees.php';</script>";
    } elseif ($action === 'delete') {
        $employeeID = intval($_POST['employee_id']);
        $procedures->deleteEmployee($employeeID);
        echo "<script>alert('Employee deleted successfully!'); window.location.href = 'manage_employees.php';</script>";
    }
}
?>

<header class="header">
    <img src="../photos/logo.png" alt="ChooksToJarell Logo" class="logo">
    <!--<h2>Welcome, <?php echo htmlspecialchars($managerDetails['FirstName'] . ' ' . $managerDetails['LastName']); ?></h2> -->
    <a href="?action=logout" class="btn btn-danger float-right">Logout</a> <!-- Add Logout Button -->
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
            <?php foreach ($employees as $employee): ?>
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
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Employee Modal --> 
<div class="modal fade" id="employeeModal" tabindex="-1" role="dialog" aria-labelledby="employeeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="employeeForm" action="manage_edit_employee.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="employeeModalLabel">Add/Edit Employee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="action" value="update">
                    <input type="hidden" name="employee_id" id="employee_id">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                    </div>
                    <div class="form-group">
                        <label for="contact_number">Contact Number</label>
                        <input type="text" class="form-control" id="contact_number" name="contact_number" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea class="form-control" id="address" name="address" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="position">Position</label>
                        <input type="text" class="form-control" id="position" name="position" required>
                    </div>
                    <div class="form-group">
                        <label for="hire_date">Hire Date</label>
                        <input type="date" class="form-control" id="hire_date" name="hire_date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
 <!-- Back Button -->
 <div class="mb-3">
        <a href="manager.php" class="btn btn-secondary">🔙 Back to Manager Hub</a>
    </div>

<div class="container mt-5">

<script>
    // Clear the form for adding a new employee
    function clearForm() {
        document.getElementById('employeeForm').reset();
        document.getElementById('action').value = 'create';
        document.getElementById('employee_id').value = '';
    }

    // Populate the form for editing an employee
    function editEmployee(employee) {
        document.getElementById('action').value = 'update';
        document.getElementById('employee_id').value = employee.EmployeeID;
        document.getElementById('first_name').value = employee.FirstName;
        document.getElementById('last_name').value = employee.LastName;
        document.getElementById('contact_number').value = employee.ContactNumber;
        document.getElementById('email').value = employee.Email;
        document.getElementById('address').value = employee.Address;
        document.getElementById('position').value = employee.Position;
        document.getElementById('hire_date').value = employee.HireDate;
    }

    // Delete an employee
    function deleteEmployee(employeeID) {
        if (confirm('Are you sure you want to delete this employee?')) {
            fetch('crud.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'delete',
                    employee_id: employeeID
                })
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                location.reload();
            })
            .catch(error => console.error('Error:', error));
        }
    }

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
                // Perform the delete action
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
                        location.reload(); // Reload the page to reflect changes
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