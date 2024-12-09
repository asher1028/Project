<?php
include 'superAdminSessionStart.php';
?>
<link rel="stylesheet" href="superAdminSidebar1.css">
<div id="tabSelection">
<style>
    #logoSJBPnav {
        width: 250px; /* Set the desired width */
        height: 90px; /* Maintain aspect ratio */
    }
</style>
<div id="logo-container">
    <img src="../Images/logoSJBP.png" alt="Logo SJBP" id="logoSJBPnav">
</div>

    <hr id="tabLine">
    <div id="dashBoardArea">
        <label for="dashBoardArea" id="dashBoardLabel" style="font-size: 20px; color: black; margin-top: 20px; margin-bottom: -10px;">DASHBOARD</label>
        <div id="dashBoardLinks">
            <a href="superAdminHomepage.php" id="homeLink">Home</a>
            <a href="superAdminService.php" id="serviceLink">Service</a>
            <a href="superAdminRecords.php" id="recordsLink">Records</a>
            <a href="superAdminSchedule.php" id="aboutLink">Available Schedule </a>
        </div>
    </div>
    <div id="maintenanceArea">
        <label for="maintenanceArea" id="maintenanceLabel" style="font-size: 20px; color: black; margin-top: 20px; margin-bottom: -10px;">MAINTENANCE</label>
        <div id="maintenanceLinks">
            <a href="superAdminAdmins.php" id="adminsLink">Admins</a>
            <a href="superAdminUsers.php" id="usersLink">Users</a>
            <a href="superAdminArchive.php" id="archiveLink">Archive</a>
        </div>
    </div>
</div>