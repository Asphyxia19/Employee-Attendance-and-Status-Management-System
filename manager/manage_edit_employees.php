<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Edit Employee</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<?php
require_once '../functions/db_connection.php';
require_once '../functions/procedures.php';

// Initialize database and procedures
$database = new Database();
$db = $database->getConnection();
$procedures = new Procedures($db);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['employee_id'])) {
    // Fetch employee details for editing
    $employeeID = intval($_GET['employee_id']);
    $employee = $procedures->getEmployeeByID($employeeID);

    if (!$employee) {
        echo "
        <script>
            Swal.fire({
                title: 'Error!',
                text: 'Employee not found.',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'manage_employees.php';
            });
        </script>";
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employeeID = intval($_POST['employee_id']);
    
    // Fetch employee details for the current employee ID
    $employee = $procedures->getEmployeeByID($employeeID);

    if (!$employee) {
        echo "
        <script>
            Swal.fire({
                title: 'Error!',
                text: 'Employee not found.',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'manage_employees.php';
            });
        </script>";
        exit;
    }

    $firstName = htmlspecialchars(trim($_POST['first_name']));
    $lastName = htmlspecialchars(trim($_POST['last_name']));
    $contactNumber = htmlspecialchars(trim($_POST['contact_number']));
    $email = htmlspecialchars(trim($_POST['email']));
    $address = htmlspecialchars(trim($_POST['address']));
    $position = htmlspecialchars(trim($_POST['position']));
    $hireDate = htmlspecialchars(trim($_POST['hire_date']));
    $password = !empty($_POST['password']) ? password_hash(htmlspecialchars(trim($_POST['password'])), PASSWORD_BCRYPT) : null;
    $profilePicture = isset($employee['ProfilePicture']) ? $employee['ProfilePicture'] : null; // Fallback to null if ProfilePicture is missing

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = mime_content_type($_FILES['profile_picture']['tmp_name']);

        if (!in_array($fileType, $allowedTypes)) {
            echo "
            <script>
                Swal.fire({
                    title: 'Error!',
                    text: 'Invalid file type. Please upload a valid image (JPEG, PNG, GIF, or WEBP).',
                    icon: 'error',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'manage_employees.php';
                });
            </script>";
            exit;
        }

        $uploadDir = '../photos/';
        $uploadFile = $uploadDir . basename($_FILES['profile_picture']['name']);

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
            $profilePicture = $uploadFile; // Save the new file path
        } else {
            echo "
            <script>
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to upload profile picture. Please try again.',
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
        // Update employee with or without password
        if ($password) {
            $procedures->updateEmployeeWithPassword(
                $employeeID,
                $profilePicture,
                $firstName,
                $lastName,
                $contactNumber,
                $email,
                $address,
                $position,
                $hireDate,
                $password
            );
        } else {
            $procedures->updateEmployeeWithoutPassword(
                $employeeID,
                $profilePicture,
                $firstName,
                $lastName,
                $contactNumber,
                $email,
                $address,
                $position,
                $hireDate
            );
        }

        echo "
        <script>
            Swal.fire({
                title: 'Success!',
                text: 'Employee updated successfully!',
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
                text: 'An error occurred while updating the employee: " . addslashes($e->getMessage()) . "',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'manage_employees.php';
            });
        </script>";
    }
}
?>
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Employee Management</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="manage_employees.php">Manage Employees</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add_employee.php">Add Employee</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
</header>
<div class="container mt-5">
    <h2 class="text-center">Edit Employee</h2>
    <form action="manage_edit_employees.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="employee_id" value="<?php echo htmlspecialchars($employee['EmployeeID']); ?>">
        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($employee['FirstName']); ?>" required>
        </div>
        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($employee['LastName']); ?>" required>
        </div>
        <div class="form-group">
            <label for="contact_number">Contact Number</label>
            <input type="text" class="form-control" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($employee['ContactNumber']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($employee['Email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <textarea class="form-control" id="address" name="address" required><?php echo htmlspecialchars($employee['Address']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="position">Position</label>
            <input type="text" class="form-control" id="position" name="position" value="<?php echo htmlspecialchars($employee['Position']); ?>" required>
        </div>
        <div class="form-group">
            <label for="hire_date">Hire Date</label>
            <input type="date" class="form-control" id="hire_date" name="hire_date" value="<?php echo htmlspecialchars($employee['HireDate']); ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password (leave blank if not changing)</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <div class="form-group">
            <label for="profile_picture">Profile Picture (leave blank to keep current)</label>
            <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="manage_employees.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<footer class="footer mt-5">
    <p>&copy; 2025 ChooksToJarell. All Rights Reserved.</p>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>