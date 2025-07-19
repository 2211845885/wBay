<?php
session_start(); // start session

include_once("../../serverhandle/carthandle.php");
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

//initally displays all products in the page
$row = prodlist::searchProd("", "all", "", "");

if($_SERVER["REQUEST_METHOD"] == "POST"){
    //lists products depending on the search
    if($_POST["action"]=='search'){
        $searchWord = $_POST["search"];
        $cate = $_POST["cate"];
        $min = $_POST["min"];
        $max = $_POST["max"];
            
        $row = prodlist::searchProd($searchWord, $cate, $min, $max);
    }
    if ($_POST["action"] == "add-to-cart-btn") {
        $result = ["added" => CartHandle::addToCart($_POST["id"]), "newCount" => CartHandle::amountInCart()];
        echo json_encode($result);
        return;
    }
}
$cartCount = CartHandle::amountInCart();


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wbay - Products</title>
    <link rel="stylesheet" href="../css/globalStyle.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/products.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-brand">
            <a href="../page/customerPage.php">Wbay</a>
        </div>
        <div class="nav-links">
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
                    <a href="../page/profile.php" id="profileBtn">Profile</a>
                    <!-- Form for Logout -->
                    <form action="products.php" method="GET">
                        <button class="navbar-btn" type="submit" id="logoutBtn" value="logout" name="action">Logout</button>
                        </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Products Header -->
    <header class="products-header">
        <h1>Our Products</h1>
        <form method="post" action="products.php">
            <div class="filters">
                <select id="categoryFilter" name='cate'>
                    <option value="all">All Categories</option>
                    <option value="electronics">Electronics</option>
                    <option value="fashion">Fashion</option>
                    <option value="homeAndKitchen">Home & Kitchen</option>
                    <option value="sports">Sports</option>
                </select>
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Search products..." name="search">
                    <button name="action" value="search">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <div class="price-filter">
                    <input type="number" id="minPrice" placeholder="Min Price" name="min" min=0>
                    <input type="number" id="maxPrice" placeholder="Max Price" name="max" max=100000000.00>
                </div>
            </div>
        </form>
    </header>

    <!-- Products Grid -->
    <section class="products-section">
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

    <!-- Footer -->
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

    <!-- Scripts -->
    <script src="../js/ProfileDropDown.js"></script>
    <script type="module" src="../js/products.js"></script>
    <!--JQuery CDN-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

</body>
</html>
