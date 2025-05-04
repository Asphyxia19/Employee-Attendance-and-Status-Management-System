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
    $managerID = intval($_POST['manager_id']); // New ManagerID
    $firstName = htmlspecialchars(trim($_POST['first_name']));
    $lastName = htmlspecialchars(trim($_POST['last_name']));
    $email = htmlspecialchars(trim($_POST['email']));

    try {
        $procedures->updateManagerWithID($originalManagerID, $managerID, $firstName, $lastName, $email);
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
</header>
<div class="container mt-5">
    <h2 class="text-center">Edit Manager</h2>
    <form action="manage_edit_manager.php" method="POST">
        <input type="hidden" name="original_manager_id" value="<?php echo htmlspecialchars($manager['ManagerID']); ?>">
        <div class="form-group">
            <label for="manager_id">Manager ID</label>
            <input type="text" class="form-control" id="manager_id" name="manager_id" value="<?php echo htmlspecialchars($manager['ManagerID']); ?>" required>
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
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($manager['Email']); ?>" required>
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