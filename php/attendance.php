<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Login</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/styles.css">
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

    // Proceed to record attendance
    $stmt = $conn->prepare("CALL CrudEmployeeAttendance('CREATE', NULL, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $employee_id, $attendance_date, $check_in, $check_out, $status, $remarks);

    if ($stmt->execute()) {
        echo "<script>
            Swal.fire({
                title: 'Success!',
                text: 'Attendance recorded successfully!',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                title: 'Error!',
                text: 'Failed to record attendance. Please try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>";
    }

    $stmt->close();
}
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
</div>

<footer class="footer">
    <p>&copy; 2025 ChooksToJarell. All Rights Reserved.</p>
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

        // Handle delete button click
        $(document).on('click', '.delete-btn', function() {
            const id = $(this).data('id');
            if (confirm('Are you sure you want to delete this record?')) {
                $.ajax({
                    url: 'delete_attendance.php',
                    type: 'POST',
                    data: { id: id },
                    success: function(response) {
                        if (response === 'success') {
                            alert('Record deleted successfully!');
                            location.reload();
                        } else {
                            alert('Failed to delete record.');
                        }
                    }
                });
            }
        });

        // Handle edit button click
        $(document).on('click', '.edit-btn', function() {
            const id = $(this).data('id');
            // Redirect to edit page with the record ID
            window.location.href = `edit_attendance.php?id=${id}`;
        });
    });
</script>
</body>
</html>