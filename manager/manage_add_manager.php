<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add/Edit Manager</title>
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

$managerID = '';
$firstName = '';
$lastName = '';
$email = '';
$password = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['manager_id'])) {
    // Fetch manager details for editing
    $managerID = intval($_GET['manager_id']);
    $manager = $procedures->getManagerByID($managerID);

    if ($manager) {
        $firstName = $manager['FirstName'];
        $lastName = $manager['LastName'];
        $email = $manager['Email'];
    } else {
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
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $managerID = htmlspecialchars(trim($_POST['manager_id']));
    $firstName = htmlspecialchars(trim($_POST['first_name']));
    $lastName = htmlspecialchars(trim($_POST['last_name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = !empty($_POST['password']) ? password_hash(htmlspecialchars(trim($_POST['password'])), PASSWORD_BCRYPT) : null;

    try {
        if (!empty($managerID)) {
            // Update existing manager
            $procedures->updateManager($managerID, $firstName, $lastName, $email, $password);
            $message = 'Manager updated successfully!';
        } else {
            // Add new manager
            $procedures->createManager($firstName, $lastName, $email, $password);
            $message = 'Manager added successfully!';
        }

        echo "
        <script>
            Swal.fire({
                title: 'Success!',
                text: '$message',
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
                text: 'Error saving manager: " . $e->getMessage() . "',
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
    <h2 class="text-center"><?php echo !empty($managerID) ? 'Edit Manager' : 'Add Manager'; ?></h2>
    <form action="manage_add_manager.php" method="POST">
        <div class="form-group">
            <label for="manager_id">Manager ID</label>
            <input type="text" class="form-control" id="manager_id" name="manager_id" value="<?php echo htmlspecialchars($managerID); ?>" <?php echo !empty($managerID) ? 'readonly' : ''; ?>>
        </div>
        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($firstName); ?>" required>
        </div>
        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($lastName); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" <?php echo empty($managerID) ? 'required' : ''; ?>>
            <?php if (!empty($managerID)): ?>
                <small class="form-text text-muted">Leave blank to keep the current password.</small>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary"><?php echo !empty($managerID) ? 'Save Changes' : 'Add Manager'; ?></button>
        <a href="manager_hub.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<footer class="footer mt-5">
    <p>&copy; 2025 ChooksToJarell. All Rights Reserved.</p>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>