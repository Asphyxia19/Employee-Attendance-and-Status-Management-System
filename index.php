<?php 
include 'includes/header.php';
include 'db_connection.php'; // Include the database connection file

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employee_id = $_POST['employee_id'];
    $shift_id = $_POST['shift_id'];
    $attendance_date = $_POST['attendance_date'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $status = $_POST['status'];
    $remarks = $_POST['remarks'];

    $sql = "INSERT INTO employee_attendance (employee_id, shift_id, attendance_date, check_in, check_out, status, remarks) 
            VALUES ('$employee_id', '$shift_id', '$attendance_date', '$check_in', '$check_out', '$status', '$remarks')";

    if ($conn->query($sql) === TRUE) {
        echo "<p>New record created successfully!</p>";
    } else {
        echo "<p>Error: " . $sql . "<br>" . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Attendance and Management System</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Welcome to the Employee Attendance and Management System</h1>
        <p>Input attendance data below:</p>

        <!-- Form to input attendance data -->
        <form method="POST" action="">
            <label for="employee_id">Employee ID:</label>
            <input type="number" id="employee_id" name="employee_id" required><br>

            <label for="shift_id">Shift ID:</label>
            <input type="number" id="shift_id" name="shift_id" required><br>

            <label for="attendance_date">Attendance Date:</label>
            <input type="date" id="attendance_date" name="attendance_date" required><br>

            <label for="check_in">Check-In Time:</label>
            <input type="time" id="check_in" name="check_in"><br>

            <label for="check_out">Check-Out Time:</label>
            <input type="time" id="check_out" name="check_out"><br>

            <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="Present">Present</option>
                <option value="Absent">Absent</option>
                <option value="Late">Late</option>
                <option value="On Leave">On Leave</option>
            </select><br>

            <label for="remarks">Remarks:</label>
            <input type="text" id="remarks" name="remarks"><br>

            <button type="submit">Submit</button>
        </form>
    </div>
    <script src="js/scripts.js"></script>
</body>
</html>