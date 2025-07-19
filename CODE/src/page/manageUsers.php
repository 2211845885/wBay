<?php
session_start(); // start session

include_once("../../serverhandle/userhandle.php");


// Check if the user is not logged in
if (!user::isLoggedIn()) {
    // Redirect the user to the login page
    header("location: login.php");
    exit;
}

// Handle logout via GET request
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action']) && $_GET['action'] == "logout") {
    user::logout();
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id']; // Get user_id from form
    user::deleteUser($user_id);
}


$users = user::getUsers();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wbay - Your Online Shopping Destination</title>
    <link rel="stylesheet" href="../css/globalStyle.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/manageUsers.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-brand">
            <a href="../page/adminPage.php" aria-label="Home - Wbay">Wbay</a>
        </div>
        <div class="nav-links">
            <a href="../page/adminPage.php">Home</a>
            <a href="../page/manageUsers.php" id="active">Manage Users</a>
            <a href="../page/manageProducts.php">Manage Products</a>
            <div class="user-menu" id="userMenu">
                <button class="user-btn" id="userBtn">
                    <i class="fas fa-user"></i>
                </button>
                <div class="dropdown-menu" id="dropdownMenu">
                    <a href="../page/adminProfile.php" id="profileBtn">Profile</a>
                    <!-- Form for Logout -->
                    <form action="manageUsers.php" method="GET">
                        <button class="navbar-btn" type="submit" id="logoutBtn" value="logout" name="action">Logout</button>
                        </form>
                </div>
            </div>
        </div>
    </nav>

    <div>
        <section class="manage-users">
            <h1>Manage Users</h1>
            <!-- Form for managing users, including actions for Edit and Delete -->
            <div class="form-container">
                <table>
                    <thead>
                        <tr>
                            <!-- Column headers for user table -->
                            <th>UserName</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th class="actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Loop through users to display them in the table
                        foreach ($users as $user) {
                            echo '<tr>';
                            echo '<form action="manageUsers.php" method="POST">';
                            echo '<td>' . ($user['user_name']) . '</td>';
                            echo '<td>' . ($user['email']) . '</td>';
                            echo '<td>' . ($user['password']) . '</td>';
                            echo '<td>';
                            // Add delete button with the user ID as a hidden input
                            echo '<button type="submit" name="delete" class="btn-delete">Delete</button>';
                            echo '<input type="hidden" name="user_id" value="' . $user['id'] . '">';
                            echo '</td>';
                            echo '</form>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>About Us</h3>
                <p>Your one-stop shop for all your needs</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="../page/manageProducts.php">Manage Products</a></li>
                    <li><a href="../page/manageUsers.php">Manage Users</a></li>
                    <li><a href="../page/adminProfile.php">Profile</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact Us</h3>
                <p>Email: support@wbay.com</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>EBay but we W</p>
        </div>
    </footer>
    <script src="../js/ProfileDropDown.js"></script>
</body>
</html>