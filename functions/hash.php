<?php
require_once '../functions/db_connection.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT ManagerID, Password FROM manager_info";
$result = $db->query($query);

while ($row = $result->fetch_assoc()) {
    $hashedPassword = password_hash($row['Password'], PASSWORD_DEFAULT);
    $updateQuery = "UPDATE manager_info SET Password = ? WHERE ManagerID = ?";
    $stmt = $db->prepare($updateQuery);
    $stmt->bind_param("si", $hashedPassword, $row['ManagerID']);
    $stmt->execute();
}

echo "Passwords updated successfully!";
?>