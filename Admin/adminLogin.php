<?php
include 'adminSessionStart.php';
include '../config/connection.php'; // Your existing database connection

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables to retain input
$input = '';
$error = '';

// Function to verify login credentials
function verifyLogin($conn, $input, $password) {
    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT username, password, is_restricted FROM admin WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $input, $input); // Bind input to the prepared statement
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $username = $row['username']; // Retrieve the username from the database
        $hashedPassword = $row['password']; // Retrieve the hashed password from the database
        $isRestricted = $row['is_restricted']; // Retrieve the 'is_restricted' status

        // Check if the account is restricted
        if ($isRestricted == 1) {
            return 'Account is restricted.'; // Account is restricted
        }

        // Verify the entered password against the hashed password
        if (password_verify($password, $hashedPassword)) {
            return $username; // Return the username if login is successful
        }
    }
    return false; // No match found or incorrect password
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = $_POST['adminLogin']; // Username, email, or contact number
    $password = $_POST['adminPassword']; // User password

    $username = verifyLogin($conn, $input, $password);
    if ($username === false) {
        $error = 'Incorrect Username, Email, or Password!';
    } elseif ($username === 'Account is restricted.') {
        $error = $username; // Display the restriction message
    } else {
        $_SESSION['adminUsername'] = $username; // Store the username in session
        header("Location: adminHomepage.php"); // Redirect to the landing page
        exit();
    }
}

if (isset($_SESSION['adminUsername'])) {
    header("Location: adminHomepage.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Parish of San Juan</title>
        <link rel="stylesheet" href="adminLogin.css">
        <style>
            .diffUser {
                font-weight: lighter;
                font-style: italic;
                color: rgb(10, 10, 100);
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <div id="loginDiv">
            <form id="loginForm" method="POST" action="">
                <img src="../Images/logoSJBP.png" id="logoSJBP">
                <br>
                <label for="loginForm" id="loginFormLabel">PLEASE LOGIN</label>
                <br>
                <input type="text" name="adminLogin" placeholder="Username or Email" id="txtUser" required>
                <br>
                <input type="password" name="adminPassword" placeholder="Password" id="txtPass" required>
                <br>
                <input type="submit" value="Log in" id="btnLogin">
                <br>
                <a href="../SuperAdmin/superAdminLogin.php" class="diffUser">Super Admin Login</a>
                <br>
                <a href="../User/userLogin.php" class="diffUser">User Login</a>
            </form>
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
        </div>
    </body>
</html>
