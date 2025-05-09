<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<header class="header">
    <img src="../photos/logo.png" alt="ChooksToJarell Logo" class="logo">
</header>

<div class="container mt-5">
    <h2 class="text-center">Employee Dashboard</h2>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center">
                    <h3>👤 Employee Information</h3>
                </div>
                <div class="card-body">
                    <?php
                    session_start(); // Start the session

                    require_once '../functions/db_connection.php';
                    require_once '../functions/procedures.php';

                    if (!isset($_SESSION['employee_id'])) {
                        // Redirect to login page if not logged in
                        header("Location: employee_login.php");
                        exit;
                    }

                    $employeeID = $_SESSION['employee_id']; // Get the logged-in EmployeeID
                    $database = new Database();
                    $db = $database->getConnection();
                    $procedures = new Procedures($db);

                    try {
                        $employee = $procedures->getEmployeeByID($employeeID);
                        if ($employee) {
                            echo "<p><strong>Full Name:</strong> " . htmlspecialchars($employee['FirstName'] . " " . $employee['LastName']) . "</p>";
                            echo "<p><strong>Position:</strong> " . htmlspecialchars($employee['Position']) . "</p>";
                            echo "<p><strong>Contact Number:</strong> " . htmlspecialchars($employee['ContactNumber']) . "</p>";
                            echo "<p><strong>Email:</strong> " . htmlspecialchars($employee['Email']) . "</p>";
                            echo "<p><strong>Address:</strong> " . htmlspecialchars($employee['Address']) . "</p>";
                            echo "<p><strong>Hire Date:</strong> " . htmlspecialchars($employee['HireDate']) . "</p>";
                        } else {
                            echo "<p class='text-danger'>Employee details not found.</p>";
                        }
                    } catch (Exception $e) {
                        echo "<p class='text-danger'>Error: " . $e->getMessage() . "</p>";
                    }
                    ?>
                </div>
                <div class="card-footer text-center">
                    <a href="edit_personal_info.php" class="btn btn-warning">📝 Request to Edit Information</a>
                    <a href="delete_account.php" class="btn btn-danger">❌ Request to Delete Account</a>
                    <a href="employee_login.php" class="btn btn-secondary">🔚 Logout</a>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="footer mt-5">
    <p>&copy; 2025 ChooksToJarell. All Rights Reserved.</p>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@sweetalert2/11"></script>
</body>
</html>