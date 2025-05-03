<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Attttendance</title>
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
    $employeeID = 1; // Example EmployeeID
    $attendanceRecords = $procedures->getAllAttendanceLogs($employeeID);
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>
<header class="header">
    <img src="../photos/logo.png" alt="ChooksToJarell Logo" class="logo">
    <!--<h2>Welcome, <?php echo htmlspecialchars($managerDetails['FirstName'] . ' ' . $managerDetails['LastName']); ?></h2> -->
    <a href="?action=logout" class="btn btn-danger float-right">Logout</a> <!-- Add Logout Button -->
</header>

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
 <!-- Back Button -->
 <div class="mb-3">
        <a href="manager.php" class="btn btn-secondary">🔙 Back to Manager Hub</a>
    </div>

    <footer class="footer mt-5">
    <p>&copy; 2025 ChooksToJarell. All Rights Reserved.</p>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<script>
    </script>
</body>
</html>