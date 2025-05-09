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
    $employees = $procedures->getAllEmployees();

    // ✅ Fetch all attendance records for all employees
    $query = "SELECT * FROM attendance_log ORDER BY Date DESC";
    $result = $db->query($query);
    if (!$result) {
        throw new Exception("Query error: " . $db->error);
    }

    $attendanceRecords = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manager Attendance</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<header class="header p-3 bg-light">
    <img src="../photos/logo.png" alt="ChooksToJarell Logo" class="logo" height="60">
    <a href="?action=logout" class="btn btn-danger float-right">Logout</a>
</header>

<div class="container mt-4">
    <h3>Attendance Records (All Employees)</h3>

    <?php if (empty($attendanceRecords)): ?>
        <p>No attendance records found.</p>
    <?php else: ?>
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>EmployeeID</th>
                    <th>Date</th>
                    <th>CheckIn</th>
                    <th>CheckOut</th>
                    <th>Status</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendanceRecords as $record): ?>
                    <tr>
                        <td><?= htmlspecialchars($record['EmployeeID']) ?></td>
                        <td><?= htmlspecialchars($record['Date']) ?></td>
                        <td><?= htmlspecialchars($record['CheckIn']) ?></td>
                        <td><?= $record['CheckOut'] ? htmlspecialchars($record['CheckOut']) : 'Still In' ?></td>
                        <td><?= htmlspecialchars($record['Status']) ?></td>
                        <td><?= htmlspecialchars($record['Remarks']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="manager.php" class="btn btn-secondary mt-3">🔙 Back to Manager Hub</a>
</div>

<footer class="footer mt-5 text-center">
    <p>&copy; 2025 ChooksToJarell. All Rights Reserved.</p>
</footer>
</body>
</html>
