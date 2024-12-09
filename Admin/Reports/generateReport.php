<?php
// Include FPDF library
require_once('../fpdf/fpdf.php');

// Include your database connection
include '../../config/connection.php';

// Get Year, Status, Date, and Report Type from the URL parameters
$year = isset($_GET['year']) ? $_GET['year'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';
$reportType = isset($_GET['status']) ? $_GET['status'] : 'all'; // Default to "all"

// Map specific report types to custom titles
$reportTitles = [
    'Baptism Application' => 'Baptism Activity Log Report',
    'Wedding Application' => 'Wedding Activity Log Report',
    'Wedding Requests' => 'Wedding Requests Log Report',
    'Wedding Certificate Request' => 'Wedding Requests Log Report',
    'Private Mass Appointment' => 'Private Mass Activity Log Report',
    'Funeral Mass Appointment' => 'Funeral Mass Activity Log Report',
    'all' => 'Full Activity Log Report',
];

// Sanitize input if it's from user input
$reportType = htmlspecialchars($reportType, ENT_QUOTES, 'UTF-8');

// Check if $reportType is in $reportTitles; use 'all' as a fallback
$title = $reportTitles[$reportType] ?? $reportTitles['all'];

// Build the SQL query dynamically
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
                WHEN al.status IN ('Wedding Requests', 'Wedding Certificate Request') THEN CONCAT(wr.groom_name, ' and ', wr.bride_name)
                WHEN al.status = 'Baptism Request' THEN br.child_name
                WHEN al.status = 'Successful Login' THEN u.username
                WHEN al.status = 'Failed Login' THEN u.username
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
            wedding_requests wr ON al.status IN ('Wedding Certificate Request', 'Wedding Requests') AND al.appointment_date = wr.wedding_date
        WHERE 1";
$name = "";
if($status==='Wedding Certificate Request' || $status=== 'Wedding Requests'){
    $name = "Groom's and Bride's Name";
}
else if($status==='Wedding Application'){
    $name = "Groom's and Bride's Name";
}
else if($status==='Baptism Application'){
    $name = "Child's Name";
}
else if($status==='Funeral Mass Appointment'){
    $name = "Decease's Name";
}
else if($status==='Private Mass Appointment'){
    $name = "Requestor's Name";
}
else if($status==='Baptism Certificate Request'){
    $name = "Child's Name";
}
else{
    $name= "Name/Status";
}


// Add filters if provided
$params = [];
if (!empty($year)) {
    $sql .= " AND YEAR(al.timestamp) = ?";
    $params[] = $year;
}
if (!empty($status)) {
    $sql .= " AND al.status = ?";
    $params[] = $status;
}
if (!empty($date)) {
    $sql .= " AND DATE(al.timestamp) = ?";
    $params[] = $date;
}

// Prepare and execute the query
$stmt = $conn->prepare($sql);
if ($stmt) {
    // Dynamically bind parameters
    if (!empty($params)) {
        $types = str_repeat('s', count($params)); // All parameters are treated as strings
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $reportData = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $reportData[] = $row;
        }
    }

    // Check if data exists to generate a report
    if (!empty($reportData)) {
        // Create a new PDF document
        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->SetMargins(10, 10, 10); // Left, Top, Right margin
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        // Add dynamic title
        $pdf->Cell(0, 10, $title, 0, 1, 'C');
        $pdf->Ln(10);

        // Set table headers
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(35, 10, 'ID', 1, 0, 'C');
        $pdf->Cell(80, 10, $name, 1, 0, 'C');
        $pdf->Cell(55, 10, 'Status', 1, 0, 'C');
        $pdf->Cell(35, 10, 'Timestamp', 1, 0, 'C');
        $pdf->Cell(30, 10, 'App. Date', 1, 0, 'C');
        $pdf->Cell(30, 10, 'App. Time', 1, 1, 'C');

        // Add data rows
        // Add data rows
foreach ($reportData as $row) {
    $pdf->SetFont('Arial', '', 10); // Smaller font for details
    $pdf->Cell(35, 10, $row['id'], 1, 0, 'C');
    $name = isset($row['name']) ? htmlspecialchars($row['name']) : 'No Name'; // Ensure 'name' exists
    $pdf->Cell(80, 10, $name, 1, 0, 'C');  // Using the fetched name
    $pdf->Cell(55, 10, htmlspecialchars($row['status']), 1, 0, 'C');
    $pdf->Cell(35, 10, $row['timestamp'], 1, 0, 'C');
    $pdf->Cell(30, 10, ($row['appointment_date'] ?: 'N/A'), 1, 0, 'C');
    $pdf->Cell(30, 10, ($row['appointment_time'] ?: 'N/A'), 1, 1, 'C');
}


        // Output PDF
        $pdf->Output('D', 'Activity Log Report.pdf');
        exit();
    } else {
        echo "No activity log data available for the selected filters.";
    }
} else {
    echo "Error preparing the query: " . $conn->error;
}
?>


    