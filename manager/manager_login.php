<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Login</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<?php

require_once '../functions/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        die("Database connection failed.");
    }

    $managerID = htmlspecialchars(trim($_POST['ManagerID']));
    $password = htmlspecialchars(trim($_POST['password']));

    try {
        // Check if the manager exists and fetch their details
        $query = "SELECT ManagerID, FirstName, LastName, Password FROM manager_info WHERE ManagerID = :manager_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':manager_id', $managerID, PDO::PARAM_INT);
        $stmt->execute();
        $manager = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($manager) {
            $storedPassword = $manager['Password'];

            // Check if the password is hashed
            if (password_verify($password, $storedPassword)) {
                // Password matches (hashed)
                $_SESSION['manager_id'] = $manager['ManagerID'];
                $_SESSION['manager_name'] = $manager['FirstName'] . ' ' . $manager['LastName'];

                echo "
                <script>
                    Swal.fire({
                        title: 'Welcome!',
                        text: 'Login Successful',
                        icon: 'success'
                    }).then(function() {
                        window.location.href = 'manager.php';  // Redirect to dashboard
                    });
                </script>";
            } elseif ($password === $storedPassword) {
                // Password matches (plain text)
                $_SESSION['manager_id'] = $manager['ManagerID'];
                $_SESSION['manager_name'] = $manager['FirstName'] . ' ' . $manager['LastName'];

                echo "
                <script>
                    Swal.fire({
                        title: 'Welcome!',
                        text: 'Login Successful',
                        icon: 'success'
                    }).then(function() {
                        window.location.href = 'manager.php';  // Redirect to dashboard
                    });
                </script>";
            } else {
                // Password does not match
                echo "
                <script>
                    Swal.fire({
                        title: 'Oops!',
                        text: 'Invalid password. Please try again.',
                        icon: 'error'
                    }).then(function() {
                        window.location.href = 'manager_login.php';  // Redirect back to login page
                    });
                </script>";
            }
        } else {
            // Manager not found
            echo "
            <script>
                Swal.fire({
                    title: 'Oops!',
                    text: 'Manager ID not found. Please try again.',
                    icon: 'error'
                }).then(function() {
                    window.location.href = 'manager_login.php';  // Redirect back to login page
                });
            </script>";
        }
    } catch (Exception $e) {
        // Handle any errors
        echo "
        <script>
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred: " . $e->getMessage() . "',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>";
    }
}
?>

<header class="header">
    <img src="../photos/logo.png" alt="ChooksToJarell Logo" class="logo">
</header>

<div class="container mt-5">
    <h2 class="text-center">Manager Login</h2>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form method="POST" action="manager_login.php">
                <div class="form-group">
                    <label for="ManagerID">Manager ID</label>
                    <input type="text" class="form-control" id="ManagerID" name="ManagerID" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
            <div class="text-center mt-3">
                <a href="../php/index.php" class="btn btn-warning btn-lg">Back</a>
            </div>
        </div>
    </div>
</div>

<footer class="footer mt-5">
    <p>&copy; 2025 ChooksToJarell. All Rights Reserved.</p>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>