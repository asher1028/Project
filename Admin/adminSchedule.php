<?php
include 'adminSessionStart.php';

if (!isset($_SESSION['adminUsername'])) {
    header("Location: adminUserLogin.php");
    exit();
}

// Database connection
include '../config/connection.php'; 

// Get the current month and year from the URL parameters, or use the current month/year
$month = isset($_GET['month']) ? intval($_GET['month']) : date('m');
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Query to get the available baptism schedules for the specified month and year
$query = "SELECT id, date, time, status, details FROM available_schedule 
          WHERE MONTH(date) = $month AND YEAR(date) = $year AND details LIKE '%baptism%'";
$result = mysqli_query($conn, $query);

// Fetch the schedules into an array
$schedules = [];
while ($row = mysqli_fetch_assoc($result)) {
    $schedules[$row['date']][] = $row; // Group schedules by date
}

mysqli_close($conn); // Close the database connection

// Function to draw the calendar
function draw_calendar($month, $year, $schedules) {
    // Days of the week
    $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

    // Get today's date
    $today = date('Y-m-d');

    // Calculate the first day of the month
    $firstDay = mktime(0, 0, 0, $month, 1, $year);
    $totalDays = date('t', $firstDay);
    $startingDay = date('w', $firstDay);

    echo '<div class="calendar-header">';
    echo '<h2>Available Schedule for ' . date('F Y', $firstDay) . '</h2>';
    echo '</div>';
    echo '<div class="calendar-days">';

    // Display the days of the week
    foreach ($daysOfWeek as $day) {
        echo "<div class='calendar-day header'>$day</div>";
    }

    // Adjust the starting day
    for ($i = 0; $i < $startingDay; $i++) {
        echo '<div class="calendar-day empty"></div>';
    }

    // Fill in the days of the month
    for ($day = 1; $day <= $totalDays; $day++) {
        $currentDate = "$year-$month-" . str_pad($day, 2, '0', STR_PAD_LEFT);
        $hasSchedule = isset($schedules[$currentDate]);

        // Initialize button variable
        $button = '';

        // Check if there are schedules for the current date
        if ($hasSchedule) {
            // Check if all schedules have the status "scheduled"
            $allScheduled = true;

            foreach ($schedules[$currentDate] as $schedule) {
                if ($schedule['status'] !== 'scheduled') {
                    $allScheduled = false; // There is at least one available schedule
                    break;
                }
            }
        }

        // Check if the current date is today
        $isToday = ($currentDate === $today) ? 'today' : '';

        echo "<div class='calendar-day $isToday " . ($hasSchedule ? 'available' : '') . "'>$day $button";

        // If there are schedules for this date, list them
        if ($hasSchedule) {
            echo '<ul>';
            foreach ($schedules[$currentDate] as $schedule) {
                echo '<li>' . date('h:i A', strtotime($schedule['time'])) . ': ' . htmlspecialchars($schedule['details']) . ' (' . htmlspecialchars($schedule['status']) . ')';
                
                // Only show the delete button if the schedule is in the past
                if (strtotime($currentDate) < strtotime($today)) {
                    echo ' <form method="POST" style="display:inline-block;">';
                    echo '<input type="hidden" name="schedule_id" value="' . $schedule['id'] . '">';
                    echo '<button type="submit" name="delete_schedule" class="btn btn-danger btn-sm">Delete</button>';
                    echo '</form>';
                }
                
                echo '</li>';
            }
            echo '</ul>';
        }
        echo '</div>';

        // Start a new row after Saturday
        if (($day + $startingDay) % 7 == 0) {
            echo '</div><div class="calendar-days">';
        }
    }

    // Fill in the remaining empty cells after the end of the month
    while (($day + $startingDay) <= 42) {
        echo '<div class="calendar-day empty"></div>';
        $day++;
    }

    echo '</div>'; // End of calendar-days div
}
?>

<?php
// Handle delete action for past schedules
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_schedule'])) {
    $scheduleId = intval($_POST['schedule_id']);

    // Database connection again
    include '../config/connection.php';

    // Query to delete the schedule
    $deleteQuery = "DELETE FROM available_schedule WHERE id = $scheduleId";
    if (mysqli_query($conn, $deleteQuery)) {
        header("Location: adminSchedule.php?month=$month&year=$year&delete_success=1");
        exit();
    } else {
        $error = "Failed to delete schedule: " . mysqli_error($conn);
    }

    mysqli_close($conn); // Close the database connection
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Parish of San Juan</title>
    <link rel="stylesheet" href="../User/userBaptismAvailableSchedule.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style>
        #mainDiv {
            margin-top: 30px;
        }
        .today {
            color: red; /* Change text color to red for today's date */
            font-weight: bold; /* Make today's date bold */
            background-color: #ffe6e6; /* Light background to stand out */
        }
        .text-space-between {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include 'adminHeader.php'; ?>
    <div id="mainDiv">
        <?php draw_calendar($month, $year, $schedules); ?>
        <div class="text-space-between">
            <a href="adminSchedule.php?month=<?php echo ($month == 1 ? 12 : $month - 1); ?>&year=<?php echo ($month == 1 ? $year - 1 : $year); ?>" class="btn btn-secondary">Previous</a>
            <a href="adminHomepage.php" class="btn btn-danger">Back</a>
            <a href="adminSchedule.php?month=<?php echo ($month == 12 ? 1 : $month + 1); ?>&year=<?php echo ($month == 12 ? $year + 1 : $year); ?>" class="btn btn-secondary">Next</a>
        </div>
    </div>
</body>
</html>