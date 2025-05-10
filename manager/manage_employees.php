<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Employee</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<?php
session_start();
require_once '../functions/db_connection.php';
require_once '../functions/procedures.php';

// Logout logic
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: manager_login.php");
    exit;
}

// Check if the manager is logged in
if (!isset($_SESSION['manager_id'])) {
    header("Location: manager_login.php");
    exit;
}

// Initialize database and procedures
$database = new Database();
$db = $database->getConnection();
$procedures = new Procedures($db);

try {
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $searchTerm = htmlspecialchars(trim($_GET['search']));
        $employees = $procedures->searchEmployees($searchTerm);
    } else {
        $employees = $procedures->getAllEmployees();
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Handle form submissions for editing or deleting employees
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    try {
        if ($action === 'update') {
            $employeeID = intval($_POST['employee_id']);
            $firstName = htmlspecialchars(trim($_POST['first_name']));
            $lastName = htmlspecialchars(trim($_POST['last_name']));
            $contactNumber = htmlspecialchars(trim($_POST['contact_number']));
            $email = htmlspecialchars(trim($_POST['email']));
            $address = htmlspecialchars(trim($_POST['address']));
            $position = htmlspecialchars(trim($_POST['position']));
            $hireDate = htmlspecialchars(trim($_POST['hire_date']));

            $procedures->updateEmployee($employeeID, $firstName, $lastName, $contactNumber, $email, $address, $position, $hireDate, $shift_id, $role_id);
            echo "<script>
                Swal.fire({
                    title: 'Success!',
                    text: 'Employee updated successfully!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'manage_employees.php';
                });
            </script>";
        } elseif ($action === 'delete') {
            $employeeID = intval($_POST['employee_id']);
            $procedures->deleteEmployee($employeeID);
            echo "<script>
                Swal.fire({
                    title: 'Deleted!',
                    text: 'Employee deleted successfully!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'manage_employees.php';
                });
            </script>";
        }
    } catch (Exception $e) {
        echo "<script>
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred: " . $e->getMessage() . "',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>";
    }
}

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
            });
        </script>";
        exit;
    }

    $uploadDir = '../photos/';
    $uploadFile = $uploadDir . basename($_FILES['profile_picture']['name']);

    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
        $profilePicture = $uploadFile; // Save the file path
    } else {
        echo "
        <script>
            Swal.fire({
                title: 'Error!',
                text: 'Failed to upload profile picture. Please try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>";
        exit;
    }
} else {
    // Retain the original profile picture if no new picture is uploaded
    $profilePicture = isset($employee['ProfilePicture']) ? $employee['ProfilePicture'] : '../photos/default-profile.png';
}
?>

<header class="header">
    <img src="../photos/logo.png" alt="ChooksToJarell Logo" class="logo">
    <a href="?action=logout" class="btn btn-danger float-right">Logout</a>
</header>

<div class="container mt-5">
    <h2 class="text-center">Manager Dashboard</h2>

    <!-- Employees Section -->
    <h3>Employees</h3>
    <div class="mb-3">
        <form method="GET" action="manage_employees.php">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search employees..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">Search</button>
                </div>
            </div>
        </form>
    </div>
    <button class="btn btn-primary mb-3" onclick="window.location.href='manage_add_employee.php'">Add Employee</button>
    <table class="table table-bordered">
        <thead>
            <tr>
            <th>Profile Picture</th>
                <th>Employee ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Position</th>
                <th>Shift</th>
                <th>Contact</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
    <?php if (!empty($employees)): ?>
        <?php foreach ($employees as $employee): ?>
            <tr>
                <td class="text-center align-middle">
                    <img src="<?php echo !empty($employee['ProfilePicture']) ? htmlspecialchars($employee['ProfilePicture']) : '../photos/default-profile.png'; ?>" 
                         alt="Profile Picture" 
                         class="img-thumbnail" 
                         style="width: 50px; height: 50px; object-fit: cover;">
                </td>
                <td><?php echo htmlspecialchars($employee['EmployeeID']); ?></td>
                <td><?php echo htmlspecialchars($employee['FirstName']); ?></td>
                <td><?php echo htmlspecialchars($employee['LastName']); ?></td>
                <td><?php echo htmlspecialchars($employee['Position']); ?></td>
                <td><?php echo htmlspecialchars($employee['Shift']); ?></td>
                <td><?php echo htmlspecialchars($employee['ContactNumber']); ?></td>
                <td><?php echo htmlspecialchars($employee['Email']); ?></td>
                <td>
                    <a href="manage_edit_employees.php?employee_id=<?php echo $employee['EmployeeID']; ?>" class="btn btn-warning btn-sm">Edit</a>
                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $employee['EmployeeID']; ?>)">Delete</button>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="9" class="text-center">No employees found.</td>
        </tr>
    <?php endif; ?>
</tbody>
    </table>
    <button class="btn btn-secondary mt-3" onclick="window.location.href='manager.php'">🔙 Back to Manager Hub</button>
</div>

<script>
    function confirmDelete(employeeID) {
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
                fetch('manage_employees.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'delete',
                        employee_id: employeeID
                    })
                })
                .then(response => response.text())
                .then(data => {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'The employee has been deleted.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while deleting the employee.',
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