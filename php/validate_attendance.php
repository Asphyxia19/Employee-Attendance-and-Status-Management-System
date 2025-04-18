<?php
function validateAttendance($conn, $employee_id, $attendance_date, $check_in, $check_out, $status) {
    // Check if employee ID exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM employee_info WHERE employee_id = ?");
    $stmt->bind_param("i", $employee_id);
    $employee_exists = 0; // Initialize the variable
    $stmt->bind_result($employee_exists);
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();

    if ($employee_exists == 0) {
        return "Employee ID does not exist.";
    }

    // Validate attendance date
    if (empty($attendance_date)) {
        return "Attendance date is required.";
    }

    // Validate check-in time
    if (empty($check_in)) {
        return "Check-in time is required.";
    }

    // Validate status
    if (empty($status)) {
        return "Attendance status is required.";
    }

    return true; // Validation passed
}
?>