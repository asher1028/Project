<?php
include 'adminSessionStart.php';
include '../config/connection.php'; // Make sure this path is correct

if (!isset($_SESSION['adminUsername'])) {
    header("Location: adminLogin.php");
    exit();
}

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
    <title>Parish of San Juan</title>
    <link rel="stylesheet" href="adminHomepage.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
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
/* Add a container to make the table scrollable */
.table-striped {
    width: 100%; /* Full-width table */
    border-collapse: collapse;
}

.table-striped th, .table-striped td {
    padding: 8px;
    text-align: left;
    border: 1px solid #ddd; /* Add borders for clarity */
}

.table-container {
    max-height: 400px; /* Adjust the height as needed */
    overflow-y: auto; /* Enable vertical scrolling */
    border: 1px solid #ccc; /* Add border around the table */
    margin-top: 20px; /* Add spacing from other content */
}

/* Optional: Add alternating row colors */
.table-striped tr:nth-child(even) {
    background-color: #f9f9f9;
}

.table-striped tr:hover {
    background-color: #f1f1f1; /* Highlight on hover */
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
                <!-- Activity Log Table -->

                <!-- Graph for Activity Log (Pie Chart) -->
                <div class="mt-5 chart-container">
                    <canvas id="activityChart" width="100" height="1"></canvas> <!-- Reduced size -->
                    <h3>Activity Distribution by Status</h3>
                    <p class="text-muted">A summary of the different activity statuses in the system.</p>
                    <div class="mt-3">
                        <p>Total Activities: <strong><?php echo array_sum($counts); ?></strong></p>
                        
                        <p>Activity Count Breakdown:</p>
                        <ul>
                            <?php
                            // Display count breakdown for each activity type
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
<div class="table-container">
<table class="table table-striped">
<thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Status</th>
            <th>Timestamp</th>
            <th>Appointment Date</th>
            <th>Appointment Time</th>
        </tr>
    </thead>
    <tbody>
        <?php
         // Set the number of records per page
        $recordsPerPage = 10;

        // Get the current page from the URL, default to 1 if not set
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($currentPage - 1) * $recordsPerPage;

        // Initialize filter values
        $year = isset($_GET['year']) ? $_GET['year'] : '';
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        $search = isset($_GET['search']) ? $_GET['search'] : '';

        // Start building the SQL query
        $sql = "SELECT 
        u.id, 
        al.username, 
        al.status, 
        al.timestamp, 
        al.appointment_date, 
        al.appointment_time, 
    CASE
        WHEN al.status = 'Baptism Application' THEN ba.child_name
        WHEN al.status = 'Baptism Certificate Request' THEN br.child_name
        WHEN al.status = 'Funeral Mass Appointment' THEN fa.deceased_name
        WHEN al.status = 'Private Mass Appointment' THEN ma.requester_name
        WHEN al.status = 'Wedding Application' THEN CONCAT(wa.groom_name, ' & ', wa.bride_name)
        WHEN al.status IN ('Wedding Requests', 'Wedding Certificate Request') THEN CONCAT(wr.groom_name, ' & ', wr.bride_name)
        WHEN al.status = 'Baptism Request' THEN br.child_name
        WHEN al.status IN ('Successful Login', 'Failed Login') THEN u.username
        ELSE NULL
    END AS name

    FROM 
        activity_log al
    JOIN 
        user u ON al.username = u.username
    LEFT JOIN 
        baptism_applications ba ON al.status = 'Baptism Application' AND al.appointment_date = ba.date_baptized
    LEFT JOIN 
        baptism_requests br ON al.status = 'Baptism Certificate Request' AND al.appointment_date = br.baptism_date
    LEFT JOIN 
        funeral_applications fa ON al.status = 'Funeral Mass Appointment' AND al.appointment_date = fa.date_of_mass
    LEFT JOIN 
        mass_applications ma ON al.status = 'Private Mass Appointment' AND al.appointment_date = ma.date_of_mass
    LEFT JOIN 
        wedding_applications wa ON al.status = 'Wedding Application' AND al.appointment_date = wa.date_married
    LEFT JOIN 
        wedding_requests wr ON al.status = 'Wedding Certificate Request' AND al.appointment_date = wr.wedding_date
    WHERE 1";

$params = [];   
$types = '';

        // Apply filters if they are provided
      // Add year filter if provided
if ($year) {
    $sql .= " AND YEAR(al.timestamp) = ?";
    $params[] = $year;
    $types .= 's';  // Assuming 's' for string
}

// Add status filter if provided
if ($status) {
    $sql .= " AND al.status = ?";
    $params[] = $status;
    $types .= 's';  // Assuming 's' for string
}

// Add search filter if provided
if ($search) {
    $sql .= " AND (u.id LIKE ? OR u.username LIKE ?)";
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
    $types .= 'ss';  // Assuming two strings for the LIKE search
}

// Add offset and limit for pagination
$sql .= " ORDER BY al.timestamp DESC LIMIT ?, ?";
$params[] = $offset;
$params[] = $recordsPerPage;
$types .= 'ii';  // integers for offset and recordsPerPage


// Prepare the statement and bind the parameters
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}

