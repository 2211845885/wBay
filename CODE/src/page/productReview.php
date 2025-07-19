<?php
session_start(); // start session

include_once("../../serverhandle/userhandle.php");
include_once("../../serverhandle/ratinghandle.php");
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

if (isset($_GET['id'])) {
    $productId = intval($_GET['id']);

    $row = prodlist::prodDesc($productId);  
    $vendorName = user::getVendorName($row["vendor_id"]);  
} else {
    header("Location: products.php");
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if($_POST["action"] == "rate"){
        $ratingValue = $_POST["starRating"];
        $productId = $_POST["hiddenId"];
        ratinghandle::addRating($ratingValue, $productId);
    }

    if($_POST["action"] == "addToCart"){
        CartHandle::addToCartByQuantity($_POST["hiddenId"], $_POST["quan"]);
    }
    header("Location: products.php");
}
$rating = ratinghandle::getRating($productId);
$cartCount = CartHandle::amountInCart();


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
    
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-brand">
            <a href="../page/customerPage.php">Wbay</a>
        </div>
        <div class="nav-links">
            <!-- WBay Needs to be a text not a link -->
            <a href="../page/customerPage.php">Home</a>
            <a href="../page/products.php" id="active">Products</a>
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
                    <a href="/src/html/profile.php" id="profileBtn">Profile</a>
                    <!-- Form for Logout -->
                    <form action="productReview.php" method="GET">
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
                    <h2><?php echo $row['name'] ?></h2>
                    <h3 class="rating-box">Rating: <div id="rating"><?php echo number_format($rating,2) ?></div></h3>
                </div>
                <h4><?php echo $row['category'] ?>: <?php echo $row['sub_category'] ?></h4>
                <h4>Vendor: <?php echo $vendorName ?></h4>
                <p><?php echo $row['description'] ?></p>
                    <br>
                <div>
                    <h4>Price: <span id="price"><?php echo $row['price'] ?></span></h4>
                    <h4>Stock: <span id="stock"><?php echo $row['stock_quantity'] ?></span></h4> 
                    <div class="quantity-controls">
                        <h4>Quantity:</h4>
                        <button class="quantity-btn minus" onclick="updateQuantity(-1)">-</button>
                        <input type="number" class="quantity" id="quantityInput" min="1" value="1" step="1">
                        <button class="quantity-btn plus" onclick="updateQuantity(1)">+</button>
                    </div>
                </div>
                <br>
                <div class="btn-container add-to-cart-btn">
                    <form action="productReview.php?id=<?= $productId ?>" method="post" style="display: flex">
                        <div style="padding: 1rem">
                        <input type="text" value="<?= $productId ?>" hidden name="hiddenId">
                            <input class="rating" min="0" max="5"
                                oninput="this.style.setProperty('--value', this.value)"
                                step="0.1"
                                type="range"
                                value="1"
                                name="starRating">
                        </div>
                        <button class="btn" name="action" value="rate">
                            <i class="fas fa-star"></i>
                            Rate
                        </button>
                    </form>
                    <form action="productReview.php" method="post">
                        <input type="number" value="1" hidden id="hiddenQuantity" name="quan">
                        <input type="text" value="<?= $productId ?>" hidden name="hiddenId">
                        <?php
                            if(CartHandle::isAdded($productId)){
                                echo'
                                    <button class="add-disabled" disabled>
                                        <i class="fas fa-shopping-cart"></i>
                                        In Cart
                                    </button>
                                ';
                            } else {
                                echo'
                                    <button class="btn" name="action" value="addToCart">
                                        <i class="fas fa-shopping-cart"></i>
                                        Add to Cart
                                    </button>
                                ';
                            }
                        ?>
                    </form>
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
    <script src="../js/productReview.js"></script>
    
</body>
</html>