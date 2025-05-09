<?php
class Database {
    private $host = "localhost"; // Database host
    private $db_name = "attendance_management"; // Database name
    private $username = "root"; // Database username
    private $password = ""; // Database password
    public $conn;

    // Get the database connection
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
        } catch (Exception $e) {
            die("Connection error: " . $e->getMessage());
        }

        return $this->conn;
    }
}
?>