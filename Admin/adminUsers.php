
<?php
include 'adminSessionStart.php';
include '../config/connection.php'; // Make sure to include the database connection

if (!isset($_SESSION['adminUsername'])) {
    header("Location: adminLogin.php");
    exit();
}

// Set the number of records per page
$records_per_page = 10;

// Get the current page number from the URL, default to 1 if not set
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $records_per_page;

// Fetch records from the baptism_applications table with pagination, including is_restricted field
$query = "SELECT id, CONCAT(first_name, ' ', IFNULL(NULLIF(middle_name, ''), ''), ' ', last_name) AS full_name, email, username, contact_num, created_at, is_restricted 
          FROM user  
          LIMIT $records_per_page OFFSET $offset";

$result = $conn->query($query);

// Get the total number of 'pending' records to calculate total pages
$total_query = "SELECT COUNT(*) AS total FROM user";
$total_result = $conn->query($total_query);
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $records_per_page);

// Toggle the is_restricted status when the button is clicked
if (isset($_POST['toggle_restriction'])) {
    $user_id = $_POST['user_id'];
    $current_status = $_POST['current_status'];
    $new_status = $current_status == 0 ? 1 : 0; // Toggle between 0 and 1

    // Update the database
    $update_query = "UPDATE user SET is_restricted = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ii", $new_status, $user_id);
    $stmt->execute();

    // Redirect to refresh the page
    header("Location: adminUsers.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parish of San Juan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style>
        #disableButtons {
            background-color: gray;
            border: none;
            color: white;
        }

        #disableButtons:hover {
            background-color: rgb(255, 0, 0); /* Red when Disabled */
        }

        #enableButtons {
            background-color: gray;
            border: none;
            color: white;
        }

        #enableButtons:hover {
            background-color: rgb(0, 128, 0); /* Green when Enabled */
        }

        body {
            background-image: url("../Images/mainBG.png");
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: 100% 100%;
            font-size: 16px;
        }
        .bg:nth-child(odd) {
            background: linear-gradient(to bottom, rgb(255, 0, 0), rgb(125, 0, 0));
            opacity: 70%;
        }
        .bg:nth-child(even) {
            background: #fff;
            opacity: 70%;
            color: black;
        }
        .bg {
            height: 100px;
            font-size: 3rem;
        }
        #mainPage {
            max-width: 100%;
            text-align: center;
            width: 100%;
            padding: 20px;
            margin: 0 auto 0 auto;
        }
        a {
            display: block;
            margin: 10px 0;
        }
        h2 {
            color: white;
        }
        a {
            text-decoration: underline;
        }
        #logoutLink {
            margin-left: 90%;
            font-size: 30px;
        }
        #viewButtons {
            color: white;
            width: 75px;
            border: none;
            background-color: gray;
        }
        #messageButtons {
            color: white;
            width: 75px;
            border: none;
            background-color: gray;
        }

        #viewButtons:hover {
            background-color: rgb(0, 150, 255);
        }

        #messageButtons:hover {
            background-color: rgb(0, 150, 255);
        }

        #editButton {
            color: white;
            padding: 5px;
            background-color: gray;
        }

        #editButton:hover {
            background-color: rgb(0, 150, 255);
        }

        #userReqInfo {
            padding: 10px;
            background: rgba(255, 255, 255, 0.7);
            font-size: 35px;
        }

        #reqInfoLabel {
            font-size: 25px;
        }

        #applicantInfos, #weddingApplicantInfos {
            text-align: center;
            align-items: center;
            justify-content: space-evenly;
        }

        .applicantData, .weddingApplicantData {
            margin-top: 20px;
            font-size: 30px;
            font-weight: bold;
        }

        .applicantDataLabel {
            margin-top: -20px;
            font-size: 20px;
        }

        .weddingApplicantDataLabel {
            margin-top: -20px;
            font-size: 20px;
            margin-bottom: 45px;
        }

        #backButton {
            width: 80px;
            height: 30px;
            border: none;
            background-color: gray;
            color: white;
            font-size: 15px;
        }
        #archiveButton, #backButton, #sendButton {
            border: none;
            background-color: gray;
            color: white;
            font-size: 15px;
        }

        #sendButton, #archiveButton {
            width: 80px;
            height: 30px;
            margin: 0 20px;
        }

        #sendButton {
            margin-right: 0;
        }

        #sendButton:hover {
            background-color:green;
        }

        #archiveButton:hover {
            background-color:darkred;
        }

        #forButtons {
            display: flex;
            justify-content: space-between;
            align-items: center; /* Ensure vertical alignment */
            margin-top: 20px;
        }

        #leftButtons, #rightButtons {
            display: flex;
            align-items: center; /* Ensure vertical alignment */
        }

        #rightButtons button {
            margin-left: 10px; /* Add some space between the archive and send buttons */
        }

        #backButton:hover {
            background-color: rgb(0, 150, 255);
        }

        .action-buttons {
            display: flex;
            gap: 10px; /* Adds space between the buttons */
            white-space: nowrap; /* Ensures the buttons stay in one line */
        }
        #weddingApplicationLabel, #baptismApplicationLabel {
            color: white;
        }
        #bottomSide { 
            max-height: 100px;
            overflow-y: scroll;
            overflow-x: hidden;
            border: 2px solid black;
            padding: 0 40px 0 40px;
            margin: 0 200px 0 200px;
            align-items: center;               /* Center horizontally */
            justify-content: center;           /* Center vertically */
            text-align: center;                /* Center text within */
        }
        .funeralApplicantData {
            margin-top: 20px;
            font-size: 20px;
        }

        .funeralApplicantDataLabel {
            margin-top: -20px;
            font-size: 20px;
        }

        #commentsLabel {
            margin-top: 30px;
            align-items: center;
            text-align: center;
        }

        #bottomSide1 {
            align-items: center;               /* Center horizontally */
            justify-content: center;           /* Center vertically */
            text-align: center;                /* Center text within */
        }
        #sidebar {
            margin-right: 100px;
        }

        #btnMoreInfo {
            border: none;
            background-color: gray;
            color: white;
        }
        
        #btnMoreInfo:hover {
            background-color: rgb(0, 150, 255);
            color: white;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            align-items: center;
        }

    </style>
