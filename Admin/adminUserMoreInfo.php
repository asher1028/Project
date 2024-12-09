<?php
include 'adminSessionStart.php';
include '../config/connection.php'; // Make sure to include the database connection

if (!isset($_SESSION['adminUsername'])) {
    header("Location: adminLogin.php");
    exit();
}

// Get the record ID from the URL
if (isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];

    // Fetch the record from the database
    $query = "SELECT id, CONCAT(first_name, ' ', IFNULL(NULLIF(middle_name, ''), ''), ' ', last_name) AS full_name, email, username, contact_num, created_at, profile_pic, address, birthday, is_restricted FROM user WHERE id = $user_id";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $record = $result->fetch_assoc();

        // Set the profile picture path
        $profilePicPath = "../uploads/" . $record['profile_pic']; // Adjust path as needed
    } else {
        echo "No record found.";
        exit; // Exit if no record found
    }
} else {
    echo "Invalid record ID.";
    exit; // Exit if no ID is provided
}

// Handle the form submission to toggle user status
if (isset($_POST['toggle_restriction'])) {
    $user_id = $_POST['user_id'];
    $current_status = $_POST['current_status'];

    // Toggle status: If 0, set to 1, else set to 0
    $new_status = ($current_status == 0) ? 1 : 0;

    // Update the status in the database
    $updateQuery = "UPDATE user SET is_restricted = $new_status WHERE id = $user_id";
    if ($conn->query($updateQuery) === TRUE) {
        // Redirect to reload the page to reflect changes
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $user_id);
        exit();
    } else {
        echo "Error updating user status: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parish of San Juan</title>
    <link rel="stylesheet" href="adminHomepage.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script>
        function btnback(event) {
            event.preventDefault();
            window.location.href = "adminUsers.php";
        }
    </script>
    <style>
        #restrictUserBtn {
            font-size: 15px;
            width: 80px;
            height: 30px;
            margin: 0 20px;
        }
        /* Using classes for hover styles */
        .disableButton {
            background-color: gray;
            border: none;
            color: white;
        }

        .disableButton:hover {
            background-color: rgb(255, 0, 0); /* Red when Disabled */
        }

        .enableButton {
            background-color: gray;
            border: none;
            color: white;
        }

        .enableButton:hover {
            background-color: rgb(0, 128, 0); /* Green when Enabled */
        } 
    </style>
</head>
<body>
    <?php include 'adminHeader.php'; ?>
    <div class='container-fluid'>
        <div class='row'>
            <div class='col-2'>
                <?php include 'adminSidebar.php'; ?>
            </div>
            <div class="container mt-5 mb-5" style="margin-left: -20px;">
                <h2 id="baptismApplicationLabel">User Details</h2>
                <div id="userReqInfo">
                    <div id="forLabel">
                        <p id="reqInfoLabel">User ID: <?php echo htmlspecialchars($record['id']); ?></p>
                    </div>
                    <div id="toCenter" style="display: flex; justify-content: center; align-items: center; flex-direction: column; text-align: center;">
                        <img src="<?php echo $profilePicPath; ?>" id="profilePic" alt="Profile Picture" style="width: 250px; height: 250px;">
                        <p class="applicantData"><?php echo htmlspecialchars($record['full_name']); ?></p>
                        <p class="applicantDataLabel">Full Name</p>
                    </div>
                    <div id="applicantInfos" class="d-flex">
                        <div id="leftSide">
                            <p class="applicantData"><?php echo htmlspecialchars($record['email']); ?></p>
                            <p class="applicantDataLabel">Email</p>
                            <p class="applicantData"><?php echo htmlspecialchars($record['contact_num']); ?></p>
                            <p class="applicantDataLabel">Contact Number</p>
                            <p class="applicantData"><?php echo htmlspecialchars($record['birthday']); ?></p>
                            <p class="applicantDataLabel">Birthday</p>
                        </div>
                        <div id="rightSide">
                            <p class="applicantData"><?php echo htmlspecialchars($record['username']); ?></p>
                            <p class="applicantDataLabel">Username</p>
                            <p class="applicantData"><?php echo htmlspecialchars($record['address']); ?></p>
                            <p class="applicantDataLabel">Address</p>
                            <p class="applicantData">
                                <?php echo htmlspecialchars($record['is_restricted'] == 0 ? 'Enabled' : 'Disabled'); ?>
                            </p>
                            <p class="applicantDataLabel">Status</p>
                        </div>
                    </div>
                    <form method="POST" action="" style="display: inline;">
                        <input type="hidden" name="user_id" value="<?php echo $record['id']; ?>">
                        <input type="hidden" name="current_status" value="<?php echo $record['is_restricted']; ?>">
                        <div id="forButtons" class="d-flex justify-content-between mt-4">
                            <div id="leftButtons">
                                <button id="backButton" type="button" onclick="btnback(event)">Back</button>
                            </div>
                            <button id="restrictUserBtn" type="submit" name="toggle_restriction" class="restrictButton 
                                <?php echo $record['is_restricted'] == 1 ? 'enableButton' : 'disableButton'; ?>">
                                <?php echo $record['is_restricted'] == 1 ? 'Enable' : 'Disable'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
