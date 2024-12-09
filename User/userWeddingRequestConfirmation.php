<?php
include 'userSessionStart.php';
include '../config/connection.php';

if (!isset($_SESSION['username'])) {
    header("Location: userLogin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = $_SESSION['username'];
    $groomName = $_SESSION['groomName'];
    $brideName = $_SESSION['brideName'];
    $datePick = $_SESSION['datePick'];
    $contact = $_SESSION['contact'];
    $commentText = $_SESSION['commentText'];

    $sql = "INSERT INTO wedding_requests (username, groom_name, bride_name, wedding_date, contact, comments)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $username, $groomName, $brideName, $datePick, $contact, $commentText);

    if ($stmt->execute()) {
        // After successfully inserting the appointment, log the action with the scheduled date and time
        $status = "Wedding Certificate Request"; // Include date and time in status
        $activity_time = date('H:i:s');
        // Insert the activity log with the appointment's date and time
        $logSql = "INSERT INTO activity_log (username, status, appointment_date, appointment_time) 
                   VALUES (?, ?, ?, ?)";
        $logStmt = $conn->prepare($logSql);
        $logStmt->bind_param("ssss", $username, $status, $datePick, $activity_time );
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
        <link rel="stylesheet" href="userWedding3.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <script>
            function btnback(event) {
                event.preventDefault();
                window.location.href = "userWeddingRequestInfo.php";
            }
        </script>
    </head>
    <body>
        <?php include 'userHeader.php'; ?>
        <div id="userInfoDiv">
            <a href="#" id="weddingLabel" class="label">Wedding</a>
            <div id="notice">
                <p id="userInfoLabel">Request Confirmation</p>
                <div id="infoContent">
                    <p class="userData"><?php echo $_SESSION['groomName']; ?></p>
                    <p class="userDataLabel">Name of Groom</p>
                    <p class="userData"><?php echo $_SESSION['brideName']; ?></p>
                    <p class="userDataLabel">Name of Bride</p>
                    <p class="userData"><?php echo $_SESSION['datePick']; ?></p>
                    <p class="userDataLabel">Date Married</p>
                    <p class="userData"><?php echo $_SESSION['contact']; ?></p>
                    <p class="userDataLabel">Contact Info</p>
                    <p class="userData"><?php echo $_SESSION['commentText']; ?></p>
                    <p class="userDataLabel">Comments</p>
                </div>
                <p id="confirmLabel">Confirm</p>
                <button id="btnBack" type="button" class="btn btn-danger" onclick="btnback(event)">BACK</button>
                <form method="POST" style="display:inline;">
                    <button id="btnSend" type="submit" class="btn btn-success">SEND</button>
                </form>
            </div>
        </div>
    </body>
</html>