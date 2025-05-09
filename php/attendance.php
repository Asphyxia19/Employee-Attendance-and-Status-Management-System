<?php 
require_once '../functions/db_connection.php';
date_default_timezone_set('Asia/Manila');

$swalScript = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = $_POST['employee_id'];
    $action = $_POST['action'];
    $current_date = date('Y-m-d');
    $current_time = date('H:i:s');

    try {
        // Check if employee exists
        $check_employee = $conn->prepare("SELECT EmployeeID FROM employee_info WHERE EmployeeID = :employee_id");
        $check_employee->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
        $check_employee->execute();

        if ($check_employee->rowCount() === 0) {
            $swalScript = "Swal.fire('Error', 'Employee ID does not exist.', 'error');";
        } else {
            // Check attendance log for the employee
            $check_attendance = $conn->prepare("SELECT * FROM attendance_log WHERE EmployeeID = :employee_id AND Date = :current_date");
            $check_attendance->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
            $check_attendance->bindParam(':current_date', $current_date);
            $check_attendance->execute();

            if ($check_attendance->rowCount() > 0) {
                $record = $check_attendance->fetch(PDO::FETCH_ASSOC);

                if ($action === 'Time In') {
                    $swalScript = "Swal.fire('Warning', 'You already timed in today.', 'warning');";
                } elseif ($action === 'Time Out') {
                    if (!empty($record['CheckOut'])) {
                        $swalScript = "Swal.fire('Info', 'You already timed out today.', 'info');";
                    } else {
                        // Update CheckOut time
                        $update = $conn->prepare("UPDATE attendance_log SET CheckOut = :current_time WHERE EmployeeID = :employee_id AND Date = :current_date");
                        $update->bindParam(':current_time', $current_time);
                        $update->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
                        $update->bindParam(':current_date', $current_date);
                        if ($update->execute()) {
                            $swalScript = "Swal.fire('Success', 'Time out recorded.', 'success').then(() => location.reload());";
                        } else {
                            $swalScript = "Swal.fire('Error', 'Failed to update time out.', 'error');";
                        }
                    }
                }
            } else {
                if ($action === 'Time In') {
                    // Insert new attendance record
                    $insert = $conn->prepare("INSERT INTO attendance_log (EmployeeID, Date, CheckIn, Status) VALUES (:employee_id, :current_date, :current_time, 'Present')");
                    $insert->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
                    $insert->bindParam(':current_date', $current_date);
                    $insert->bindParam(':current_time', $current_time);
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
    } catch (PDOException $e) {
        $swalScript = "Swal.fire('Error', 'Database error: " . $e->getMessage() . "', 'error');";
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
        <button onclick="window.location.href='/Employee-Attendance-and-Status-Management-System/php/index.php'" class="btn btn-primary w-100 mt-3">Return</button>
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