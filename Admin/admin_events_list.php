<?php
include '../config/connection.php'; // Database connection
$events = $conn->query("SELECT * FROM events ORDER BY created_at DESC");

echo "<h1 class='text-light'>Manage Events</h1>";

while ($event = $events->fetch_assoc()):
?>
    <div class="event-item">
        <h3 class="text-light"><?php echo htmlspecialchars($event['title']); ?></h3>
        <p class="text-light"><?php echo htmlspecialchars($event['description']); ?></p>
        <img src="../Images/<?php echo htmlspecialchars($event['image_path']); ?>" alt="Event Image" width="150">
        <a href="admin_event.php?id=<?php echo $event['id']; ?>" class="btn btn-warning">Edit</a>
        <a href="delete_event.php?id=<?php echo $event['id']; ?>" class="btn btn-danger">Delete</a>
    </div>
<?php endwhile; ?>
