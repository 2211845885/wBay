<?php
session_start(); // start session

include_once("../../serverhandle/managingProducts.php");
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

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $prodName = $_POST["productName"];
    $prodPrice = $_POST["productPrice"];
    $prodDesc = $_POST["productDescription"];
    $stocks = $_POST["Stocks"];
    $category = $_POST["mainCategory"] ?? "Unknown";
    $subCategory = $_POST["Category"];


    $info = ["name" => $prodName, "price" => $prodPrice, "description" => $prodDesc,
            "stock_quantity" => $stocks, "category" => $category, "sub_category" => $subCategory];

    $lastInsertedId = mangProd::addProduct($info);
    if ($lastInsertedId) {
    } else {
        echo "error";
    }
    header("location: vendorPage.php");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add products</title>
    
    <link rel="stylesheet" href="../css/addAndEditProduct.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/products.css">
    <link rel="stylesheet" href="../css/globalStyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navigation bar for the application -->
    <nav class="navbar">
        <div class="nav-brand">
            <!-- Link to vendor homepage -->
            <a href="../page/vendorPage.php">Wbay</a>
        </div>
        <div class="nav-links">
            <!-- Navigation links -->
            <a href="../page/vendorPage.php" class="active">Home</a> <!-- Active link to home -->
            <a href="../page/addproduct.php" class="add-product-nav-btn" id="active">
                <i class="fas fa-plus"></i> Add Product <!-- Add product button with icon -->
            </a>
            <a href="../page/vendorProducts.php">Your Products</a> <!-- Link to view vendor products -->
            <div class="user-menu" id="userMenu">
                <!-- User profile menu -->
                <button class="user-btn" id="userBtn">
                    <i class="fas fa-user"></i> <!-- User icon -->
                </button>
                <div class="dropdown-menu" id="dropdownMenu">
                    <a href="../page/vendorProfile.php" id="profileBtn">Profile</a> <!-- Link to profile -->
                    <!-- Form for Logout -->
                    <form action="addproduct.php" method="GET">
                        <button class="navbar-btn" type="submit" id="logoutBtn" value="logout" name="action">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main container for adding a product -->
    <div class="addProduct-container">
        <h1>Add Product</h1> <!-- Page heading -->
        <form id="addProduct-form" class="addProduct-form" method="post" action="addproduct.php" enctype="multipart/form-data">
            <!-- Form for product details -->
            <div class="form-group">
                <label for="productName">Product Name :</label> <!-- Label for product name -->
                <input type="text" id="productName" required minlength="3" placeholder="Enter product name (min 3 characters)" name="productName">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <!-- Price input -->
                    <label for="productPrice">Price :</label>
                    <input type="number" id="productPrice" step="1" min="0" required placeholder="Enter price" name="productPrice">
                </div>
                <div class="form-group">
                    <!-- Stock input -->
                    <label for="Stocks">Stock :</label>
                    <input type="number" id="Stocks" min="0" required placeholder="Available quantity" name="Stocks">
                </div>
                <div class="form-group">
                    <!-- Category dropdown -->
                    <label for="Category">Category :</label>
                    <select id="Category" name="Category" required>
                        <option value="" disabled selected>Select a category</option> <!-- Default option -->
                        <optgroup label="Electronics" data-group="Electronics">
                            <!-- Electronics category options -->
                            <option value="Mobile Phones">Mobile Phones</option>
                            <option value="Laptops">Laptops</option>
                            <option value="Cameras">Cameras</option>
                            <option value="Accessories">Accessories</option>
                        </optgroup>  
                        <optgroup label="Fashion" data-group="Fashion">
                            <!-- Fashion category options -->
                            <option value="Clothing">Clothing</option>
                            <option value="Shoes">Shoes</option>
                            <option value="Watches">Watches</option>
                            <option value="Jewelry">Jewelry</option>
                        </optgroup>
                    </select>
                    <input type="hidden" name="mainCategory" id="mainCategory">
                </div>
                <div class="form-group">
                    <!-- Description input -->
                    <label for="productDescription">Description :</label>
                    <textarea id="productDescription" name="productDescription" rows="4" required minlength="10" placeholder="Detailed product description (min 10 characters)"></textarea>
                </div>
                <div class="form-group" id="uploadImageId">
                    <!-- Image upload section -->
                    <label>Product Image :</label>
                    <div class="imageUpload-container" id="dropZone">
                        <img id="imagePreview" src="/img/box.png" alt="Product preview"> <!-- Default preview -->
                        <div>
                            <label for="image" class="upload-button"> <i class="upload"></i> Choose Image </label>
                            <input type="file" id="image" hidden required name="image" accept="image/*" > <!-- Image input -->
                        </div>
                    </div>
                </div>
                <div>
                    <!-- Submit button -->
                     <input id="subbtn" type="submit" value="Submit" onclick="mainCat()" class="submit-button"> <i class="addProduct"></i>
                </div>
            </div>
        </form>
    </div>

    <script src="../js/categoryHandler.js"></script>
    <script src="../js/ProfileDropDown.js"></script>
</body>
</html>
