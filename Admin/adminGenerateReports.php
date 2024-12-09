<?php
// Include database connection
include '../config/connection.php';

// Define the file name and path for saving the report
$filename = '../reports/activity_log_report_' . date('Y-m-d') . '.csv';

// Open the file for writing
$file = fopen($filename, 'w');

// Check if the file was opened successfully
if ($file === false) {
    die("Error opening file for writing.");
}

// Set the column headers for the CSV file
$headers = ['ID', 'Username', 'Status', 'Timestamp', 'Appointment Date', 'Appointment Time'];
fputcsv($file, $headers);

// Fetch all the records from the activity_log table
$sql = "SELECT id, username, status, timestamp, appointment_date, appointment_time FROM activity_log";
$result = $conn->query($sql);

// Write the rows to the CSV file
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($file, $row);
    }
} else {
    echo "No records found for today.";
}

// Close the file after writing
fclose($file);

// Optionally, provide a link to download the report (if needed)
echo "Activity log report generated successfully. <a href='../Reports/activity_log_report_" . date('Y-m-d') . ".csv'>Download Report</a>";
?>
