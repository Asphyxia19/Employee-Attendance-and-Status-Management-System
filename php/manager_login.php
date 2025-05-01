<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../functions/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userid = $_POST['username'];
    $password = $_POST['password'];

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT Password FROM manager_info WHERE ManagerID = ?");
    $stmt->bind_param("s", $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['Password'])) {
            $_SESSION['ManagerID'] = $userid; // Set the session variable
            header("Location: manager.php");
            exit();
        } else {
            echo "<script>
                Swal.fire({
                    title: 'Error!',
                    text: 'Invalid password. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            </script>";
        }
    } else {
        echo "<script>
            Swal.fire({
                title: 'Error!',
                text: 'Invalid username or role.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<header class="header">
    <img src="../photos/logo.png" alt="ChooksToJarell Logo" class="logo">
</header>

<div class="container mt-5">
    <h2 class="text-center">Manager Login</h2>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form method="POST" action="manager_login.php">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
            <!-- Centered Back Button -->
            <div class="text-center mt-3">
                <a href="login.php" class="btn btn-warning btn-lg">Back</a>
            </div>
        </div>
    </div>
</div>

<footer class="footer mt-5">
    <p>&copy; 2025 ChooksToJarell. All Rights Reserved.</p>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@sweetalert2/11"></script>
</body>
</html>