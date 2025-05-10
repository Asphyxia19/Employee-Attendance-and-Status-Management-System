<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Requests</title>
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
    header("Location: manager_login.php");
    exit;
}

// Initialize database and procedures
$database = new Database();
$db = $database->getConnection();
$procedures = new Procedures($db);

try {
    // Fetch all requests
    $requests = $procedures->getAllRequests();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Handle form submission for noting requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['noted'])) {
    $requestId = intval($_POST['request_id']);

    try {
        // Delete the request from the database
        $procedures->noteRequest($requestId);

        // Redirect back to the same page with a success flag
        header("Location: manage_requests.php?noted_success=1");
        exit;
    } catch (Exception $e) {
        echo '<div class="alert alert-danger text-center">An error occurred: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}
?>

<header class="header">
    <img src="../photos/logo.png" alt="ChooksToJarell Logo" class="logo">
    <a href="manager_logout.php" class="btn btn-danger float-right">Logout</a>
</header>

<div class="container mt-4">
    <h3>Employee Requests</h3>

    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Request ID</th>
                <th>Employee ID</th>
                <th>Requested For</th>
                <th>Details</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($requests)): ?>
                <?php foreach ($requests as $request): ?>
                    <tr>
                        <td><?= htmlspecialchars($request['RequestID']) ?></td>
                        <td><?= htmlspecialchars($request['EmployeeID']) ?></td>
                        <td><?= htmlspecialchars($request['CreatedAt']) ?></td>
                        <td>
                            <?php
                            // Decode the JSON details
                            $details = json_decode($request['Details'], true);
                            if (isset($details['reason'])) {
                                echo htmlspecialchars($details['reason']);
                            } elseif (isset($details['current_shift']) && isset($details['requested_shift'])) {
                                echo "From " . htmlspecialchars($details['current_shift']) . " to " . htmlspecialchars($details['requested_shift']);
                            } else {
                                echo "No details provided.";
                            }
                            ?>
                        </td>
                        <td>
                            <form method="POST" action="manage_requests.php" style="display:inline;">
                                <input type="hidden" name="request_id" value="<?= htmlspecialchars($request['RequestID']) ?>">
                                <button type="submit" name="noted" class="btn btn-primary btn-sm">Noted</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No requests found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="manager.php" class="btn btn-secondary mt-3">🔙 Back to Manager Hub</a>
</div>

<footer class="footer mt-5 text-center">
    <p>&copy; 2025 ChooksToJarell. All Rights Reserved.</p>
</footer>

<?php if (isset($_GET['noted_success']) && $_GET['noted_success'] == 1): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                title: 'Success!',
                text: 'The request has been noted.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        });
    </script>
<?php endif; ?>
</body>
</html>