<?php
require_once '../functions/db_connection.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT ManagerID, Password FROM manager_info";
$stmt = $db->prepare($query);
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Check if the password is already hashed
    if (!password_verify('test', $row['Password'])) { // Replace 'test' with a known plain-text password
        $hashedPassword = password_hash($row['Password'], PASSWORD_DEFAULT);
        $updateQuery = "UPDATE manager_info SET Password = :password WHERE ManagerID = :manager_id";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bindParam(':password', $hashedPassword);
        $updateStmt->bindParam(':manager_id', $row['ManagerID']);
        $updateStmt->execute();
    }
}

echo "Passwords updated successfully!";
?>