<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM employee_attendance WHERE id = $id";
    $result = $conn->query($sql);
    $record = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $employee_id = $_POST['employee_id'];
    $attendance_date = $_POST['attendance_date'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $status = $_POST['status'];
    $remarks = $_POST['remarks'];

    $sql = "UPDATE employee_attendance 
            SET employee_id = '$employee_id', attendance_date = '$attendance_date', check_in = '$check_in', 
                check_out = '$check_out', status = '$status', remarks = '$remarks' 
            WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        header('Location: index.php');
    } else {
        echo 'Error updating record: ' . $conn->error;
    }
}
?>