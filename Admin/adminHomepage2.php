//this is the old adminHomepage.php
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
    text-align: center;
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
            <div class="col-2" style="margin-right : 65px;">
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

                
<div>
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

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
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
                    u.unique_code, 
                    al.username, 
                    al.status, 
                    al.timestamp, 
                    al.appointment_date, 
                    al.appointment_time 
                FROM 
                    activity_log al
                JOIN 
                    user u 
                ON 
                    al.username = u.username
                WHERE 1"; // Start with no filtering

        // Apply filters if they are provided
        if ($year) {
            $sql .= " AND YEAR(al.timestamp) = ?";
        }
        if ($status) {
            $sql .= " AND al.status = ?";
        }
        if ($search) {
            $sql .= " AND (u.unique_code LIKE ? OR u.username LIKE ?)";
        }

        // Add pagination
        $sql .= " ORDER BY al.timestamp DESC LIMIT ?, ?";

        // Prepare the statement
        $stmt = $conn->prepare($sql);

        // Bind the parameters dynamically
        $params = [];
        $types = '';
        if ($year) {
            $params[] = $year;
            $types .= 's';  // string for year
        }
        if ($status) {
            $params[] = $status;
            $types .= 's';  // string for status
        }
        if ($search) {
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
            $types .= 'ss';  // two strings for search
        }
        // Always add offset and limit for pagination
        $params[] = $offset;
        $params[] = $recordsPerPage;
        $types .= 'ii';  // integers for offset and recordsPerPage

        // Bind parameters and execute
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        // Handle query error if any
        if (!$result) {
            die("Query failed: " . $conn->error);
        }

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row['unique_code'] . "</td>
                        <td>" . htmlspecialchars($row['username']) . "</td>
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
<!-- Pagination Controls -->
<div class="pagination">
    <a href="?page=<?php echo ($currentPage > 1) ? $currentPage - 1 : 1; ?>" class="btn btn-primary <?php echo ($currentPage == 1) ? 'disabled' : ''; ?>">Prev</a>
    <a href="?page=<?php echo ($currentPage < $totalPages) ? $currentPage + 1 : $totalPages; ?>" class="btn btn-primary <?php echo ($currentPage == $totalPages) ? 'disabled' : ''; ?>">Next</a>
</div>

    
    <div style="background-color: white; padding: 10px; margin-top: 30px; margin-bottom: 60px;">
    <div class="col-12" style="display: flex; justify-content: center; align-items: center; flex-wrap: wrap;">
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
    var ctx = document.getElementById('activityChart').getContext('2d');
    var activityChart = new Chart(ctx, {
        type: 'pie',  // Changed to pie chart
        data: {
            labels: <?php echo json_encode($statuses); ?>, // Activity types
            datasets: [{
                label: 'Number of Activities',
                data: <?php echo json_encode($counts); ?>, // Activity counts
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',  // Red
                    'rgba(54, 162, 235, 0.7)',  // Blue
                    'rgba(255, 206, 86, 0.7)',  // Yellow
                    'rgba(75, 192, 192, 0.7)',  // Green
                    'rgba(153, 102, 255, 0.7)', // Purple
                    'rgba(255, 159, 64, 0.7)'     // Lime
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',  // Red
                    'rgba(54, 162, 235, 1)',  // Blue
                    'rgba(255, 206, 86, 1)',  // Yellow
                    'rgba(75, 192, 192, 1)',  // Green
                    'rgba(153, 102, 255, 1)', // Purple
                    'rgba(255, 159, 64, 1)'     // Lime
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
    callbacks: {
        label: function(tooltipItem) {
            return tooltipItem.label + ': ' + tooltipItem.raw + ' activities';
        }
    }
}
,
                legend: {
                    position: 'top',
                    labels: {
                        fontSize: 14
                    }
                }
            }
        }
    });
</script>

</body>
</html>