$stmt->bind_param($types, ...$params);  // Bind all parameters dynamically

        $stmt->execute();
        $result = $stmt->get_result();
        
        // Handle query error if any
        if (!$result) {
            die("Query failed: " . $conn->error);
        }
    
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row['id'] . "</td>
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>" . htmlspecialchars($row['status']) . "</td>
                        <td>" . $row['timestamp'] . "</td>
                        <td>" . ($row['appointment_date'] ? $row['appointment_date'] : 'N/A') . "</td>
                        <td>" . ($row['appointment_time'] ? $row['appointment_time'] : 'N/A') . "</td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No activity logs available for the selected filters.</td></tr>";
        }
        
        // Get the total number of records to calculate the number of pages
$totalRecordsQuery = "SELECT COUNT(*) as total FROM activity_log al
JOIN user u ON al.username = u.username
WHERE 1";

// Add conditions to the query based on filters
if ($year) {
$totalRecordsQuery .= " AND YEAR(al.timestamp) = ?";
}
if ($status) {
$totalRecordsQuery .= " AND al.status = ?";
}
if ($search) {
$totalRecordsQuery .= " AND (u.unique_code LIKE ? OR u.username LIKE ?)";
}

// Prepare and bind total records query
$totalStmt = $conn->prepare($totalRecordsQuery);
$totalParams = [];
$totalTypes = '';

// Bind parameters if filters are applied
if ($year) {
$totalParams[] = $year;
$totalTypes .= 's';  // for year
}
if ($status) {
$totalParams[] = $status;
$totalTypes .= 's';  // for status
}
if ($search) {
$totalParams[] = '%' . $search . '%';
$totalParams[] = '%' . $search . '%';
$totalTypes .= 'ss';  // for search
}

// If there are parameters, bind them
if (!empty($totalTypes)) {
$totalStmt->bind_param($totalTypes, ...$totalParams);
}

// Execute the query
$totalStmt->execute();
$totalRecordsResult = $totalStmt->get_result();
$totalRecords = $totalRecordsResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

        ?>
    </tbody>
</table>
</div>

<div class="col-12" style="display: flex; justify-content: center; align-items: center; flex-wrap: wrap; margin-top:30px; margin-bottom:60px; background-color: white; padding: 10px;">
    <h3>GENERATE REPORT</h3>
    <form method="GET" action="Reports/generateReport.php" style="display: flex; justify-content: center; gap: 10px; align-items: center;">
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
    </div>
    </div>
    <script>
        // Data for the chart
        const statuses = <?php echo json_encode($statuses); ?>;
        const counts = <?php echo json_encode($counts); ?>;

        // Set up the chart
        const ctx = document.getElementById('activityChart').getContext('2d');
        const activityChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: statuses,
                datasets: [{
                    data: counts,
                    backgroundColor: ['#FF5733', '#33FF57', '#3357FF', '#F5A623', '#FF1493', '#FF6347'], // Add colors for each section
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw;
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
