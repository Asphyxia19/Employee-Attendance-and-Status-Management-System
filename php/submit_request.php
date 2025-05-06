<?php
require_once '../functions/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employeeID = htmlspecialchars(trim($_POST['employee_id']));
    $requestMessage = htmlspecialchars(trim($_POST['request_message']));

    try {
        // Initialize database connection
        $database = new Database();
        $db = $database->getConnection();

        // Insert the request into the database
        $query = "INSERT INTO requests (EmployeeID, RequestMessage, RequestDate) VALUES (:employee_id, :request_message, NOW())";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':employee_id', $employeeID, PDO::PARAM_INT);
        $stmt->bindParam(':request_message', $requestMessage, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "
            <script>
                Swal.fire({
                    title: 'Success!',
                    text: 'Your request has been submitted successfully.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'request.php';
                });
            </script>";
        } else {
            echo "
            <script>
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to submit your request. Please try again later.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'request.php';
                });
            </script>";
        }
    } catch (Exception $e) {
        echo "
        <script>
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred: " . $e->getMessage() . "',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'request.php';
            });
        </script>";
    }
} else {
    header("Location: request.php");
    exit;
}
?>