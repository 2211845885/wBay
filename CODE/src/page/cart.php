
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

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if($_POST["action"] == "remove"){
        $product_id = $_POST["prodID"];
        CartHandle::removeFromCart($product_id);
        header("location: cart.php");
    }
    if($_POST["action"] == "checkout"){
        CartHandle::checkout();
    }
}

$cart = CartHandle::getCartItems();
$price = CartHandle::total();
$cartCount = CartHandle::amountInCart();


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping cart</title>
    <link rel="stylesheet" href="../css/globalStyle.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/cart.css">
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
                <a href="../page/cart.php" id="active">
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
                    <form action="cart.php" method="GET">
                        <button class="navbar-btn" type="submit" id="logoutBtn" value="logout" name="action">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <section class="shopping-cart">
        <h1>Shopping Cart</h1>

        <div class="cart-container">
            <!-- Contains all cart items-->
            <div class="cart-items">
                <?php
                    if(!empty($cart)){
                        foreach($cart as $item){
                            $total = $item['product_price'] * $item['cart_quantity'];
                            echo"
                            <div class='cart-item'>
                                <img src='{$item['product_image']}' alt='MacBook Air M1'>
                                <div class='item-details'>
                                    <h3>{$item['product_name']}</h3>
                                    <p class='item-price'>" . number_format($total, 2) ."</p>
                                </div>
                                <!-- Quantity Controls -->
                                <div class='quantity-controls'>
                                    <input type='number' class='quantity' min='1' disabled value='$item[cart_quantity]'>
                                </div>
                                <form action='cart.php' method='POST'>
                                    <button class='remove-item' name='action' value='remove' >
                                        <i class='fas fa-trash'></i>
                                    </button>
                                    <input type='text' hidden name='prodID' value='{$item['product_id']}'>
                                </form>
                            </div>
                            ";
                        }
                    }
                ?>
            </div>

            <!-- Order Summary -->
             <?php 
                if(!empty($cart)){
                    echo '
                    <div class="summary">
                        <h2>Order Summary</h2>
                        <div class="item-summary">
                            <p>Subtotal</p>
                            <p>'.$price.'</p>
                        </div>
                        <div class="item-summary">
                            <p>Delivery fee</p>
                            <p>$10.00</p>
                        </div>
                        <div>
                            <div class="item-summary total">
                                <p>Total</p>
                                <p>'.number_format($price + 10, 2).'</p>
                            </div>
                        </div>
                        <form action="cart.php" method="post">
                            <button id="checkoutBtn" class="checkout-btn" name="action" value="checkout">
                                <i class="fas fa-lock"></i> Checkout
                            </button>
                        </form>
                    </div>';
                }
             ?>
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
</body>
</html>