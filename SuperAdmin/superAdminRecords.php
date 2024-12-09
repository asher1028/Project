<?php
include 'superAdminSessionStart.php';

if (!isset($_SESSION['superAdminUsername'])) {
    header("Location: superAdminLogin.php");
    exit();
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Parish of San Juan</title>
        <link rel="stylesheet" href="superAdminHomepage1.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    </head>
    <body>
        <?php include 'superAdminHeader.php'; ?>
        <div class='container-fluid'>
        <div class='row'>
            <div class='col-2'>
                <?php include 'superAdminSidebar.php'; ?>
            </div>
            <div class='col-9'>
                 <h2 class='mx-5 mt-5 white'>Service</h2>
                <div id="mainPage">     
                    <a href="superAdminBaptismRecords.php" id="serviceLink" class="bg">Baptism</a>
                    <a href="superAdminWeddingRecords.php" id="scheduleLink" class="bg">Wedding</a>
                </div>
            </div>
        </div>
        </div>
    </body>
</html>