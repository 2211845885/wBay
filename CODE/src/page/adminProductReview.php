<?php
session_start(); // start session

include_once("../../serverhandle/userhandle.php");
include_once("../../serverhandle/ratinghandle.php");

include_once("../../serverhandle/productListing.php");


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

if (isset($_GET['id'])) {
    $productId = intval($_GET['id']);

    $row = prodlist::prodDesc($productId); 
    $vendorName = user::getVendorName($row["vendor_id"]); 
} else {
    header("Location: manageProducts.php");
}

$rating = ratinghandle::getRating($productId);

?>

<!DOCTYPE html>
<html lang="en">
<head>
 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Review</title>
    <link rel="stylesheet" href="../css/stars.css">
    <link rel="stylesheet" href="../css/globalStyle.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/productReview.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    
    <!-- Admin nav bar -->
    <nav class="navbar">
        <div class="nav-brand">
            <a href="../page/adminPage.html">Wbay</a>
        </div>
        <div class="nav-links">
            <a href="../page/adminPage.php" id="active">Home</a>
            <a href="../page/manageUsers.php">Manage Users</a>
            <a href="../page/manageProducts.php">Manage Products</a>
            <div class="user-menu" id="userMenu">
                <button class="user-btn" id="userBtn">
                    <i class="fas fa-user"></i>
                </button>
                <div class="dropdown-menu" id="dropdownMenu">
                    <a href="../page/adminProfile.php" id="profileBtn">Profile</a>
                    <!-- Form for Logout -->
                    <form action="adminProductReview.php" method="GET">
                        <button class="navbar-btn" type="submit" id="logoutBtn" value="logout" name="action">Logout</button>
                        </form>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Product Review Card -->
     <section class="review-page">
        <div class="review-container">

            <!-- Image section -->
             <div class="image-container">
                <?php echo '<img src="'. $row["image_url"] .'" alt="Profile Picture" id="profileImage">' ?>
             </div>

             <!-- Review Area -->
             <div class="review-content">
                <div class="header">
                    <h2><?php echo $row['name']?></h2>
                    <h3 class="rating-box">Rating: <div id="rating"><?php echo number_format($rating,2) ?></div></h3>
                </div>
                <h4><?php echo $row['category']?>: <?php echo $row['sub_category'] ?></h4>
                <h4>Vendor: <?php echo $vendorName ?></h4>
                <p><?php echo $row['description']?></p>
                    <br>
                <div>
                    <h4>Price: <span id="price"><?php echo $row['price']?></span></h4>
                    <h4>Stock: <span id="stock"><?php echo $row['stock_quantity']?></span></h4> 
                </div>
                <br>
                <div class="btn-container add-to-cart-btn">
                    <a class="linkBtn" href="./adminEditProduct.php">
                    <!--added this part to help out the edit product function later -->
                    <?php echo '<button class="btn to-AdminEdit"  name="action" value="'.$productId.'">' ?>
                        <i class="fas fa-shopping-cart"></i>
                        Edit Product
                    </button>
                    </a>
                </div>
             </div>
        </div>
     </section>

     <!-- Page Footer -->
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
    <script src="../js/productReview.js"></script>
    <script src="../js/toEdit.js"></script>
    
</body>
</html>