<?php
include 'adminSessionStart.php';
include '../config/connection.php'; // Make sure this path is correct

if (!isset($_SESSION['adminUsername'])) {
    header("Location: adminLogin.php");
    exit();
}

// Array of report titles
$reportTitles = [
    'baptism' => 'Baptism Activity Log Report',
    'wedding' => 'Wedding Activity Log Report',
    'private_mass' => 'Private Mass Activity Log Report',
    'funeral_mass' => 'Funeral Mass Activity Log Report',
    'all' => 'Full Activity Log Report', // Default title for "all"
];

// Get report type from the GET parameter
$reportType = isset($_GET['status']) ? strtolower(str_replace(' ', '_', $_GET['status'])) : 'all';

// Determine the title to use based on the report type
$title = isset($reportTitles[$reportType]) ? $reportTitles[$reportType] : 'Full Activity Log Report';

// Query to fetch count of activities grouped by status
$sql = "SELECT status, COUNT(*) AS count 
        FROM activity_log 
        WHERE status IN (
            'Baptism Application', 
            'Baptism Certificate Request', 
            'Wedding Application', 
            'Wedding Certificate Request', 
            'Private Mass Appointment', 
            'Funeral Mass Appointment'
        ) 
        GROUP BY status";

$result = $conn->query($sql);

// Handle query error if any
if (!$result) {
    die("Query failed: " . $conn->error);
}

