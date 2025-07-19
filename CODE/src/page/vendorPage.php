<?php
session_start(); // start session

include_once("../../serverhandle/userhandle.php");
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

//shows 21 products to fill the page for the specific vendor that is logged in
$row = prodlist::vendorProd(21);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wbay - Your Online Shopping Destination</title>
    <link rel="stylesheet" href="../css/globalStyle.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/products.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!--navbar for the vendor class it can be copy pasted for every page that only the vendor can see-->
    <nav class="navbar">
        <div class="nav-brand">
            <a href="../page/vendorPage.php">Wbay</a>
        </div>
        <div class="nav-links">
            <a href="../page/vendorPage.php" id="active">Home</a>
            <a href="../page/addproduct.php" class="add-product-nav-btn">
                <i class="fas fa-plus"></i> Add Product
            </a>
            <a href="../page/vendorProducts.php">Your Products</a>
            <div class="user-menu" id="userMenu">
                <button class="user-btn" id="userBtn">
                    <i class="fas fa-user"></i>
                </button>
                <div class="dropdown-menu" id="dropdownMenu">
                    <a href="../page/vendorProfile.php" id="profileBtn">Profile</a>
                    <!-- Form for Logout -->
                    <form action="vendorPage.php" method="GET">
                    <button class="navbar-btn" type="submit" id="logoutBtn" value="logout" name="action">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!--this is the section that will show the vendor some of the products that he is displaying on the site -->
    
    <!--this section should also only show a couple of products that the vendor has listed
    all of the vendor products should be shown in the vendorProducts.php page-->
    <section class="new-products">
        <h2>Some of Your Products</h2>
        <div class="products-grid" id="products">
            <?php
                if(!empty($row)){
                    foreach($row as $prod){
                        echo "
                        <a href='vendorProductReview.php?id={$prod['id']}'  class='product-card-link'>
                        <div class='product-card' data-id='{$prod['id']}'>
                            <div class='product-image'>
                                <img src='{$prod['image_url']}' alt='{$prod['name']}'>
                            </div>
                            <div class='product-details'>
                                <span class='product-category'>{$prod['category']}</span>
                                <h3>{$prod['name']}</h3>
                                <p class='price'>\$ {$prod['price']}</p>
                                <p class='description'>{$prod['description']}</p>
                                <button value='{$prod['id']}' class='to-edit add-to-cart-btn'>
                                <i class='fas fa-wrench'></i>
                                    Edit Product
                                </button>
                            </div>
                        </div>
                        </a>";
                    }
                } else {
                    echo "<p>no products to list</p>";
                }
            ?>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>About Us</h3>
                <p>Your one-stop shop for all your needs</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="../page/vendorProducts.php">Your Products</a></li>
                    <li><a href="../page/addproduct.php">Add Product</a></li>
                    <li><a href="../page/vendorProfile.php">Profile</a></li>
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
    <script src="../js/products.js"></script>
    <script src="../js/toEdit.js"></script>
</body>
</html>