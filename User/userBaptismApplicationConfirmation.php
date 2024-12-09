<?php
include 'userSessionStart.php';
include '../config/connection.php'; // Include your database connection

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: userLogin.php");
    exit();
}

// Handle form submission for sending data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from session
    $username = $_SESSION['username'];
    $childName = $_SESSION['childName'];
    $motherName = $_SESSION['motherName'];
    $fatherName = $_SESSION['fatherName'];
    $godmotherName = $_SESSION['godmotherName'];
    $godfatherName = $_SESSION['godfatherName'];
    $dateBaptized = $_SESSION['datePick'];  // Date of baptism from session
    $timeBaptized = $_SESSION['timePick'];  // Time of baptism from session
    $contactInfo = $_SESSION['contact'];
    $comments = $_SESSION['commentText'];

    // Prepare SQL statement to insert baptism application
    $sql = "INSERT INTO baptism_applications (username, child_name, mother_name, father_name, godmother_name, godfather_name, date_baptized, time_baptized, contact_info, comments) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssss", $username, $childName, $motherName, $fatherName, $godmotherName, $godfatherName, $dateBaptized, $timeBaptized, $contactInfo, $comments);

    // Execute statement
    if ($stmt->execute()) {
        // After successfully inserting the appointment, log the action with the scheduled date and time
        $status = "Baptism Application "; // Include date and time in status

        // Insert the activity log with the appointment's date and time
        $logSql = "INSERT INTO activity_log (username, status, appointment_date, appointment_time) 
                   VALUES (?, ?, ?, ?)";
        $logStmt = $conn->prepare($logSql);
        $logStmt->bind_param("ssss", $username, $status, $dateBaptized, $timeBaptized);

        // Execute the log insert
        $logStmt->execute();
        $logStmt->close();

        // Redirect to the application details page
        header("Location: userApplicationDetails.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    session_unset(); // Unset session variables after the appointment
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Parish of San Juan</title>
    <link rel="stylesheet" href="userBaptismApplication2.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
    <?php include 'userHeader.php'; ?>
    <form method="POST" action="">
        <div id="userInfoDiv">
            <a href="#" id="baptismLabel" class="label">Baptism</a>
            <div id="noticeConfirm">
                <p id="userInfoLabel">Application Confirmation</p>
                <div id="infoContent">
                    <p class="userData"><?php echo $_SESSION['childName']; ?></p>
                    <p class="userDataLabel">Name of Child</p>
                    <p class="userData"><?php echo $_SESSION['motherName']; ?></p>
                    <p class="userDataLabel">Names of Mother (Maiden Name)</p>
                    <p class="userData"><?php echo $_SESSION['fatherName']; ?></p>
                    <p class="userDataLabel">Names of Father</p>
                    <p class="userData"><?php echo $_SESSION['godmotherName']; ?></p>
                    <p class="userDataLabel">Godmother</p>
                    <p class="userData"><?php echo $_SESSION['godfatherName']; ?></p>
                    <p class="userDataLabel">Godfather</p>
                    <p class="userData"><?php echo $_SESSION['datePick']; ?></p>
                    <p class="userDataLabel">Date of Baptism</p>
                    <p class="userData"><?php echo $_SESSION['timePick']; ?></p>
                    <p class="userDataLabel">Time of Baptism</p>
                    <p class="userData"><?php echo $_SESSION['contact']; ?></p>
                    <p class="userDataLabel">Contact Info</p>
                    <p class="userData"><?php echo $_SESSION['commentText']; ?></p>
                    <p class="userDataLabel">Comments</p>
                </div>
                <p id="confirmLabel">Confirm</p>
                <button id="btnBack" type="button" class="btn btn-danger" onclick="window.location.href='userBaptismApplication.php';">BACK</button>
                <button id="btnSend" type="submit" class="btn btn-success">SEND</button>
            </div>
        </div>
    </form>
</body>
</html>
