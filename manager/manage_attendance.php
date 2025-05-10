<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<?php
session_start();
require_once '../functions/db_connection.php';
require_once '../functions/procedures.php';

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: manager_login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$procedures = new Procedures($db);

try {
    // Fetch all employees
    $employees = $procedures->getAllEmployees();

    // Fetch all attendance records for all employees
    $attendanceRecords = $procedures->getAttendanceLogs();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>


<header class="header">
    <img src="../photos/logo.png" alt="ChooksToJarell Logo" class="logo">
    <a href="manager_logout.php" class="btn btn-danger float-right">Logout</a>
</header>

<div class="container mt-4">
    <h3>Attendance Records (All Employees)</h3>

    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Profile Picture</th>
                <th>EmployeeID</th>
                <th>Date</th>
                <th>CheckIn</th>
                <th>CheckOut</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($attendanceRecords)): ?>
                <?php foreach ($attendanceRecords as $record): ?>
                    <tr>
                        <td class="text-center align-middle">
                            <img src="<?php echo !empty($record['ProfilePicture']) ? htmlspecialchars($record['ProfilePicture']) : '../photos/default-profile.png'; ?>" 
                                 alt="Profile Picture" 
                                 class="img-thumbnail" 
                                 style="width: 50px; height: 50px; object-fit: cover;">
                        </td>
                        <td><?= htmlspecialchars($record['EmployeeID']) ?></td>
                        <td><?= htmlspecialchars($record['Date']) ?></td>
                        <td><?= htmlspecialchars($record['CheckIn']) ?></td>
                        <td><?= $record['CheckOut'] ? htmlspecialchars($record['CheckOut']) : 'Still In' ?></td>
                        <td><?= htmlspecialchars($record['Status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No attendance records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    

    <a href="manager.php" class="btn btn-secondary mt-3">🔙 Back to Manager Hub</a>
</div>

<footer class="footer mt-5 text-center">
    <p>&copy; 2025 ChooksToJarell. All Rights Reserved.</p>
</footer>
</body>
</html>