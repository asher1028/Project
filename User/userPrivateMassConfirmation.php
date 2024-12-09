<?php 
include 'userSessionStart.php';
include '../config/connection.php';

// Capture user's IP and User Agent

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_SESSION['username'];
    $requesterName = $_SESSION['requesterName'];
    $address = $_SESSION['address'];
    $dateOfMass = $_SESSION['datePick']; // Date of the mass
    $timeOfMass = $_SESSION['timePick']; // Time of the mass
    $HaveParishChoir = $_SESSION['parishChoirAnswer'];
    $contactInfo = $_SESSION['contact'];
    $comments = $_SESSION['commentText'];

    if (isset($_SESSION['username']) && !empty($_SESSION['username'])) {
        $username = $_SESSION['username']; // Use logged-in user's username
    } else {
        $username = NULL; // Set username to NULL if not logged in
    }

    // Insert appointment details into the database
    $sql = "INSERT INTO mass_applications (username, requester_name, address, date_of_mass, time_of_mass, have_parish_choir, contact_info, comments) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $username, $requesterName, $address, $dateOfMass, $timeOfMass, $HaveParishChoir, $contactInfo, $comments);

    if ($stmt->execute()) {
        // After successfully inserting the appointment, log the action with the scheduled date and time
        $status = "Private Mass Appointment"; // Include date and time in status

        // Insert the activity log with the appointment's date and time
        $logSql = "INSERT INTO activity_log (username, status, appointment_date, appointment_time) 
                   VALUES (?, ?, ?, ?)";
        $logStmt = $conn->prepare($logSql);
        $logStmt->bind_param("ssss", $username, $status, $dateOfMass, $timeOfMass);

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
        <link rel="stylesheet" href="userPrivateMassDetails.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <script>
            function btnback(event) {
                event.preventDefault();
                window.location.href = "userPrivateMass.php";
            }
        </script>
    </head>
    <body>
        <?php include 'userHeader.php'; ?>
        <form method="POST" action="">
            <div id="userInfoDiv">
                <a href="#" id="weddingLabel" class="label">Wedding</a>
                <div id="notice">
                    <p id="userInfoLabel">Request Confirmation</p>
                    <div id="infoContent">
                        <p class="userData"><?php echo $_SESSION['requesterName']; ?></p>
                        <p class="userDataLabel">Name of Requestor</p>
                        <p class="userData"><?php echo $_SESSION['address']; ?></p>
                        <p class="userDataLabel">Address</p>
                        <p class="userData"><?php echo $_SESSION['datePick']; ?></p>
                        <p class="userDataLabel">Date</p>
                        <p class="userData"><?php echo $_SESSION['timePick']; ?></p>
                        <p class="userDataLabel">Time</p>
                        <p class="userData"><?php echo $_SESSION['parishChoirAnswer']; ?></p>
                        <p class="userDataLabel">Parish Choir</p>
                        <p class="userData"><?php echo $_SESSION['contact']; ?></p>
                        <p class="userDataLabel">Contact Info.</p>
                        <p class="userData"><?php echo $_SESSION['commentText']; ?></p>
                        <p class="userDataLabel">Comments</p>
                    </div>
                    <p id="confirmLabel">Confirm</p>
                    <button id="btnBack" type="button" class="btn btn-danger" onclick="window.location.href='userPrivateMass.php';">BACK</button>
                    <button id="btnSend" type="submit" class="btn btn-success">SEND</button>
                </div>
            </div>
        </form>
    </body>
</html>
