<?php
require_once 'database.php';

$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>