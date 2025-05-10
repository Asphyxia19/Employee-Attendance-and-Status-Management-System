<?php
class Database {
    private $host = "localhost"; // Database host
    private $db_name = "attendancemanagement"; // Database name
    private $username = "root"; // Database username
    private $password = ""; // Database password
    public $conn;

    // Get the database connection using PDO
    public function getConnection() {
$this->conn = null;

try {
    $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
    $this->conn = new PDO($dsn, $this->username, $this->password);
    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Enable exceptions for errors
    $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Set default fetch mode
} catch (PDOException $e) {
    die("Connection error: " . $e->getMessage());
}

return $this->conn;
}
}

// Initialize the database connection
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
die("Database connection failed.");
}
?>