<?php
include 'adminSessionStart.php';
?>
<link rel="stylesheet" href="adminSidebar.css">
<div id="tabSelection">
<style>
#logoSJBPnav {
    width: 180px; /* Reduced width */

    height: auto; /* Maintain aspect ratio */
}

/* General Button Styling */
.btn-custom {
    background-color: white;
    color: black;
    border: 1px solid #ccc; /* Light gray border */
    text-align: center;
    padding: 8px; /* Smaller padding */
    width: 100%; /* Fit to sidebar width */
    margin-bottom: 5px; /* Reduced spacing between buttons */
    font-size: 14px; /* Smaller font size */
    font-weight: bold;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s, color 0.3s;
}

.btn-custom:hover {
    background-color: #f0f0f0;
    color: #333;
}

/* Scrollable Areas */
#dashBoardArea, #maintenanceArea {
    padding: 5px 0; /* Remove scrollbar and adjust padding */
}

#dashBoardLabel, #maintenanceLabel {
    font-size: 16px; /* Adjusted font size */
    font-weight: bold;
    margin-bottom: 5px; /* Reduced spacing */
}

/* Ensure the layout is clear and not cut off */
#tabSelection {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    width: 200px; /* Fixed sidebar width */
    padding: 10px; /* Add padding for better layout */
    box-sizing: border-box; /* Include padding in width */
}

#tabSelection > div {
    flex: 1; /* Distribute space evenly */
}

/* Adjustments for overall container height */
#tabSelection {
    min-height: 100vh; /* Ensure full view height */
    overflow: hidden; /* Disable scrolling */
}


</style>


<div id="logo-container">
    <img src="../Images/logoSJBP.png" alt="Logo SJBP" id="logoSJBPnav">
</div>

<div id="dashBoardArea">
    <label for="dashBoardArea" id="dashBoardLabel">DASHBOARD</label>
    <div id="dashBoardLinks">
        <a href="adminHomepage.php" id="homeLink" class="btn-custom nav-link">Home</a>
        <a href="adminService.php" id="serviceLink" class="btn-custom nav-link">Service</a>
        <a href="adminRecords.php" id="recordsLink" class="btn-custom nav-link">Records</a>
        <a href="adminSchedule.php" id="aboutLink" class="btn-custom nav-link">Available Schedule</a>
        <a href="adminAddSchedule.php" id="aboutLink" class="btn-custom nav-link">Add Schedule</a>
   
    <label for="maintenanceArea" id="maintenanceLabel">MAINTENANCE</label>
   
        <a href="adminUsers.php" id="usersLink" class="btn-custom nav-link">Users</a>
        <a href="adminArchive.php" id="archiveLink" class="btn-custom nav-link">Archive</a>
        <a href="admin_event.php" id="adminEventLink" class="btn-custom nav-link">Manage Events</a>
    </div>
</div>
</div>
