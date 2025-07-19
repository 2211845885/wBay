<?php
include_once("dbaccess.php");
class user {
    //function to register a user 
    public static function reg($creds, $info){
        $db = new dbhandle();
        try{
            //code that checks if the user already exisits
            $sql = "SELECT COUNT(*) AS user_count FROM user_cred WHERE user_name = ?";
            $result = $db->prepQuery($sql, "s", $creds["username"]);

            if($result[0]["user_count"] > 0 ){
                return false;
                exit;
            }

            $hashedpass = password_hash($creds["password"], PASSWORD_DEFAULT);
            
            //first creates the user in the user_cred table
            $sql = "INSERT INTO user_cred (user_name, email, password, role) VALUES (?, ?, ?, ?)";
            $db->prepUpdate($sql, "ssss", $creds["username"], $creds["email"], $hashedpass, $creds["role"]);

            //takes the last inserted id in the user_cred table which is the user that was just inserted in the line above
            $sql = "SELECT LAST_INSERT_ID() FROM user_cred";
            $id = $db->query($sql)[0]["LAST_INSERT_ID()"];
            
            //checks the user type and creates said user in their respective table
            //user_id column is a fk to the id column in user_cred
            if($creds["role"] == "customer"){
                $sql = "INSERT INTO customers (user_id, address, phone_number) VALUES (?, ?, ?)";
                $db->prepUpdate($sql, "iss", $id, $info["address"], $info["phone"]);
            } else {
                $sql = "INSERT INTO vendors (user_id, company_name, company_address) VALUES (?, ?, ?)";
                $db->prepUpdate($sql, "iss", $id, $info["name"], $info["address"]);
            }
        } catch (mysqli_sql_exception $ex) {
            echo $ex;
        }
        //closes connection and returns succsess
        $db->close();
        return true;
    }

    //function to login user
    public static function login($creds){
        $db = new dbhandle();
        try {
            // Query the database to find the user by username and password
            $sql = "SELECT * FROM user_cred WHERE user_name = ?";
            $row = $db->prepQuery($sql, "s", $creds["username"]);
    
            // Check if a user was found
            if(count($row) > 0) {
                if(password_verify($creds["password"], $row[0]["password"])){
                    // Start the session to store user data
                session_start();
                $_SESSION['user_id'] = $row[0]['id'];
                $_SESSION['role'] = $row[0]['role'];
    
                return true;  // Login successful
                }    
            } else {
                return false;  // Invalid credentials
            }
        } catch (mysqli_sql_exception $ex) {
            echo $ex;
        } finally {
            // Close the database connection
            $db->close();
        }
    }

    public static function deleteUser($id){
        $db = new dbhandle();
        try {
            // Delete user from user_cred table
            $sql = "DELETE FROM user_cred WHERE id = ?";
            $db->prepUpdate($sql, "i", $id);
            
        } catch (mysqli_sql_exception $ex) {
            echo $ex;
        }
        $db->close();
    }

    // Function to log out a user
    public static function logout(): never{
        session_start();
         // Unset all session variables
        session_unset();
        // Destroy the session
        session_destroy();
        // Redirect to the login page
        header(header: "Location: login.php");
        exit();
    }

     // Function to check if the user is logged in or not
     public static function isLoggedIn(): bool {
        // To avoid starting a session multiple times
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
         // Check if the user ID and role are set in the session
        return isset($_SESSION['user_id']) && isset($_SESSION['role']);
        
    }

    public static function getUserInfo($userId) {
        $db = new dbhandle(); // Initialize the database handler
        try {
            // Query to fetch user information from user_cred
            $sql = "SELECT uc.id, uc.user_name, uc.email, uc.role, c.address AS customer_address, c.phone_number, v.company_name, v.company_address FROM user_cred uc LEFT JOIN customers c ON uc.id = c.user_id LEFT JOIN vendors v ON uc.id = v.user_id WHERE uc.id = ?";
            
            // Execute the query and fetch the result
            $result = $db->prepQuery($sql, "i", $userId);
    
            if (count($result) > 0) {
                // Return the first row (should be the only row for a unique user ID)
                return $result[0];
            } else {
                return null; // User not found
            }
        } catch (mysqli_sql_exception $ex) {
            echo $ex; // Handle exceptions
        } finally {
            $db->close(); // Close the database connection
        }
    }

