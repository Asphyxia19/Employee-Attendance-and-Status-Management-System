<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Manager</title>
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

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['manager_id'])) {
    $managerID = intval($_GET['manager_id']);
    $manager = $procedures->getManagerByID($managerID);

    if (!$manager) {
        echo "
        <script>
            Swal.fire({
                title: 'Error!',
                text: 'Manager not found.',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'manager_hub.php';
            });
        </script>";
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $originalManagerID = intval($_POST['original_manager_id']); // Original ManagerID
    $managerID = htmlspecialchars(trim($_POST['manager_id'])); // New ManagerID
    $firstName = htmlspecialchars(trim($_POST['first_name']));
    $lastName = htmlspecialchars(trim($_POST['last_name']));
    $contactNumber = htmlspecialchars(trim($_POST['contact_number']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = !empty($_POST['password']) ? password_hash(htmlspecialchars(trim($_POST['password'])), PASSWORD_BCRYPT) : null;
    $profilePicture = null;

    // Fetch the current manager details to retain existing values
    $currentManager = $procedures->getManagerByID($originalManagerID);

    if (!$currentManager) {
        echo "
        <script>
            Swal.fire({
                title: 'Error!',
                text: 'Manager not found.',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'manager_hub.php';
            });
        </script>";
        exit;
    }

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../photos/';
        $uploadFile = $uploadDir . basename($_FILES['profile_picture']['name']);
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
            $profilePicture = $uploadFile; // Save the new file path
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
    } else {
        // Retain the existing profile picture if no new one is uploaded
        $profilePicture = $currentManager['ProfilePicture'];
    }

    try {
        // Update manager with or without password
        if ($password) {
            $procedures->updateManagerWithPassword($originalManagerID, $managerID, $profilePicture, $firstName, $lastName, $contactNumber, $email, $password);
        } else {
            $procedures->updateManagerWithoutPassword($originalManagerID, $managerID, $profilePicture, $firstName, $lastName, $contactNumber, $email);
        }

        echo "
        <script>
            Swal.fire({
                title: 'Success!',
                text: 'Manager updated successfully!',
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
                text: 'Error updating manager: " . $e->getMessage() . "',
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
    <h2 class="text-center">Edit Manager</h2>
    <form action="manage_edit_manager.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="original_manager_id" value="<?php echo htmlspecialchars($manager['ManagerID']); ?>">
        <div class="form-group">
            <label for="manager_id">Manager ID</label>
            <input type="text" class="form-control" id="manager_id" name="manager_id" maxlength="7" value="<?php echo htmlspecialchars($manager['ManagerID']); ?>" required>
        </div>
        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($manager['FirstName']); ?>" required>
        </div>
        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($manager['LastName']); ?>" required>
        </div>
        <div class="form-group">
            <label for="contact_number">Contact Number</label>
            <input type="text" class="form-control" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($manager['ContactNumber']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($manager['Email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password (leave blank if not changing)</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <div class="form-group">
            <label for="profile_picture">Profile Picture</label>
            <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="manager_hub.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<footer class="footer mt-5">
    <p>&copy; 2025 ChooksToJarell. All Rights Reserved.</p>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>