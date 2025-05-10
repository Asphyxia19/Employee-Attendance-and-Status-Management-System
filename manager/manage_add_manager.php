<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Manager</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<?php
require_once '../functions/db_connection.php';
require_once '../functions/procedures.php';

$database = new Database();
$db = $database->getConnection();
$procedures = new Procedures($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $managerID = htmlspecialchars(trim($_POST['manager_id']));
    $firstName = htmlspecialchars(trim($_POST['first_name']));
    $lastName = htmlspecialchars(trim($_POST['last_name']));
    $contactNumber = htmlspecialchars(trim($_POST['contact_number']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = !empty($_POST['password']) ? password_hash(htmlspecialchars(trim($_POST['password'])), PASSWORD_BCRYPT) : null;
    $profilePicture = null;

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../photos/';
        $uploadFile = $uploadDir . basename($_FILES['profile_picture']['name']);
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
            $profilePicture = $uploadFile; // Save the file path
        } else {
            echo "
            <script>
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to upload profile picture.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'manager_hub.php';
                });
            </script>";
            exit;
        }
    }

    try {
        if ($password === null) {
            throw new Exception("Password is required when creating a new manager.");
        }

        // Call the createManager procedure
        $procedures->createManager($managerID, $firstName, $lastName, $contactNumber, $email, $password, $profilePicture);

        echo "
        <script>
            Swal.fire({
                title: 'Success!',
                text: 'Manager added successfully!',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'manager_hub.php';
            });
        </script>";
    } catch (Exception $e) {
        echo "
        <script>
            Swal.fire({
                title: 'Error!',
                text: 'Error adding manager: " . $e->getMessage() . "',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'manager_hub.php';
            });
        </script>";
    }
    exit;
}
?>

<header class="header">
    <img src="../photos/logo.png" alt="ChooksToJarell Logo" class="logo">
    <a href="manager_logout.php" class="btn btn-danger float-right">Logout</a>
</header>
<div class="container mt-5">
    <h2 class="text-center">Add Manager</h2>
    <form action="manage_add_manager.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="manager_id">Manager ID</label>
            <input type="text" class="form-control" maxlength = "7" ="manager_id" name="manager_id" required>
        </div>
        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" required>
        </div>
        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" required>
        </div>
        <div class="form-group">
            <label for="contact_number">Contact Number</label>
            <input type="text" class="form-control" id="contact_number" name="contact_number" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="profile_picture">Profile Picture</label>
            <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Add Manager</button>
        <a href="manager_hub.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<footer class="footer mt-5">
    <p>&copy; 2025 ChooksToJarell. All Rights Reserved.</p>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>