    public static function getUsers(){
        $db = new dbhandle();
        try{
            $sql = "SELECT * FROM user_cred";
            $row = $db->query($sql);
            return $row;
        } catch (mysqli_sql_exception $e){
            error_log($e->getMessage());
        } finally {
            $db->close();
        }
    }

    public static function updateInfo($userId, $role, $info) {
        $db = new dbhandle();
        try {

            // Update role-specific tables
            if ($role == "customer") {
                $sql = "UPDATE user_cred SET user_name = ?, email = ? WHERE id = ?";
                $db->prepUpdate($sql, "ssi", $info['username'], $info['email'], $userId);
                $sql = "UPDATE customers SET address = ?, phone_number = ? WHERE user_id = ?";
                $db->prepUpdate($sql, "ssi", $info['address'], $info['phone'], $userId);
            } elseif ($role == "vendor") {
                $sql = "UPDATE user_cred SET email = ? WHERE id = ?";
                $db->prepUpdate($sql, "si", $info['email'], $userId);
                $sql = "UPDATE vendors SET company_name = ?, company_address = ? WHERE user_id = ?";
                $db->prepUpdate($sql, "ssi", $info['company_name'], $info['company_address'], $userId);
            } elseif ($role == "admin") {
                // Update user_cred table for admin
                $sql = "UPDATE user_cred SET user_name = ?, email = ? WHERE id = ?";
                $db->prepUpdate($sql, "ssi", $info['username'], $info['email'], $userId);
            }

            return true; // Update successful
        } catch (mysqli_sql_exception $ex) {
            echo $ex;
            return false; // Update failed
        } finally {
            $db->close();
        }
    }
    
    public static function updatePassword($userId, $currentPassword, $newPassword) {
        $db = new dbhandle();
        try {
            // Verify the current password
            $sql = "SELECT password FROM user_cred WHERE id = ?";
            $result = $db->prepQuery($sql, "i", $userId);
    
            if (count($result) > 0) {
                $storedPassword = $result[0]['password'];
    
                // Check if the current password matches
                if (password_verify($currentPassword, $storedPassword)) {
                    //hashing new password
                    $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                    // Update the password
                    $sql = "UPDATE user_cred SET password = ? WHERE id = ?";
                    $db->prepUpdate($sql, "si", $hashedNewPassword, $userId);
                    
                    return true; // Password updated successfully
                } else {
                    return false; // Current password is incorrect
                }
            } else {
                return false; // User not found
            }
        } catch (mysqli_sql_exception $ex) {
            echo $ex;
            return false; // Error occurred
        } finally {
            $db->close();
        }
    }

    public static function userToVendor(){
        $db = new dbhandle();
        try {
            $sql = "SELECT id FROM vendors WHERE user_id = ?";
            $id = $db->prepQuery($sql, "i", $_SESSION["user_id"])[0]["id"];
            return $id;
        } catch (mysqli_sql_exception $e){
            error_log($e->getMessage());
        } finally {
            $db->close();
        }
    }

    public static function userToCustomer(){
        $db = new dbhandle();
        try {
            $sql = "SELECT id FROM customers WHERE user_id = ?";
            $id = $db->prepQuery($sql, "i", $_SESSION["user_id"])[0]["id"];
            return $id;
        } catch (mysqli_sql_exception $e){
            error_log($e->getMessage());
        } finally {
            $db->close();
        }
    }

    public static function getVendorName($id){
        $db = new dbhandle();
        try {
            $sql = "SELECT company_name FROM vendors where id = ?";
            $vendorName = $db->prepQuery($sql, "i", $id);
            return $vendorName[0]["company_name"];
        } catch (mysqli_sql_exception $e){
            error_log($e->getMessage());
        } finally {
            $db->close();
        }
    }
}

?>