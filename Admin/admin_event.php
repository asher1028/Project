<?php
include 'adminSessionStart.php';
include '../config/connection.php'; // Database connection

if (!isset($_SESSION['adminUsername'])) {
    header("Location: adminLogin.php");
    exit();
}

// Handle form submission for adding/editing event
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $image_path = '';

    // Image upload handling
    if ($_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
        $image_name = basename($_FILES['event_image']['name']);
        $target_dir = "../Images/";
        $target_file = $target_dir . $image_name;

        // Check if image is valid
        if (move_uploaded_file($_FILES['event_image']['tmp_name'], $target_file)) {
            $image_path = $image_name; // Save image path
        }
    }

    // Insert new event or update existing one
    if (isset($_GET['id'])) { // Update event if id is set
        $event_id = (int)$_GET['id'];
        $conn->query("UPDATE events SET title='$title', description='$description', image_path='$image_path' WHERE id=$event_id");
    } else { // Add new event
        $conn->query("INSERT INTO events (title, description, image_path) VALUES ('$title', '$description', '$image_path')");
    }
}

// Fetch event details for editing
$event = null;
if (isset($_GET['id'])) {
    $event_id = (int)$_GET['id'];
    $result = $conn->query("SELECT * FROM events WHERE id=$event_id");
    $event = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($event) ? 'Edit Event' : 'Add Event'; ?></title>

    <!-- Bootstrap 4 and Custom Styles -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        .form-container {
            max-width: 700px;
            margin: 50px auto;
            background-color: rgba(255,255,255, 0.5);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-right: 200px;
        }

        .form-group label {
            font-weight: bold;
            color: #495057;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .form-control {
            border-radius: 5px;
            box-shadow: none;
        }

        .img-preview {
            margin-top: 20px;
        }

        .text-light {
            color: #343a40 !important;
        }

        .form-title {
            text-align: center;
            font-size: 24px;
            margin-bottom: 30px;
            color: #343a40;
        }

        @media (max-width: 768px) {
            .form-container {
                padding: 20px;
            }

            .form-title {
                font-size: 20px;
            }
        }
        body {
    background-image: url("../Images/mainBG.png");
    background-repeat: no-repeat;
    background-attachment: fixed;
    background-size: 100% 100%;
    font-size: 16px;
}
    </style>
</head>
<body>
<?php include 'adminHeader.php'; ?>
<?php include 'adminSidebar.php'; ?>

<div class="form-container">
    <h1 class="form-title"><?php echo isset($event) ? 'Edit Event' : 'Add Event'; ?></h1>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title" class="text-light">Event Title</label>
            <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($event['title'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="description" class="text-light">Event Description</label>
            <textarea id="description" name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($event['description'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label for="event_image" class="text-light">Event Image</label>
            <input type="file" id="event_image" name="event_image" class="form-control">
            <?php if (isset($event['image_path'])): ?>
                <?php 
                    $image_path = "../Images/" . htmlspecialchars($event['image_path']);
                    echo "<p>Image Path: " . $image_path . "</p>"; // For debugging
                ?>
                <div class="img-preview">
                    <img src="<?php echo $image_path; ?>" alt="Current Event Image" width="150">
                </div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary btn-block"><?php echo isset($event) ? 'Update Event' : 'Add Event'; ?></button>
    </form>

    <!-- Redirect to adminHomepage.php -->
    <button type="button" class="btn btn-secondary btn-block mt-3" onclick="window.location.href='adminHomepage.php';">Back to Homepage</button>
</div>

<!-- Bootstrap JS and Popper.js -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>