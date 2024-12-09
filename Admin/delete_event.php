<?php
include '../config/connection.php'; // Database connection

if (isset($_GET['id'])) {
    $event_id = (int)$_GET['id'];
    // Fetch the event's image to delete it from the file system
    $result = $conn->query("SELECT image_path FROM events WHERE id=$event_id");
    $event = $result->fetch_assoc();
    if ($event) {
        unlink("../Images/" . $event['image_path']); // Delete the image from the server
        $conn->query("DELETE FROM events WHERE id=$event_id"); // Delete event from DB
    }
}
header("Location: admin_events_list.php");
exit();
