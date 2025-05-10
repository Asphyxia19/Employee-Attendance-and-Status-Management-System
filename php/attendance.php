<?php 
require_once '../functions/db_connection.php';
date_default_timezone_set('Asia/Manila');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$swalScript = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = intval($_POST['employee_id']);
    $password = $_POST['password'];
    $action = $_POST['action'];
    $current_date = date('Y-m-d');
    $current_time = date('H:i:s');

    // 1. CHECK IF EMPLOYEE EXISTS
    $stmt = $conn->prepare("CALL CheckEmployeeExists(:employee_id)");
    $stmt->bindValue(':employee_id', $employee_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        $swalScript = "Swal.fire('Error', 'Employee ID does not exist.', 'error');";
        $stmt->closeCursor();
    } else {
        $stmt->closeCursor();

        // 2. VERIFY PASSWORD
        $stmt = $conn->prepare("CALL GetEmployeePassword(:employee_id)");
        $stmt->bindValue(':employee_id', $employee_id, PDO::PARAM_INT);
        $stmt->execute();
        $employee = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if ($employee) {
            $storedPassword = $employee['Password'];

            // Verify the password
            if (password_verify($password, $storedPassword)) {
                // Password matches (hashed)
                // Proceed with attendance logic
            } elseif ($password === $storedPassword) {
                // Password matches (plain-text)
                // Rehash the plain-text password and update the database
                $newHashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $updateStmt = $conn->prepare("CALL UpdateEmployeePassword(:employee_id, :password)");
                $updateStmt->bindValue(':employee_id', $employee_id, PDO::PARAM_INT);
                $updateStmt->bindValue(':password', $newHashedPassword, PDO::PARAM_STR);
                $updateStmt->execute();
                $updateStmt->closeCursor();

                // Proceed with attendance logic
            } else {
                // Password does not match
                $swalScript = "Swal.fire('Error', 'Incorrect password.', 'error');";
            }
        } else {
            // Employee not found
            $swalScript = "Swal.fire('Error', 'Employee ID does not exist.', 'error');";
        }

        // 3. GET TODAY'S ATTENDANCE
        $stmt = $conn->prepare("CALL GetTodayAttendance(:employee_id, :current_date)");
        $stmt->bindValue(':employee_id', $employee_id, PDO::PARAM_INT);
        $stmt->bindValue(':current_date', $current_date, PDO::PARAM_STR);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if ($record) {
            if ($action === 'Time In') {
                $swalScript = "Swal.fire('Warning', 'Already timed in at {$record['CheckIn']}.', 'warning');";
            } elseif ($action === 'Time Out') {
                if (!empty($record['CheckOut'])) {
                    $swalScript = "Swal.fire('Info', 'Already timed out at {$record['CheckOut']}.', 'info');";
                } else {
                    $stmt = $conn->prepare("CALL UpdateTimeOut(:employee_id, :current_date, :current_time)");
                    $stmt->bindValue(':employee_id', $employee_id, PDO::PARAM_INT);
                    $stmt->bindValue(':current_date', $current_date, PDO::PARAM_STR);
                    $stmt->bindValue(':current_time', $current_time, PDO::PARAM_STR);
                    $success = $stmt->execute();
                    $swalScript = $success
                        ? "Swal.fire('Success', 'Time out recorded at $current_time.', 'success').then(() => location.reload());"
                        : "Swal.fire('Error', 'Failed to record time out.', 'error');";
                    $stmt->closeCursor();
                }
            }
        } else {
            if ($action === 'Time In') {
                $stmt = $conn->prepare("CALL InsertTimeIn(:employee_id, :current_date, :current_time)");
                $stmt->bindValue(':employee_id', $employee_id, PDO::PARAM_INT);
                $stmt->bindValue(':current_date', $current_date, PDO::PARAM_STR);
                $stmt->bindValue(':current_time', $current_time, PDO::PARAM_STR);
                $success = $stmt->execute();
                $swalScript = $success
                    ? "Swal.fire('Success', 'Time in recorded at $current_time.', 'success').then(() => location.reload());"
                    : "Swal.fire('Error', 'Failed to record time in.', 'error');";
                $stmt->closeCursor();
            } elseif ($action === 'Time Out') {
                $swalScript = "Swal.fire('Error', 'You must time in before timing out.', 'error');";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        #clock {
            font-size: 2rem;
            font-weight: bold;
            color: #dc3545;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<header class="header text-center py-4">
    <img src="../photos/logo.png" alt="ChooksToJarell Logo" class="logo mb-3" style="max-height: 100px;">
    <h2 class="text-white">Attendance Tracker</h2>
</header>

<main class="container d-flex justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="card p-4 shadow" style="max-width: 400px; width: 100%;">
        <div id="clock" class="text-center"></div> <!-- Clock Display -->
        <form method="POST">
    <div class="form-group">
        <label for="employee_id">Enter Employee ID</label>
        <input type="text" class="form-control" maxlength= "5" name="employee_id" required>
    </div>
    <div class="form-group">
        <label for="password">Enter Password</label>
        <input type="password" class="form-control" name="password" required>
    </div>
    <div class="d-flex justify-content-between">
        <button type="submit" name="action" value="Time In" class="btn btn-success w-45">TimeIn</button>
        <button type="submit" name="action" value="Time Out" class="btn btn-danger w-45">TimeOut</button>
    </div>
</form>
        <!-- Button to navigate to index.php -->
        <button onclick="window.location.href='/Employee-Attendance-and-Status-Management-System/php/index.php'" class="btn btn-primary w-100 mt-3"> return </button>
    </div>
</main>

<footer class="footer text-center py-3 bg-dark text-white">
    <p>&copy; 2025 ChooksToJarell. All Rights Reserved.</p>
</footer>

<?php if (!empty($swalScript)): ?>
<script>
    <?php echo $swalScript; ?>
</script>
<?php endif; ?>

<script>
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', { hour12: false });
        document.getElementById('clock').textContent = timeString;
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>

</body>
</html>