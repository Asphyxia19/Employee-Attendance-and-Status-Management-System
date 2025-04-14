CREATE TABLE employee_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(50) NOT NULL,
    request_message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

<?php
// filepath: c:\xampp\htdocs\Employee-Attendance-and-Status-Management-System\php\submit_request.php
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = $_POST['employee_id'];
    $request_message = $_POST['request_message'];

    $stmt = $conn->prepare("CALL SubmitEmployeeRequest(?, ?)");
    $stmt->bind_param("is", $employee_id, $request_message);

    if ($stmt->execute()) {
        echo "<p>Request submitted successfully!</p>";
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>