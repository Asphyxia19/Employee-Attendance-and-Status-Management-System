<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<?php
session_start();
require_once '../functions/db_connection.php';
require_once '../functions/procedures.php';

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
    $employeeID = 1; // Example EmployeeID
    $attendanceRecords = $procedures->getAllAttendanceLogs($employeeID);
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<header class="header">
    <img src="../photos/logo.png" alt="ChooksToJarell Logo" class="logo">
    <!--<h2>Welcome, <?php echo htmlspecialchars($managerDetails['FirstName'] . ' ' . $managerDetails['LastName']); ?></h2> -->
</header>

<div class="container mt-5">
    <h2 class="text-center">Manager Dashboard</h2>

    <!-- Employees Section -->
    <h3>Employees</h3>
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#employeeModal" onclick="clearForm()">Add Employee</button>
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
                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#employeeModal" onclick="editEmployee(<?php echo htmlspecialchars(json_encode($employee)); ?>)">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteEmployee(<?php echo $employee['EmployeeID']; ?>)">Delete</button>
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
            <form id="employeeForm" action="crud.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="employeeModalLabel">Add/Edit Employee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="action" value="create">
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

<!-- Attendance Records Section -->
<h3>Attendance Records</h3>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Record ID</th>
            <th>Employee ID</th>
            <th>Date</th>
            <th>Check-In</th>
            <th>Check-Out</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($attendanceRecords as $record): ?>
            <tr>
                <td><?php echo htmlspecialchars($record['RecordID']); ?></td>
                <td><?php echo htmlspecialchars($record['EmployeeID']); ?></td>
                <td><?php echo htmlspecialchars($record['Date']); ?></td>
                <td><?php echo htmlspecialchars($record['CheckIn']); ?></td>
                <td><?php echo htmlspecialchars($record['CheckOut']); ?></td>
                <td><?php echo htmlspecialchars($record['Status']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<footer class="footer mt-5">
    <p>&copy; 2025 ChooksToJarell. All Rights Reserved.</p>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@sweetalert2/11"></script>

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
</script>
</body>
</html>