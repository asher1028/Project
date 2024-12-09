<?php 
include 'adminSessionStart.php';
include '../config/connection.php';
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Query to get the profile picture
    $query = "SELECT profile_pic FROM user WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($profile_picture);
    $stmt->fetch();

    // Check if the user has a profile picture
    if ($profile_picture) {
        $profilePicPath = "../uploads/" . $profile_picture; // Assuming pictures are stored in 'uploads' folder
    } else {
        $profilePicPath = "../Images/profileIcon.png"; // Default image
    }
}
?>
<header class="w-100">
    <link rel="stylesheet" href="adminHeader2.css">
    <style>
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }
        .profile-picture {
            max-width: 50px;
            margin-right: -260px;
            border-radius: 50%;
            z-index: 10;
            position: relative;
        }
        #logoSJBPnav {
            max-width: 150px; /* Adjust logo size */
            height: auto; /* Maintain aspect ratio */
            margin-left: -10px; /* Adjust margin if needed */
        }
    </style>

    <div class="containernav">
        <nav class="navbar navbar-expand-lg d-flex justify-content-center">
            <!-- Logo on the left -->
            <div id="logo-container"
                <a href="userLandingpage.php">
                    <img src="../Images/logoSJBP.png" alt="Logo SJBP" id="logoSJBPnav">
                </a>
            </div>

            <!-- Navigation links in the center -->
            <div id="links" class="d-flex">
                <a href="userLandingpage.php" id="homeLink" class="mx-5">Home</a>
                <a href="userAbout.php" class="mx-5">About</a>
                <a href="userContactUs.php" id="contactLink" class="mx-5">Contact Us</a>
            </div>

            <?php if (isset($_SESSION['username'])): ?>
                <!-- If logged in, show profile picture -->
                <img src="<?php echo $profilePicPath; ?>" class="profile-picture" alt="Profile Picture">
                <a href="userAccountSettings.php" class="white mx-5"><?php echo htmlspecialchars($_SESSION['username']); ?></a>
                <a href="#" onclick="confirmLogout(event)" class="white mx-5">Logout</a>
            <?php else: ?>
                <!-- If not logged in, show Login link -->
                <a href="userLogin.php" class="white mx-5">Login</a>
            <?php endif; ?>
        </nav>
    </div>
</header>


<script>
    function confirmLogout(event) {
        event.preventDefault();
        if (confirm("Are you sure you want to logout?")) {
            window.location.href = "userLogout.php";
        }
    }
        // Attach login alert to restricted links
        document.querySelector("a[href='userBaptismCertificateRequest.php']")?.addEventListener("click", alertLoginRequired);
        document.querySelector("a[href='userWeddingCertificateRequest.php']")?.addEventListener("click", alertLoginRequired);
        document.querySelector("a[href='userBaptismSchedule.php']")?.addEventListener("click", alertLoginRequired);
        document.querySelector("a[href='userWeddingSchedule.php']")?.addEventListener("click", alertLoginRequired);
        document.querySelector("a[href='userBaptismOwnRecords.php']")?.addEventListener("click", alertLoginRequired);
        document.querySelector("a[href='userWeddingOwnRecords.php']")?.addEventListener("click", alertLoginRequired);
        document.querySelector("a[href='userFuneralOwnRecords.php']")?.addEventListener("click", alertLoginRequired);
        document.querySelector("a[href='userPrivateMassOwnRecords.php']")?.addEventListener("click", alertLoginRequired);
        document.querySelector("a[href='comments.php']")?.addEventListener("click", alertLoginRequired);

    
</script>