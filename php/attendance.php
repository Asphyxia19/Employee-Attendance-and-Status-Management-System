<?php 
require_once '../functions/db_connection.php';
date_default_timezone_set('Asia/Manila');

$swalScript = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = $_POST['employee_id'];
    $action = $_POST['action'];
    $current_date = date('Y-m-d');
    $current_time = date('H:i:s');

    // Check if employee exists
    $check_employee = $conn->prepare("SELECT EmployeeID FROM employee_info WHERE EmployeeID = ?");
    $check_employee->bind_param("i", $employee_id);
    $check_employee->execute();
    $check_result = $check_employee->get_result();

    if ($check_result->num_rows === 0) {
        $swalScript = "Swal.fire('Error', 'Employee ID does not exist.', 'error');";
    } else {
        $check_attendance = $conn->prepare("SELECT * FROM attendance_log WHERE EmployeeID = ? AND Date = ?");
        $check_attendance->bind_param("is", $employee_id, $current_date);
        $check_attendance->execute();
        $result = $check_attendance->get_result();

        if ($result->num_rows > 0) {
            $record = $result->fetch_assoc();

            if ($action === 'Time In') {
                $swalScript = "Swal.fire('Warning', 'You already timed in today.', 'warning');";
            } elseif ($action === 'Time Out') {
                if (!empty($record['CheckOut'])) {
                    $swalScript = "Swal.fire('Info', 'You already timed out today.', 'info');";
                } else {
                    $update = $conn->prepare("UPDATE attendance_log SET CheckOut = ? WHERE EmployeeID = ? AND Date = ?");
                    $update->bind_param("sis", $current_time, $employee_id, $current_date);
                    if ($update->execute()) {
                        $swalScript = "Swal.fire('Success', 'Time out recorded.', 'success').then(() => location.reload());";
                    } else {
                        $swalScript = "Swal.fire('Error', 'Failed to update time out.', 'error');";
                    }
                }
            }
        } else {
            if ($action === 'Time In') {
                $insert = $conn->prepare("INSERT INTO attendance_log (EmployeeID, Date, CheckIn, Status) VALUES (?, ?, ?, 'Present')");
                $insert->bind_param("iss", $employee_id, $current_date, $current_time);
                if ($insert->execute()) {
                    $swalScript = "Swal.fire('Success', 'Time in recorded.', 'success').then(() => location.reload());";
                } else {
                    $swalScript = "Swal.fire('Error', 'Failed to record time in.', 'error');";
                }
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
                <input type="text" class="form-control" name="employee_id" required>
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" name="action" value="Time In" class="btn btn-success w-45">Time In</button>
                <button type="submit" name="action" value="Time Out" class="btn btn-danger w-45">Time Out</button>
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
