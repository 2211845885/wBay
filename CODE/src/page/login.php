<?php
// Start the session
session_start();
// Variable to track if the operation was successful
$successfull = true;
// Include the server-side user handling file
include ("../../serverhandle/userhandle.php");

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Retrieve the 'active' action, username, and password from the POST request
    $active = $_POST["active"];
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Determine the action to perform based on 'active'
    switch ($active) {
        case "signup":
            // Handle user signup
            $email = $_POST["email"]; // Get the email
            $role = $_POST["accountType"]; // Get the role (customer or company)

            // If the role is 'customer', get additional customer details
            if ($role == 'customer') {
                $customerPhone = $_POST["customerPhone"];
                $customerAddress = $_POST["customerAddress"];
                $info = ["phone" => $customerPhone, "address" => $customerAddress]; // Store customer info
            } else {
                // If the role is not 'customer', assume it's a company
                $companyName = $_POST["companyName"];
                $companyAddress = $_POST["companyAddress"];
                $info = ["name" => $companyName, "address" => $companyAddress]; // Store company info
            }

            // Prepare credentials and call the registration method
            $creds = ["username" => $username, "email" => $email, "password" => $password, "role" => $role];
            $successfull = user::reg($creds, $info); // Call registration function
            break;

        case "signin":
            // Handle user login
            $creds = ["username" => $username, "password" => $password]; // Prepare credentials
            $successfull = user::login($creds); // Call login function

            if ($successfull) {
                // If login is successful, set session variables
                $_SESSION['loggedin'] = true;

                if($_SESSION["role"] == "vendor") {
                    header("Location: vendorPage.php");
                    exit();
                } else if ($_SESSION["role"] == "admin"){
                    // Redirect the user to the customer page
                    header("Location: adminPage.php");
                    exit(); // Ensure no further code is executed after the redirect
                } else {
                    header("Location: customerPage.php");
                }
            }
            break;

        default:
            // Handle unexpected 'active' values
            echo "role error";
    }
}
?>


<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Sign In Page</title>
    <link rel="stylesheet" href="../css/login.css">
    

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <p class="logo">WBay</p>
    <div class="container" id="container">
        <div id="sign-up-container" class="form-container sign-up-container">
            <form id="sign-up-form" action="login.php" method="post">
                <h1 class="h22">Create Account</h1>
                <div class="input-group">
                    <input id="username" type="text" placeholder="Username" name="username"/>
                    <div class="error"></div>
                    <?php
                    if (!$successfull && $active == "signup") echo "<span class='error'>Username is taken</span>";
                    ?>
                </div>
                <div class="input-group">
                    <input id="email" type="email" placeholder="Email" name="email"/>
                    <div class="error"></div>
                </div>
                <div id="customerDiv"></div>
                <div id="vendorDiv"></div>
                <div class="input-group">
                    <input id="password" type="password" placeholder="Password" name="password"/>
                    <div class="error"></div>
                </div>
                <div class="input-group">
                    <input id="conPassword" type="password" placeholder="Confirm Password" name="conPassword"/>
                    <div class="error"></div>
                </div>
                <div class="radio-container">
                    <input type="radio" id="customer" name="accountType" value="customer" checked>
                    <label for="customer" class="radio-label">Customer</label>
                    <input type="radio" id="vendor" name="accountType" value="vendor">
                    <label for="vendor" class="radio-label">Vendor</label>
                </div>
                <button type="submit" value="signup" name="active">Sign up</button>
            </form>
        </div>
        <div id="sign-in-container" class="form-container sign-in-container">
            <form action="login.php" method="post">
                <h1 class="h22">Sign in</h1>
                <div class="input-group">
                    <input id="signin-username" type="username" placeholder="Username" name="username"/>
                    <div class="error"></div>
                    <?php
                    if (!$successfull && $active == "signin") echo "<span class='error'>Invalid Username or password</span>"; 
                    ?>
                </div>
                <div class="input-group">
                    <input id="signin-password" type="password" placeholder="Password" name="password"/>
                    <div class="error"></div>
                </div>
                <button value="signin" name="active">Sign In</button>
            </form>
        </div>
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Welcome Back!</h1>
                    <p>To keep connected with us please login with your personal info</p>
                    <button class="ghost" id="signIn">Sign In</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Hello, Friend!</h1>
                    <p>Enter your personal details and start journey with us</p>
                    <button class="ghost" id="signUp">Sign Up</button>
                </div>
            </div>
        </div>
    </div>  
    <script src="../js/login.js"></script>
</body>
</html>
