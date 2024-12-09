<?php
include 'adminSessionStart.php';
require_once '../config/connection.php';

if (!isset($_SESSION['username'])) {
    die("You must be logged in to perform this action.");
}

// Get the current logged-in user ID from session
$user_id = $_SESSION['username'];

// Function to log user activity
function log_user_activity($user_id, $activity_type, $activity_description) {
    global $db; // Database connection
    
    // Prepare the SQL query to insert a new activity log
    $sql = "INSERT INTO user_activity_logs (user_id, activity_type, activity_description) 
            VALUES (?, ?, ?)";
    
    // Prepare the statement
    if ($stmt = $db->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param("iss", $user_id, $activity_type, $activity_description);
        
        // Execute the statement
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    } else {
        // If statement preparation fails
        return false;
    }
}

// Example function for logging login activity
function log_login() {
    global $user_id;
    $activity_type = "Login";
    $activity_description = "User logged in successfully.";
    return log_user_activity($user_id, $activity_type, $activity_description);
}

// Example function for logging password change activity
function log_password_change() {
    global $user_id;
    $activity_type = "Password Change";
    $activity_description = "User changed their password.";
    return log_user_activity($user_id, $activity_type, $activity_description);
}

// Example function for logging baptism schedule application
function log_baptism_application() {
    global $user_id;
    $activity_type = "Baptism Application";
    $activity_description = "User applied for baptism schedule.";
    return log_user_activity($user_id, $activity_type, $activity_description);
}

// Here you can call the respective function depending on the action being performed
// For example, you can call one of the log functions when user logs in, changes password, etc.

// For testing purposes:
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    if ($action == "login") {
        if (log_login()) {
            echo "Login activity logged successfully!";
        } else {
            echo "Failed to log login activity.";
        }
    } elseif ($action == "password_change") {
        if (log_password_change()) {
            echo "Password change activity logged successfully!";
        } else {
            echo "Failed to log password change activity.";
        }
    } elseif ($action == "baptism_application") {
        if (log_baptism_application()) {
            echo "Baptism application activity logged successfully!";
        } else {
            echo "Failed to log baptism application activity.";
        }
    }
}

?>

<!-- You can use simple HTML for testing -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Activity Monitor</title>
</head>
<body>
    <h1>Monitor User Activity</h1>
    
    <!-- Link to simulate different actions -->
    <a href="monitor_activity.php?action=login">Log Login Activity</a><br>
    <a href="monitor_activity.php?action=password_change">Log Password Change Activity</a><br>
    <a href="monitor_activity.php?action=baptism_application">Log Baptism Application Activity</a><br>
    
</body>
</html>