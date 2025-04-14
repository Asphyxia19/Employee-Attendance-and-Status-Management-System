<?php
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("CALL CrudEmployeeAttendance('READ', ?, NULL, NULL, NULL, NULL, NULL, NULL, NULL)");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $record = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $employee_id = $_POST['employee_id'];
    $shift_id = $_POST['shift_id'];
    $attendance_date = $_POST['attendance_date'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $status = $_POST['status'];
    $remarks = $_POST['remarks'];

    $stmt = $conn->prepare("CALL CrudEmployeeAttendance('UPDATE', ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiisssss", $id, $employee_id, $shift_id, $attendance_date, $check_in, $check_out, $status, $remarks);

    if ($stmt->execute()) {
        header('Location: index.php');
    } else {
        echo 'Error updating record: ' . $stmt->error;
    }

    $stmt->close();
}
?>