<?php
require_once '../functions/db_connection.php';
require_once '../functions/procedures.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['manager_id'])) {
        http_response_code(400); // Bad Request
        echo "Manager ID is required.";
        exit;
    }

    $managerID = intval($_POST['manager_id']);

    $database = new Database();
    $db = $database->getConnection();
    $procedures = new Procedures($db);

    try {
        // Call the stored procedure to delete the manager
        $procedures->deleteManager($managerID);

        echo "Manager deleted successfully.";
    } catch (Exception $e) {
        http_response_code(500); // Internal Server Error
        echo "Error deleting manager: " . $e->getMessage();
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo "Invalid request method.";
}
?>