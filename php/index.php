<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChooksToJarell - Employee Attendance Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css"> <!-- Your custom CSS -->
</head>
<body>
<header class="header">
<img src="../photos/logo.png" alt="ChooksToJarell Logo" class="logo">
    <h4>Employee Attendance Management System</h4>
</header>
<?php 
include 'db_connection.php';

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



<div class="container mt-5">
    <h2 class="text-center">Attendance Dashboard</h2>
    <div class="row">
        <div class="col-md-6">
            <h4>Mark Attendance</h4>
            <form id="attendanceForm">
                <div class="form-group">
                    <label for="employeeId">Employee ID</label>
                    <input type="text" class="form-control" id="employeeId" required>
                </div>
                <div class="form-group">
                    <label for="attendanceStatus">Status</label>
                    <select class="form-control" id="attendanceStatus" required>
                        <option value="">Select Status</option>
                        <option value="Present">Present</option>
                        <option value="Absent">Absent</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-custom">Submit Attendance</button>
            </form>
        </div>
        <div class="col-md-6">
            <h4>Attendance Records</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="attendanceRecords">
                    <?php
                $sql = "SELECT ei.employee_id, ea.attendance_id, ei.first_name, ei.last_name, es.shift_date, es.shift_type, 
               ea.attendance_date, ea.status, ea.check_in, ea.check_out, ea.remarks
        FROM employee_attendance ea
        JOIN employee_info ei ON ea.employee_id = ei.employee_id
        JOIN employee_shift es ON ea.shift_id = es.id
        ORDER BY ea.attendance_date DESC";

$result = $conn->query($sql);

// Check if the query was successful
if (!$result) {
    echo "Error executing query: " . $conn->error;
} else {
    // Check if there are results
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>" . $row['employee_id'] . "</td>
                <td>" . $row['first_name'] . "</td>
                <td>" . $row['last_name'] . "</td>
                <td>" . $row['attendance_date'] . "</td>
                <td>" . $row['status'] . "</td>
                <td>" . $row['check_in'] . "</td>
                <td>" . $row['check_out'] . "</td>
                <td>" . $row['remarks'] . "</td>
                <td>
                    <button class='btn btn-danger delete-btn' data-id='" . $row['attendance_id'] . "'>Delete</button>
                    <button class='btn btn-warning edit-btn' data-id='" . $row['attendance_id'] . "'>Edit</button>
                    <button class='btn btn-info view-btn' data-id='" . $row['attendance_id'] . "'>View</button>
                </td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='9' class='text-center'>No records found.</td></tr>";
    }
}
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<footer class="footer">
    <p>&copy; 2023 ChooksToJarell. All Rights Reserved.</p>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@sweetalert2/11"></script>
<script>
    $(document).ready(function() {
        $('#attendanceForm').on('submit', function(e) {
            e.preventDefault();
            const employeeId = $('#employeeId').val();
            const status = $('#attendanceStatus').val();
            const date = new Date().toLocaleDateString();

            // Add record to the table
            $('#attendanceRecords').append(`
                <tr>
                    <td>${employeeId}</td>
                    <td>${date}</td>
                    <td>${status}</td>
                </tr>
            `);

            // Show success alert
            Swal.fire({
                title: 'Success!',
                text: 'Attendance recorded successfully!',
                icon: 'success',
                confirmButtonText: 'OK'
            });

            // Reset form
            $('#attendanceForm')[0].reset();
        });
    });
</script>
</body>
</html>