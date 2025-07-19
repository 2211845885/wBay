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
    $role = $userInfo['role'];
    $phone = $userInfo['phone_number'] ?? '';
    $address = $userInfo['customer_address'] ?? '';
} else {
    echo "User not found!";
    exit;
}
$cartCount = CartHandle::amountInCart();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check the action
    $action = $_POST['action'] ?? '';

    if ($action == 'updateInfo') {
        // Get the form data for updating user info
        $info = [
            'username' => $_POST['username'],
            'email' => $_POST['email'],
            'address' => $_POST['address'] ?? null,
            'phone' => $_POST['phone'] ?? null,
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
    if ($action == "update-order") {
        CartHandle::updateCustomerOrder($_POST["vendId"], $_POST["state"], $_POST["time"]);
    }
    header("location: profile.php");
    exit;
}

$row = CartHandle::orders();

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
            <a href="../page/customerPage.php">Wbay</a>
        </div>
        <div class="nav-links">
            <!-- WBay Needs to be a text not a link -->
            <a href="../page/customerPage.php">Home</a>
            <a href="../page/products.php">Products</a>
            <div class="cart-icon">
                <a href="../page/cart.php">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count" id="cartCount"><?php echo $cartCount ?></span>
                </a>
            </div>
            <div class="user-menu" id="userMenu">
                <button class="user-btn" id="userBtn">
                    <i class="fas fa-user" id="active"></i>
                </button>
                <div class="dropdown-menu" id="dropdownMenu">
                    <a href="../page/profile.php" id="profileBtn">Profile</a>
                     <!-- Form for Logout -->
                     <form action="../page/login.php" method="GET">
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
                        <i class="fas fa-shopping-bag"></i> My Orders
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
                    <form class="profile-form" id="personalInfoForm" method="POST" action="profile.php">
                        <input type="hidden" name="action" value="updateInfo">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" value="<?php echo $userName; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo $phone; ?>">
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" id="address" name="address" value="<?php echo $address; ?>" required>
                        </div>
                        <button type="submit" class="save-btn">Save Changes</button>
                    </form>
                </div>
            

                <!-- Orders tab -->
                <div class="tab-content" id="orders">
                    <h2>Orders</h2>
                    <?php ?>
                    <div class="orders-list" id="ordersList">
                        <table>
                            <thead>
                                <tr>
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
                                            $status_txt = "";
                                            switch($orders["status"]){
                                                case "pending":
                                                    $status_btn = "pending";
                                                    $status_txt = "Pending";
                                                    break;
                                                case "on_the_way":
                                                    $status_btn = "on_the_way";
                                                    $status_txt = "On The Way";
                                                    break;
                                                case "delivered":
                                                    $status_btn = "delivered";
                                                    $status_txt = "Delivered";
                                                    break;
                                                case "cancelled":
                                                    $status_btn = "cancelled";
                                                    $status_txt = "cancelled";
                                                    break;
                                            }
                                            echo '
                                                <tr>
                                                    <form action="profile.php" method="post">
                                                        <td>'. $orders["total"] .'</td>
                                                        <td>'. $orders["items"] .'</td>
                                                        <td>
                                                        <button type="submit" name="state" value="'.$orders["status"].'" class="btn-status '. $orders["status"] .'"><span></span></button>
    
                                                        <input type="hidden" name="action" value="update-order">
                                                            <input type="hidden" name="vendId" value="'. $orders["vendor_id"] .'">
                                                        <input type="hidden" name="time" value="'. $orders["time"] .'">
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
                    <form class="profile-form" id="passwordForm" method="POST" action="profile.php">
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