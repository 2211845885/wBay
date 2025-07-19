<?php
session_start(); // start session

include_once("../../serverhandle/userhandle.php");
include_once("../../serverhandle/productListing.php");
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

$row = prodlist::listProducts(21, 'DESC');
$cartCount = CartHandle::amountInCart();

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
    <!--navbar section most of the styling code can be found in the navbar.css file
    some js code is also implemented to make the userMenu drop down list work-->
    <nav class="navbar">
        <div class="nav-brand">
            <a href="../page/customerPage.php">Wbay</a>
        </div>
        <div class="nav-links">
            <a href="../page/customerPage.php" id="active">Home</a>
            <a href="../page/products.php">Products</a>
            <div class="cart-icon">
                <a href="../page/cart.php">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count" id="cartCount"><?php echo $cartCount ?></span>
                </a>
            </div>
            <div class="user-menu" id="userMenu">
                <button class="user-btn" id="userBtn">
                    <i class="fas fa-user"></i>
                </button>
                <div class="dropdown-menu" id="dropdownMenu">
                    <a href="../page/profile.php" id="profileBtn">Profile</a>

                    <!-- Form for Logout -->
                    <form action="customerPage.php" method="GET">
                    <button class="navbar-btn" type="submit" id="logoutBtn" value="logout" name="action">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    
    <!--This is the hero section mostly just a slide show of random internet photos--> 
    <section class="hero">
        <div class="hero-content">
            <h1>Welcome to Wbay</h1>
            <p>Discover amazing products at great prices</p>
            <a href="../page/products.php" class="cta-button">Shop Now</a>
        </div>
    </section>


    <section class="new-products">
        <h2>New Products</h2>
        <div class="products-grid" id="products">
            <?php
                if(!empty($row)){
                    foreach($row as $prod){
                        $cart_btn=
                        "<button class='add-to-cart-btn' name='add-to-cart-btn' value='{$prod['id']}'>
                          
                                <i class='fas fa-shopping-cart'></i>
                                    Add to Cart
                                </button>";
                        if(CartHandle::isAdded($prod['id'])) $cart_btn="
                        <button disabled class='add-disabled' name='add-to-cart-btn'>
                                <i class='fas fa-shopping-cart'></i>
                                 Added to Cart
                                </button>";
                        echo "
                        <a href='productReview.php?id={$prod['id']}'  class='product-card-link'>
                        <div class='product-card' data-id='{$prod['id']}'>
                            <div class='product-image'>
                                <img src='{$prod['image_url']}' alt='{$prod['name']}'>
                            </div>
                            <div class='product-details'>
                                <span class='product-category'>{$prod['category']}</span>
                                <h3>{$prod['name']}</h3>
                                <p class='price'>\$ {$prod['price']}</p>
                                <p class='description'>{$prod['description']}</p>
                                {$cart_btn}
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
                    <li><a href="../page/products.php">Products</a></li>
                    <li><a href="../page/cart.php">Cart</a></li>
                    <li><a href="../page/profile.php">Profile</a></li>
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
    <script type="module" src="../js/products.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</body>
</html>