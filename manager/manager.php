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
session_start(); // Start the session

if (!isset($_SESSION['manager_id'])) {
    // Redirect to login page if not logged in
    header("Location: manager_login.php");
    exit;
}
?>
<header class="header">
    <img src="../photos/logo.png" alt="ChooksToJarell Logo" class="logo">
    <a href="manager_logout.php" class="btn btn-danger float-right">Logout</a>
</header>

<div class="container mt-5">
    <h2 class="text-center">Welcome  <?php echo htmlspecialchars($_SESSION['manager_name']); ?> to ChooksToJarell Manager Hub !</h2>
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <p>What do you want to do today?</p>
            <a href="manage_attendance.php" class="btn btn-primary btn-lg btn-block mb-3">📋 Attendance Logs</a>
            <a href="manage_employees.php" class="btn btn-primary btn-lg btn-block mb-3">👥 Manage Employees</a>
            <a href="show_requests.php" class="btn btn-primary btn-lg btn-block mb-3">📩 Show Requests</a>
            <a href="manager_hub.php" class="btn btn-primary btn-lg btn-block mb-3">🏢 Manager Hub</a>
        </div>
    </div>
</div>

<footer class="footer mt-5">
    <p>&copy; 2025 ChooksToJarell. All Rights Reserved.</p>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@sweetalert2/11"></script>
</body>
</html>