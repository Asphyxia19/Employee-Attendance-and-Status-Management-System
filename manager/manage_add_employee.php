<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employee</title>
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
    $employeeID = htmlspecialchars(trim($_POST['employee_id']));
    $firstName = htmlspecialchars(trim($_POST['first_name']));
    $lastName = htmlspecialchars(trim($_POST['last_name']));
    $contactNumber = htmlspecialchars(trim($_POST['contact_number']));
    $email = htmlspecialchars(trim($_POST['email']));
    $address = htmlspecialchars(trim($_POST['address']));
    $position = htmlspecialchars(trim($_POST['position']));
    $hireDate = htmlspecialchars(trim($_POST['hire_date']));
    $password = password_hash(htmlspecialchars(trim($_POST['password'])), PASSWORD_BCRYPT); // Hash the password
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
                    window.location.href = 'manage_employees.php';
                });
            </script>";
            exit;
        }
    }

    try {
        // Call the procedure to add a new employee with the profile picture
        $procedures->createEmployeeWithProfilePicture($employeeID, $profilePicture, $firstName, $lastName, $contactNumber, $email, $address, $position, $hireDate, $password);
        echo "
        <script>
            Swal.fire({
                title: 'Success!',
                text: 'Employee added successfully!',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'manage_employees.php';
            });
        </script>";
    } catch (Exception $e) {
        echo "
        <script>
            Swal.fire({
                title: 'Error!',
                text: 'Error adding employee: " . $e->getMessage() . "',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'manage_employees.php';
            });
        </script>";
    }
    exit;
}
?>

<header class="header">
    <img src="../photos/logo.png" alt="ChooksToJarell Logo" class="logo">
</header>
<div class="container mt-5">
    <h2 class="text-center">Add Employee</h2>
    <form action="manage_add_employee.php" method="POST" enctype="multipart/form-data">
    <div class="form-group">
    <label for="employee_id">Employee ID</label>
    <input type="text" class="form-control" id="employee_id" name="employee_id" maxlength="5" pattern="\d{1,5}" title="Employee ID must be a numeric value with up to 5 digits" required>
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
            <input type="text" class="form-control" id="contact_number" maxlength="11" name="contact_number" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <textarea class="form-control" id="address" name="address" required></textarea>
        </div>
        <div class="form-group">
            <label for="position">Position</label>
            <input type="text" class="form-control" id="position" name="position" required>
        </div>
        <div class="form-group">
            <label for="hire_date">Hire Date</label>
            <input type="date" class="form-control" id="hire_date" name="hire_date" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="profile_picture">Profile Picture</label>
            <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Add Employee</button>
        <a href="manage_employees.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<footer class="footer mt-5">
    <p>&copy; 2025 ChooksToJarell. All Rights Reserved.</p>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>