<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Hub</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<?php
session_start();
require_once '../functions/db_connection.php';
require_once '../functions/procedures.php';

// Check if the manager is logged in
if (!isset($_SESSION['manager_id'])) {
    header("Location: manager_login.php"); // Redirect to login page if not logged in
    exit;
}

// Fetch manager details
$database = new Database();
$db = $database->getConnection();
$procedures = new Procedures($db);

try {
    // Fetch all managers
    $managers = $procedures->getAllManagers();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<header class="header">
    <img src="../photos/logo.png" alt="ChooksToJarell Logo" class="logo">
    <a href="manager_logout.php" class="btn btn-danger float-right">Logout</a>
</header>

<div class="container mt-5">
    <h2 class="text-center">Manager Hub</h2>

    <!-- Managers Section -->
    <h3>Managers</h3>
    <button class="btn btn-primary mb-3" onclick="window.location.href='add_manager.php'">Add Manager</button>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Manager ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($managers as $manager): ?>
                <tr>
                    <td><?php echo htmlspecialchars($manager['ManagerID']); ?></td>
                    <td><?php echo htmlspecialchars($manager['FirstName']); ?></td>
                    <td><?php echo htmlspecialchars($manager['LastName']); ?></td>
                    <td><?php echo htmlspecialchars($manager['Email']); ?></td>
                    <td>
                        <a href="edit_manager.php?manager_id=<?php echo $manager['ManagerID']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $manager['ManagerID']; ?>)">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    function confirmDelete(managerID) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Perform the delete action
                fetch('crud.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'deleteManager',
                        manager_id: managerID
                    })
                })
                .then(response => response.text())
                .then(data => {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'The manager has been deleted.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload(); // Reload the page to reflect changes
                    });
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while deleting the manager.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    console.error('Error:', error);
                });
            }
        });
    }
</script>

<footer class="footer mt-5">
    <p>&copy; 2025 ChooksToJarell. All Rights Reserved.</p>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>