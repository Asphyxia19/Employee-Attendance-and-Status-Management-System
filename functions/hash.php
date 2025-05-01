<?php
require_once '../functions/db_connection.php';

$database = new Database();
$conn = $database->getConnection();

$query = "SELECT ManagerID, Password FROM manager_info";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    $hashedPassword = password_hash($row['Password'], PASSWORD_DEFAULT);
    $updateQuery = $conn->prepare("UPDATE manager_info SET Password = ? WHERE ManagerID = ?");
    $updateQuery->bind_param("si", $hashedPassword, $row['ManagerID']);
    $updateQuery->execute();
    $updateQuery->close();
}

echo "Passwords updated successfully!";
?>