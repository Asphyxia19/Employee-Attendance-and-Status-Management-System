<?php
require_once '../functions/db_connection.php';
require_once '../functions/procedures.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $procedures = new Procedures($conn);

    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $contactNumber = $_POST['contactNumber'];
    $email = $_POST['email'];
    $position = $_POST['position'];
    $hireDate = $_POST['hireDate'];

    $procedures->addEmployee($firstName, $lastName, $contactNumber, $email, '', $position, $hireDate);

    echo "<script>
        Swal.fire({
            title: 'Success!',
            text: 'Employee added successfully.',
            icon: 'success'
        }).then(function() {
            window.location.href = 'manager.php';
        });
    </script>";
}
?>