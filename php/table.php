<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <h1>Employee Attendance Records</h1>
    <table border="1">
        <tr>
            <th>Employee ID</th>
            <th>Shift ID</th>
            <th>Attendance Date</th>
            <th>Check-In</th>
            <th>Check-Out</th>
            <th>Status</th>
            <th>Remarks</th>
        </tr>
        <?php
        require_once 'db_connection.php';

        $sql = "SELECT * FROM employee_attendance";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['employee_id'] . "</td>";
                echo "<td>" . $row['shift_id'] . "</td>";
                echo "<td>" . $row['attendance_date'] . "</td>";
                echo "<td>" . $row['check_in'] . "</td>";
                echo "<td>" . $row['check_out'] . "</td>";
                echo "<td>" . $row['status'] . "</td>";
                echo "<td>" . $row['remarks'] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No records found</td></tr>";
        }

        $conn->close();
        ?>
    </table>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</html>