<?php
session_start(); // start session

include_once("../../serverhandle/userhandle.php");
include_once("../../serverhandle/carthandle.php");


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
$userId = $_SESSION['user_id'];

$userInfo = user::getUserInfo($userId);

if ($userInfo) {
    // Assign the fetched values to variables
    $userName = $userInfo['user_name'];
    $email = $userInfo['email'];
} else {
    echo "Admin user not found!";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check the action
    $action = $_POST['action'] ?? '';

    if ($action == 'updateInfo') {
        // Get the form data for updating user info
        $info = [
            'username' => $_POST['username'],
            'email' => $_POST['email'],
        ];

        // Update user information
        if (user::updateInfo($userId, $_SESSION['role'], $info)) {
            $_SESSION['successMessage'] = "User information updated successfully!";
        } else {
            $_SESSION['errorMessage'] = "Failed to update user information.";
        }
    }
    if ($action == 'updatePassword') {
        // Get the form data for updating password
        $currentPassword = $_POST['currentPassword'];
        $newPassword = $_POST['newPassword'];
        $confirmPassword = $_POST['confirmPassword'];

        // Validate and update password
        if ($newPassword == $confirmPassword) {
            if (user::updatePassword($userId, $currentPassword, $newPassword)) {
                $_SESSION['successMessage'] = "Password updated successfully!";
            } else {
                $_SESSION['errorMessage'] = "Failed to update password. Please check your current password.";
            }
        } else {
            $_SESSION['errorMessage'] = "New password and confirm password do not match.";
        }
    }
    header("location: adminProfile.php");
    exit;
}

$row = CartHandle::getAllOrders();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="../css/globalStyle.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/profile.css">
    <link rel="stylesheet" href="../css/manageUsers.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-brand">
            <a href="../page/adminPage.php">Wbay</a>
        </div>
        <div class="nav-links">
            <a href="../page/adminPage.php">Home</a>
            <a href="../page/manageUsers.php">Manage Users</a>
            <a href="../page/manageProducts.php">Manage Products</a>
            <div class="user-menu" id="userMenu">
                <button class="user-btn" id="userBtn">
                    <i class="fas fa-user" id="active"></i>
                </button>
                <div class="dropdown-menu" id="dropdownMenu">
                    <a href="../page/AdminProfile.php" id="profileBtn">Profile</a>
                    <!-- Form for Logout -->
                    <form action="adminProfile.php" method="GET">
                        <button class="navbar-btn" type="submit" id="logoutBtn" value="logout" name="action">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Profile Section -->
    <section class="profile-page">
        <div class="profile-container">
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="avatar">
                    <img src="/img/avatar.png" alt="Profile Picture" id="profileImage">
                </div>
                <h2 id="profileName"><?php echo $userName; ?></h2>
                <p id="profileEmail"><?php echo $email; ?></p>
            
                <nav class="profile-nav">
                    <button class="nav-item active" id="personalInfoBtn" data-tab="personal-info">
                        <i class="fas fa-user"></i> Personal Information
                    </button>
                    <button class="nav-item" id="ordersBtn" data-tab="orders">
                        <i class="fas fa-shopping-bag"></i> Site Orders
                    </button>
                    <button class="nav-item" id="securityBtn" data-tab="security">
                        <i class="fas fa-lock"></i> Security
                    </button>
                </nav>
            </div>

            <!-- Content Area -->
            <div class="profile-content">
                <!-- Personal Info tab -->
                <div class="tab-content active" id="personalInfo">
                    <h2>Personal Information</h2>
                    <?php if (isset($successMessage)): ?>
                        <div class="alert success"><?php echo $successMessage; ?></div>    
                    <?php endif; ?>
                    <?php if (isset($errorMessage)): ?>
                        <div class="alert error"><?php echo $errorMessage; ?></div>
                    <?php endif; ?>
                    <form class="profile-form" id="personalInfoForm" method="POST" action="adminProfile.php">
                        <input type="hidden" name="action" value="updateInfo">
                        <div class="form-group">
                            <label for="username">Admin Username</label>
                            <input type="text" id="username" name="username" value="<?php echo $userName; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
                        </div>
                        <button type="submit" class="save-btn">Save Changes</button>
                    </form>
                </div>
            

                <!-- Orders tab -->
                <div class="tab-content" id="orders">
                    <h2>Orders</h2>
                    <div class="orders-list" id="ordersList">
                        <table>
                            <thead>
                                <tr>
                                    <!-- Column headers for user table -->
                                    <th>Vendor ID</th>
                                    <th>CUSTOMER ID</th>
                                    <th>TOTAL</th>
                                    <th>ORDERD ITEMS</th>
                                    <th class="actions">STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    if(!empty($row)){
                                        foreach($row as $orders){
                                            $status_btn = "";
                                            switch($orders["status"]){
                                                case "pending":
                                                    $status_btn = '<button disabled class="status pending"><span></span></button>';
                                                    break;
                                                case "on_the_way":
                                                    $status_btn = '<button disabled class="status on_the_way"><span></span></button>';
                                                    break;
                                                case "delivered":
                                                    $status_btn = '<button disabled class="status delivered"><span></span></button>';
                                                    break;
                                                case "cancelled":
                                                    $status_btn = '<button disabled class="status cancelled"><span></span></button>';
                                                    break;
                                            }
                                            echo '
                                                <tr>
                                                    <form class="order-form" action="profile.php" method="POST">
                                                        <td>'. $orders["vendor_id"] .'</td>
                                                        <td>'. $orders["customer_id"] .'</td>
                                                        <td>'. $orders["total"] .'</td>
                                                        <td>'. $orders["items"] .'</td>
                                                        <td>
                                                        '. $status_btn .'
                                                        </td>
                                                    </form>
                                                </tr>
                                            ';
                                        }
                                    } else {
                                        echo "<p>no orders</p>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Security tab -->
                <div class="tab-content" id="security">
                    <h2>Security Settings</h2>
                    <form class="profile-form" id="passwordForm" method="POST" action="adminprofile.php">
                        <input type="hidden" name="action" value="updatePassword">
                        <div class="form-group">
                            <label for="currentPassword">Current Password</label>
                            <input type="password" id="currentPassword" name="currentPassword" required>
                        </div>
                        <div class="form-group">
                            <label for="newPassword">New Password</label>
                            <input type="password" id="newPassword" name="newPassword" required>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Confirm Password</label>
                            <input type="password" id="confirmPassword" name="confirmPassword" required>
                        </div>
                        <button type="submit" class="save-btn">Update Password</button>
                    </form>
                </div>

            </div>

        </div>
    </section>    

    <script src="../js/ProfileDropDown.js"></script>
    <script src="../js/profileSidebar.js"></script>
</body>
</html>