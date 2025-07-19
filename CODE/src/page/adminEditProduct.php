<?php
session_start(); // start session

include_once("../../serverhandle/userhandle.php");
include_once("../../serverhandle/productListing.php");
include_once("../../serverhandle/managingProducts.php");


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

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $prodID = $_GET["prodID"];
    $row = prodlist::prodDesc($prodID);
    $name = $row["name"];
    $prodPrice = $row["price"];
    $prodQuantity = $row["stock_quantity"];
    $prodDesc = $row["description"];

}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST["action"] == "editProd") {
        $prodID = $_POST["id"];
        $prodName = $_POST["productName"];
        $prodPrice = $_POST["productPrice"];
        $prodDesc = $_POST["productDescription"];
        $stocks = $_POST["Stocks"];
        $category = $_POST["mainCategory"] ?? "Unknown";
        $subCategory = $_POST["Category"];

        $info = ["name" => $prodName, "price" => $prodPrice, "description" => $prodDesc,
                "stock_quantity" => $stocks, "category" => $category, "sub_category" => $subCategory, "id" => $prodID];

        $editProduct = mangProd::editProduct($info);
        if ($editProduct) {
        } else {
            echo "error";
        }
    } if($_POST["action"] == "deleteProd") {
        $prodID = $_POST["id"];
        echo $prodID;
        $deleteProduct = mangProd::deleteProduct($prodID);
    }
    
    header("Location: manageProducts.php");

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
            <a href="../page/adminPage.php">Wbay</a>
        </div>
        <div class="nav-links">
            <a href="../page/adminPage.php">Home</a>
            <a href="../page/manageUsers.php">Manage Users</a>
            <a href="../page/manageProducts.php">Manage Products</a>
            <div class="user-menu" id="userMenu">
                <button class="user-btn" id="userBtn">
                    <i class="fas fa-user"></i>
                </button>
                <div class="dropdown-menu" id="dropdownMenu">
                    <a href="../page/adminProfile.php" id="profileBtn">Profile</a>
                    <!-- Form for Logout -->
                    <form action="adminEditProduct.php" method="GET">
                        <button class="navbar-btn" type="submit" id="logoutBtn" value="logout" name="action">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main container for adding/editing a product -->
    <div class="addProduct-container">
        <h1>Edit Product</h1> <!-- Page heading -->
        <form id="addProduct-form" action="adminEditProduct.php" method="POST" enctype="multipart/form-data" class="addProduct-form">
            <!-- Form for product details -->
            <div class="form-group">
                <label for="productName">Product Name :</label> <!-- Label for product name -->
                <?php echo '<input type="text" id="productName" value="'.$name.'" required minlength="3" placeholder="Enter product name (min 3 characters) " name="productName">' ?>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <!-- Price input -->
                    <label for="productPrice">Price :</label>
                    <?php echo '<input type="number" id="productPrice" value="'.$prodPrice.'" step="1" min="0" required placeholder="Enter price" name="productPrice">' ?>
                </div>
                <div class="form-group">
                    <!-- Stock input -->
                    <label for="Stocks">Stock :</label>
                    <?php echo '<input type="number" id="Stocks" value="'.$prodQuantity.'" min="0" required placeholder="Available quantity" name="Stocks">' ?>
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
                    <?php echo '<textarea id="productDescription" name="productDescription" rows="4" required minlength="10" placeholder="Detailed product description (min 10 characters)">'.$prodDesc.'</textarea>' ?>
                </div>
                <div class="form-group" id="uploadImageId">
                    <!-- Image upload section -->
                    <label>Product Image :</label>
                    <div class="imageUpload-container" id="dropZone">
                        <img id="imagePreview" src="/img/box.png" alt="Product preview"> <!-- Default preview -->
                        <div>
                            <label for="image" class="upload-button"> <i class="upload"></i> Choose Image </label>
                            <input type="file" hidden id="image" name="image" accept="image/*" required> <!-- Image input -->
                        </div>
                    </div>
                </div>
                <div>
                    <!-- Submit -->
                    <?php echo '<input type="text" hidden name="id" value="'.$prodID.'">' ?>
                    <button type="submit" name="action" value="editProd" class="submit-button"> <i class="addProduct"></i> Save Changes </button>
                </div>
            </div>
        </form>
        <form action="adminEditProduct.php" method="POST">
            <!-- Delete -->
            <?php echo '<input type="text" hidden name="id" value="'.$prodID.'">' ?>
            <button type="submit" name="action" value="deleteProd" class="delete-button"> <i class="addProduct"></i> Remove Product </button>
        </form>
    </div>

    <script src="../js/categoryHandler.js"></script>
    <script src="../js/ProfileDropDown.js"></script>
</body>
</html>
