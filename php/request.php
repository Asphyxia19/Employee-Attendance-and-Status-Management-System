<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Request</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<header class="header">
    <img src="../photos/logo.png" alt="ChooksToJarell Logo" class="logo">
</header>

<div class="container mt-5">
    <h2 class="text-center">Employee Request Form</h2>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form method="POST" action="submit_request.php">
                <div class="form-group">
                    <label for="employeeId">Employee ID</label>
                    <input type="text" class="form-control" id="employeeId" name="employee_id" placeholder="Enter your Employee ID" required>
                </div>
                <div class="form-group">
                    <label for="requestMessage">Request</label>
                    <textarea class="form-control" id="requestMessage" name="request_message" rows="8" placeholder="Write your request here..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Submit Request</button>
            </form>
            <!-- Centered Back Button -->
            <div class="text-center mt-3">
                <a href="login.php" class="btn btn-warning btn-lg">Back</a>
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