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
<header class="header">
<img src="../photos/logo.png" alt="ChooksToJarell Logo" class="logo">
</header>
<?php 
require_once '../functions/db_connection.php';
require_once '../functions/procedures.php';

// Define the validateAttendance function
function validateAttendance($conn, $employee_id, $attendance_date, $check_in, $check_out, $status) {
    // Check if the employee exists
    $query = "SELECT EmployeeID FROM employee_info WHERE EmployeeID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return "Employee ID does not exist.";
    }

    // Check if attendance date is valid
    if (strtotime($attendance_date) === false) {
        return "Invalid attendance date.";
    }

    // Check if check-in time is valid
    if (strtotime($check_in) === false) {
        return "Invalid check-in time.";
    }

    // Check if check-out time is valid (if provided)
    if (!empty($check_out) && strtotime($check_out) === false) {
        return "Invalid check-out time.";
    }

    // Check if check-out time is after check-in time
    if (!empty($check_out) && strtotime($check_out) <= strtotime($check_in)) {
        return "Check-out time must be after check-in time.";
    }

    // Check if status is valid
    $valid_statuses = ["Present", "Absent"];
    if (!in_array($status, $valid_statuses)) {
        return "Invalid status. Allowed values are 'Present' or 'Absent'.";
    }

    // If all validations pass
    return true;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employee_id = $_POST['employee_id'];
    $attendance_date = $_POST['attendance_date'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $status = $_POST['status'];
    $remarks = $_POST['remarks'];

    // Validate the input
    $validation_result = validateAttendance($conn, $employee_id, $attendance_date, $check_in, $check_out, $status);
    if ($validation_result !== true) {
        echo "<script>
            Swal.fire({
                title: 'Error!',
                text: '$validation_result',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>";
        exit;
    }

    // Use the Procedures class to insert attendance
    $procedures = new Procedures($conn);
    try {
        $procedures->insertAttendanceLog($employee_id, $attendance_date, $check_in, $check_out, $status, $remarks);
        echo "<script>
            Swal.fire({
                title: 'Success!',
                text: 'Attendance recorded successfully!',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                location.reload(); // Reload the page to display updated records
            });
        </script>";
    } catch (Exception $e) {
        echo "<script>
            Swal.fire({
                title: 'Error!',
                text: 'Failed to record attendance: " . $e->getMessage() . "',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>";
    }
}

// Retrieve attendance records
$attendanceRecords = [];
$stmt = $conn->prepare("CALL GetAllAttendanceLogs(?)");
$employee_id = 1; // Replace with the desired EmployeeID or session-based EmployeeID
$stmt->bind_param("i", $employee_id);

$result = $stmt->get_result();
$stmt->execute();
if ($result) {
    $attendanceRecords = $result->fetch_all(MYSQLI_ASSOC);
}
$stmt->close();
?>



<div class="container mt-5 text-center">
    <h2 class="text-center">Attendance Dashboard</h2>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h4>Mark Attendance</h4>
            <form id="attendanceForm" method="POST">
                <div class="form-group">
                    <label for="employeeId">Employee ID</label>
                    <input type="text" class="form-control" id="employeeId" name="employee_id" required>
                </div>
                <div class="form-group">
                    <label for="attendanceDate">Attendance Date</label>
                    <input type="date" class="form-control" id="attendanceDate" name="attendance_date" required>
                </div>
                <div class="form-group">
                    <label for="checkIn">Check-In Time</label>
                    <input type="time" class="form-control" id="checkIn" name="check_in" required>
                </div>
                <div class="form-group">
                    <label for="checkOut">Check-Out Time</label>
                    <input type="time" class="form-control" id="checkOut" name="check_out">
                </div>
                <div class="form-group">
                    <label for="attendanceStatus">Status</label>
                    <select class="form-control" id="attendanceStatus" name="status" required>
                        <option value="">Select Status</option>
                        <option value="Present">Present</option>
                        <option value="Absent">Absent</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="remarks">Remarks</label>
                    <input type="text" class="form-control" id="remarks" name="remarks">
                </div>
                
                <button type="submit" class="btn btn-custom">Submit Attendance</button>
            </form>
            <div class="text-center mt-3">
                <a href="index.php" class="btn btn-warning btn-lg">Back</a>
            </div>
        </div>
        
    </div>
</main>

<?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($attendanceRecords) && !empty($attendanceRecords)): ?>
<div class="container mt-5">
    <h3 class="text-center">Attendance Records</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Employee ID</th>
                <th>Date</th>
                <th>Check-In</th>
                <th>Check-Out</th>
                <th>Status</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($attendanceRecords as $record): ?>
                <tr>
                    <td><?php echo htmlspecialchars($record['EmployeeID']); ?></td>
                    <td><?php echo htmlspecialchars($record['Date']); ?></td>
                    <td><?php echo htmlspecialchars($record['CheckIn']); ?></td>
                    <td><?php echo htmlspecialchars($record['CheckOut']); ?></td>
                    <td><?php echo htmlspecialchars($record['Status']); ?></td>
                    <td><?php echo htmlspecialchars($record['Remarks']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<footer class="footer">
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