$statuses = [];
$counts = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $statuses[] = $row['status'];
        $counts[] = $row['count'];
    }
} else {
    $statuses = ['No activities found'];
    $counts = [0];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" href="adminHomepage.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Include your existing CSS styles here */
         /* Custom CSS for transparency and responsive design */
         .chart-container {
            background-color: rgba(255, 255, 255, 0.9); /* Transparent white background for the chart */
            border-radius: 10px;
            padding: 10px; /* Reduced padding */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            width: 60%; /* Smaller width */
            margin: 0 auto; /* Center the chart */
        }

        table {
            border-collapse: collapse; /* Ensure borders are collapsed for cleaner lines */
            width: 100%; /* Full width of the container */
            margin-bottom: 20px;
            background-color: rgba(255, 255, 255, 0.9); /* Transparent white background */
            border-radius: 10px; /* Rounded corners */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 8px; /* Adjust padding for readability */
            text-align: center; /* Center-align text */
            font-size: 14px; /* Adjust font size */
            border: 1px solid #ddd; /* Add borders to cells */
        }

        th {
            background-color: #f8f9fa; /* Light gray background for header */
            font-size: 16px; /* Slightly larger font for header */
            border-bottom: 2px solid #ddd; /* Thicker bottom border for header */
        }

        tr:nth-child(even) {
            background-color: #f2f2f2; /* Light gray for even rows */
        }

        tr:hover {
            background-color: rgba(0, 123, 255, 0.1); /* Highlight row on hover */
        }

        .mt-5 {
            margin-top: 30px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 15px;
        }

        h3 {
            font-size: 18px; /* Smaller font size for headers */
        }

        .mt-3 {
            font-size: 14px; /* Smaller font size for the percentage breakdown */
        }
        
        tbody {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 15px;
        }

        .container-fluid {
            max-width: 90%; /* Limit width of content */
            margin: 0 auto;
        }

        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center; /* Center the buttons */
            align-items: center;     /* Vertically center the buttons */
        }

        .pagination a {
            padding: 10px 20px;
            margin: 0 5px;
            text-decoration: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            font-size: 16px;
        }

        .pagination a.disabled {
            background-color: #ddd;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <?php include 'adminHeader.php'; ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-2" style="margin-right: 65px;">
                <?php include 'adminSidebar.php'; ?>
            </div>
            <div class="col-9">
                <h2 class="text-center"><?php echo htmlspecialchars($title); ?></h2>

                <!-- Chart -->
                <div class="mt-5 chart-container">
                    <canvas id="activityChart" width="100" height="1"></canvas>
                    <h3>Activity Distribution by Status</h3>
                    <p class="text-muted">A summary of the different activity statuses in the system.</p>
                    <div class="mt-3">
                        <p>Total Activities: <strong><?php echo array_sum($counts); ?></strong></p>
                        <p>Activity Count Breakdown:</p>
                        <ul>
                            <?php
                            foreach ($counts as $index => $count) {
                                echo "<li><strong>{$statuses[$index]}</strong>: {$count}</li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>

                <!-- Filter Form -->
                <form method="GET" action="" style="margin-top: 30px" name="Activity Logs">
                    <div style="display: flex; gap: 15px; align-items: center;">
                        <label for="year">Year:</label>
                        <select name="year" id="year">
                            <option value="">--Select Year--</option>
                            <option value="2023" <?php echo isset($_GET['year']) && $_GET['year'] == '2023' ? 'selected' : ''; ?>>2023</option>
                            <option value="2024" <?php echo isset($_GET['year']) && $_GET['year'] == '2024' ? 'selected' : ''; ?>>2024</option>
                        </select>

                        <label for="status">Status:</label>
                        <select name="status" id="status">
                            <option value="">--Select Status--</option>
                            <option value="Baptism Application" <?php echo isset($_GET['status']) && $_GET['status'] == 'Baptism Application' ? 'selected' : ''; ?>>Baptism Application</option>
                            <option value="Wedding Application" <?php echo isset($_GET['status']) && $_GET['status'] == 'Wedding Application' ? 'selected' : ''; ?>>Wedding Application</option>
                            <option value="Wedding Certificate Request" <?php echo isset($_GET['status']) && $_GET['status'] == 'Wedding Certificate Request' ? 'selected' : ''; ?>>Wedding Certificate Request</option>
                            <option value="Baptism Certificate Request" <?php echo isset($_GET['status']) && $_GET['status'] == 'Baptism Certificate Request' ? 'selected' : ''; ?>>Baptism Certificate Request</option>
                            <option value="Private Mass Appointment" <?php echo isset($_GET['status']) && $_GET['status'] == 'Private Mass Appointment' ? 'selected' : ''; ?>>Private Mass Appointment</option>
                            <option value="Funeral Mass Appointment" <?php echo isset($_GET['status']) && $_GET['status'] == 'Funeral Mass Appointment' ? 'selected' : ''; ?>>Funeral Mass Appointment</option>
                        </select>

                        <label for="search">Search (Code/Name):</label>
                        <input type="text" name="search" id="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" />

                        <button type="submit" name="filter" class="btn btn-primary">Filter</button>
                    </div>
                </form>

                <!-- Existing table code remains unchanged -->

                <!-- Report Generation Section -->
                <div class="col-12" style="display: flex; justify-content: center; align-items: center; flex-wrap: wrap; margin-top:30px; margin-bottom:60px; background-color: white; padding: 10px;">
                    <h3>GENERATE REPORT</h3>
                    <form method="GET" action="Reports/generateReport.php" style="display: flex; justify-content: center; gap: 10px; align-items: center;">
                        <input type="hidden" name="title" value="<?php echo htmlspecialchars($title); ?>">
                        <label for="year">Year:</label>
                        <select name="year" id="year">
                            <option value="">--Select Year--</option>
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                        </select>
                        <label for="status">Status:</label>
                        <select name="status" id="status">
                            <option value="">--Select Status--</option>
                            <option value="Baptism Application">Baptism Application</option>
                            <option value="Wedding Application">Wedding Application</option>
                            <option value="Wedding Certificate Request">Wedding Certificate Request</option>
                            <option value="Baptism Certificate Request">Baptism Certificate Request</option>
                            <option value="Private Mass Appointment">Private Mass Appointment</option>
                            <option value="Funeral Mass Appointment">Funeral Mass Appointment</option>
                        </select>
                        <label for="date">Date:</label>
                        <input type="date" name="date" id="date">
                        <button type="submit" name="filter">DOWNLOAD FILE</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        const statuses = <?php echo json_encode($statuses); ?>;
        const counts = <?php echo json_encode($counts); ?>;

        const ctx = document.getElementById('activityChart').getContext('2d');
        const activityChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: statuses,
                datasets: [{
                    data: counts,
                    backgroundColor: ['#FF5733', '#33FF57', '#3357FF', '#F5A623', '#FF1493', '#FF6347'],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: { callbacks: { label: tooltipItem => tooltipItem.label + ': ' + tooltipItem.raw } }
                }
            }
        });
    </script>
</body>
</html>



, baptism_applications ba, baptism_requests br, funeral_applications fr, mass_applications ma, wedding_applications wa, wedding_requests wr