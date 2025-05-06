<?php
$host = "localhost";
$user = "root";
$password = ""; // replace with your DB password
$database = "attendancemanagement";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $contactNumber = $_POST["contactNumber"];
    $email = $_POST["email"];
    $address = $_POST["address"];
    $position = $_POST["position"];
    $hireDate = $_POST["hireDate"];
    $password = $_POST["password"]; // In real life, hash this!

    // Call stored procedure
    $stmt = $conn->prepare("CALL CreateEmployee(?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $firstName, $lastName, $contactNumber, $email, $address, $position, $hireDate, $password);
    
    if ($stmt->execute()) {
        echo "<p style='color:green;'>Employee added successfully!</p>";
    } else {
        echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>

<!-- HTML FORM -->
<h2>Add New Employee</h2>
<form method="post" action="">
    <label>First Name: <input type="text" name="firstName" required></label><br>
    <label>Last Name: <input type="text" name="lastName" required></label><br>
    <label>Contact Number: <input type="text" name="contactNumber" required></label><br>
    <label>Email: <input type="email" name="email" required></label><br>
    <label>Address: <textarea name="address" required></textarea></label><br>
    <label>Position: <input type="text" name="position" required></label><br>
    <label>Hire Date: <input type="date" name="hireDate" required></label><br>
    <label>Password: <input type="password" name="password" required></label><br>
    <input type="submit" value="Add Employee">
</form>

<!-- DISPLAY EMPLOYEES -->
<h2>All Employees</h2>
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Contact</th>
        <th>Email</th>
        <th>Position</th>
        <th>Hire Date</th>
    </tr>
    <?php
    $result = $conn->query("SELECT EmployeeID, FirstName, LastName, ContactNumber, Email, Position, HireDate FROM employee_info");
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['EmployeeID']}</td>
                <td>{$row['FirstName']} {$row['LastName']}</td>
                <td>{$row['ContactNumber']}</td>
                <td>{$row['Email']}</td>
                <td>{$row['Position']}</td>
                <td>{$row['HireDate']}</td>
              </tr>";
    }
    $conn->close();
    ?>
</table>
