<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Document</title>
</head>
<body>
</body>
</html>
<?php
session_start();
require_once 'db_connection.php';
require_once 'procedures.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $database = new Database();
    $db = $database->getConnection();

    $procedures = new Procedures($db);

    // Registration Logic
    if (isset($_POST['register'])) {
        $managerID = htmlspecialchars(trim($_POST['manager_id']));
        $firstName = htmlspecialchars(trim($_POST['first_name']));
        $lastName = htmlspecialchars(trim($_POST['last_name']));
        $password = htmlspecialchars(trim($_POST['password']));
        $confirmPassword = htmlspecialchars(trim($_POST['confirm_password']));

        // Check if passwords match
        if ($password !== $confirmPassword) {
            echo "
            <script>
                Swal.fire({
                    title: 'Password Mismatch!',
                    text: 'Passwords do not match. Please try again.',
                    icon: 'error'
                });
            </script>";
        } elseif ($procedures->managerIDExists($managerID)) {
            echo "
            <script>
                Swal.fire({
                    title: 'Manager ID In Use!',
                    text: 'This Manager ID is already in use. Please use a different ID.',
                    icon: 'error'
                });
            </script>";
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Create the manager
            if ($procedures->createManager($managerID, $firstName, $lastName, $hashedPassword)) {
                echo "
                <script>
                    Swal.fire({
                        title: 'Registration Successful!',
                        text: 'You can now log in.',
                        icon: 'success'
                    }).then(function() {
                        window.location.href = 'manager_login.php';  // Redirect to login page
                    });
                </script>";
            } else {
                echo "
                <script>
                    Swal.fire({
                        title: 'Error!',
                        text: 'Registration failed. Please try again.',
                        icon: 'error'
                    });
                </script>";
            }
        }
    }

    // Login Logic
    if (isset($_POST['login'])) {
        $managerID = htmlspecialchars(trim($_POST['manager_id']));
        $password = htmlspecialchars(trim($_POST['password']));

        $managerDetails = $procedures->loginManager($managerID, $password);

        if ($managerDetails) {
            $_SESSION['manager_id'] = $managerDetails['ManagerID'];
            $_SESSION['manager_name'] = $managerDetails['FirstName'] . ' ' . $managerDetails['LastName'];
            $_SESSION['logged_in'] = true;

            echo "
            <script>
                Swal.fire({
                    title: 'Welcome!',
                    text: 'Login successful!',
                    icon: 'success'
                }).then(function() {
                    window.location.href = '../manager_dashboard.php';  // Redirect to the dashboard
                });
            </script>";
        } else {
            echo "
            <script>
                Swal.fire({
                    title: 'Error!',
                    text: 'Invalid Manager ID or password. Please try again.',
                    icon: 'error'
                });
            </script>";
        }
    }
}
?>