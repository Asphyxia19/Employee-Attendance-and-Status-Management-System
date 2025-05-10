<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Request Page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<header class="header">
    <img src="../photos/logo.png" alt="Company Logo" class="logo">
</header>

<?php
require_once '../functions/procedures.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../functions/db_connection.php';

    $employeeId = $_POST['employee_id'];
    $requestType = $_POST['request_type'];
    $details = null;

    try {
        // Initialize the database connection
        $database = new Database();
        $conn = $database->getConnection();

        // Initialize the Procedures class
        $procedures = new Procedures($conn);

        // Prepare details based on request type
        if ($requestType === 'Change Shift') {
            $currentShift = $_POST['current_shift'];
            $requestedShift = $_POST['requested_shift'];
            $shiftChangeDate = $_POST['shift_change_date'];
            $details = json_encode([
                'current_shift' => $currentShift,
                'requested_shift' => $requestedShift,
                'shift_change_date' => $shiftChangeDate
            ]);
        } elseif ($requestType === 'Sick Leave') {
            $details = json_encode(['reason' => $_POST['sick_leave_reason']]);
        } elseif ($requestType === 'Vacation Leave') {
            $details = json_encode(['reason' => $_POST['vacation_leave_reason']]);
        }

        // Call the insertRequest function
        $procedures->insertRequest($employeeId, $requestType, $details);

        echo '<div class="alert alert-success text-center">Request submitted successfully!</div>';
    } catch (Exception $e) {
        echo '<div class="alert alert-danger text-center">An error occurred: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="text-center mb-0">Employee Request Form</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="" id="requestForm">
                        <div class="form-group">
                            <label for="employeeId" class="required-field">Employee ID</label>
                            <input type="text" class="form-control" id="employeeId" maxlength="5" name="employee_id" 
                                   placeholder="Enter your Employee ID" required>
                        </div>
                        <div class="form-group">
                            <label class="required-field">Request Type</label>
                            <div class="row">
                                <!-- Change Shift Option -->
                                <div class="col-md-4 mb-3">
                                    <div class="card request-type-card" onclick="selectRequestType('changeShift')" id="changeShiftCard">
                                        <div class="card-body text-center">
                                            <i class="fas fa-exchange-alt fa-3x mb-3 text-primary"></i>
                                            <h5>Change Shift</h5>
                                            <p class="text-muted">Request shift schedule change</p>
                                        </div>
                                    </div>
                                    <input type="radio" name="request_type" value="Change Shift" id="changeShift" style="display: none;" required>
                                </div>
                                <!-- Sick Leave Option -->
                                <div class="col-md-4 mb-3">
                                    <div class="card request-type-card" onclick="selectRequestType('sickLeave')" id="sickLeaveCard">
                                        <div class="card-body text-center">
                                            <i class="fas fa-procedures fa-3x mb-3 text-danger"></i>
                                            <h5>Sick Leave</h5>
                                            <p class="text-muted">Notify for medical absence</p>
                                        </div>
                                    </div>
                                    <input type="radio" name="request_type" value="Sick Leave" id="sickLeave" style="display: none;">
                                </div>
                                <!-- Vacation Leave Option -->
                                <div class="col-md-4 mb-3">
                                    <div class="card request-type-card" onclick="selectRequestType('vacationLeave')" id="vacationLeaveCard">
                                        <div class="card-body text-center">
                                            <i class="fas fa-umbrella-beach fa-3x mb-3 text-success"></i>
                                            <h5>Vacation Leave</h5>
                                            <p class="text-muted">Request for time off</p>
                                        </div>
                                    </div>
                                    <input type="radio" name="request_type" value="Vacation Leave" id="vacationLeave" style="display: none;">
                                </div>
                            </div>
                        </div>
                        <!-- Change Shift Details -->
                        <div class="request-details" id="changeShiftDetails">
                            <div class="form-group">
                                <label class="required-field">Current Shift</label><br>
                                <div class="form-check form-check-inline shift-option">
                                    <input class="form-check-input" type="radio" name="current_shift" id="currentDayShift" value="Day Shift (8AM-5PM)" required>
                                    <label class="form-check-label" for="currentDayShift">Day Shift (8AM-5PM)</label>
                                </div>
                                <div class="form-check form-check-inline shift-option">
                                    <input class="form-check-input" type="radio" name="current_shift" id="currentNightShift" value="Night Shift (10PM-7AM)">
                                    <label class="form-check-label" for="currentNightShift">Night Shift (8PM-4AM)</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="required-field">Requested Shift</label><br>
                                <div class="form-check form-check-inline shift-option">
                                    <input class="form-check-input" type="radio" name="requested_shift" id="requestedDayShift" value="Day Shift (8AM-5PM)" required>
                                    <label class="form-check-label" for="requestedDayShift">Day Shift (8AM-5PM)</label>
                                </div>
                                <div class="form-check form-check-inline shift-option">
                                    <input class="form-check-input" type="radio" name="requested_shift" id="requestedNightShift" value="Night Shift (10PM-7AM)">
                                    <label class="form-check-label" for="requestedNightShift">Night Shift (8PM-4AM)</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="shiftChangeDate" class="required-field">Requested Date</label>
                                <input type="date" class="form-control" id="shiftChangeDate" name="shift_change_date" required>
                            </div>
                        </div>
                        <!-- Sick Leave Details -->
                        <div class="request-details" id="sickLeaveDetails">
                            <div class="form-group">
                                <label for="sickLeaveReason" class="required-field">Reason for Sick Leave</label>
                                <textarea class="form-control" id="sickLeaveReason" name="sick_leave_reason" 
                                          rows="3" placeholder="Please explain your medical condition..." required></textarea>
                            </div>
                        </div>
                        <!-- Vacation Leave Details -->
                        <div class="request-details" id="vacationLeaveDetails">
                            <div class="form-group">
                                <label for="vacationLeaveReason" class="required-field">Reason for Vacation Leave</label>
                                <textarea class="form-control" id="vacationLeaveReason" name="vacation_leave_reason" 
                                          rows="3" placeholder="Please explain the purpose of your vacation..." required></textarea>
                            </div>
                        </div>
                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="fas fa-paper-plane"></i> Submit Request
                            </button>
                        </div>
                    </form>
                    <div class="text-center mt-3">
                        <a href="index.php" class="btn btn-warning">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<footer class="footer mt-5">
    <p>&copy; 2025 ChooksToJarell. All Rights Reserved.</p>
</footer>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function selectRequestType(type) {
        // Reset all cards and hide all details
        $('.request-type-card').removeClass('active');
        $('.request-details').hide();
        $('.request-details select, .request-details input, .request-details textarea').prop('required', false);
        // Activate selected card and show relevant details
        $('#' + type + 'Card').addClass('active');
        $('#' + type).prop('checked', true);
        $('#' + type + 'Details').show();
        $('#' + type + 'Details select, #' + type + 'Details input, #' + type + 'Details textarea').prop('required', true);
    }
    // Form validation
    $('#requestForm').submit(function(e) {
        if (!$('input[name="request_type"]:checked').length) {
            e.preventDefault();
            Swal.fire('Error', 'Please select a request type.', 'error');
            return false;
        }
        
        // Validate shift change selection
        if ($('#changeShift').is(':checked')) {
            if (!$('input[name="current_shift"]:checked').length) {
                e.preventDefault();
                Swal.fire('Error', 'Please select your current shift.', 'error');
                return false;
            }
            if (!$('input[name="requested_shift"]:checked').length) {
                e.preventDefault();
                Swal.fire('Error', 'Please select your requested shift.', 'error');
                return false;
            }
            if (!$('#shiftChangeDate').val()) {
                e.preventDefault();
                Swal.fire('Error', 'Please select the requested date.', 'error');
                return false;
            }
        }
        
        return true;
    });
    // Set minimum dates for date inputs to today
    const today = new Date().toISOString().split('T')[0];
    $('input[type="date"]').attr('min', today);
</script>
</body>
</html>