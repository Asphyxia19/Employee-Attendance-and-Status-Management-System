<?php
// filepath: c:\xampp\htdocs\Employee-Attendance-and-Status-Management-System\php\db_connection.php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "attendancemanagement";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>