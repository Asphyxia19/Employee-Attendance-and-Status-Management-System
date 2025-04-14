<?php
// filepath: c:\xampp\htdocs\Employee-Attendance-and-Status-Management-System\php\delete_attendance.php
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    $stmt = $conn->prepare("CALL CrudEmployeeAttendance('DELETE', ?, NULL, NULL, NULL, NULL, NULL, NULL, NULL)");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }

    $stmt->close();
}
?>