</head>
<body>
    <?php include 'adminHeader.php'; ?>
    <div class='container-fluid'>
        <div class='row'>
            <div class='col-2'>
                <?php include 'adminSidebar.php'; ?>
            </div>
            <div class='col-9'>
                <h2 class='mx-5 mt-5 white'>Users</h2>
                <div id="mainPage">
                    <div class="table-container bg-light p-5 ">
                        <div class="d-flex justify-content-between">
                            <div class='d-flex'>

                            </div>
                            <form class="d-flex" role="search">
                                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                                <button class="btn btn-outline-success" type="submit">Search</button>
                            </form>
                        </div>
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">USER ID.</th>
                                    <th scope="col">FULL NAME</th>
                                    <th scope="col">EMAIL</th>
                                    <th scope="col">USERNAME</th>
                                    <th scope="col">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody class="table-group-divider">
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <th scope="row"><?php echo $row['id']; ?></th>
                                            <td><?php echo $row['full_name']; ?></td>
                                            <td><?php echo $row['email']; ?></td>
                                            <td><?php echo $row['username']; ?></td>
                                            <td class="action-buttons text-center">
                                                <!-- Form to toggle restriction status -->
                                                <button onclick="window.location.href='adminUserMoreInfo.php?id=<?php echo $row['id']; ?>'" id="viewButtons" style="width: 90px;">More Info.</button>
                                                <form method="POST" action="" style="display: inline;">
                                                    <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                                    <input type="hidden" name="current_status" value="<?php echo $row['is_restricted']; ?>">
                                                    <button type="submit" name="toggle_restriction" class='mx-2' 
                                                            id="<?php echo $row['is_restricted'] == 1 ? 'enableButtons' : 'disableButtons'; ?>">
                                                        <?php echo $row['is_restricted'] == 1 ? 'Enable' : 'Disable'; ?>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No records found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- Pagination controls -->
                        <nav aria-label="Page navigation" class="forCentering justify-content-center">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?php if($current_page <= 1){ echo 'disabled'; } ?>">
                                    <a class="page-link" href="?page=<?php echo $current_page - 1; ?>">Previous</a>
                                </li>
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?php if($current_page == $i){ echo 'active'; } ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?php if($current_page >= $total_pages){ echo 'disabled'; } ?>">
                                    <a class="page-link" href="?page=<?php echo $current_page + 1; ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
