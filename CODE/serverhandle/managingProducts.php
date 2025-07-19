<?php
include_once("dbaccess.php");

class mangProd{

    //Function to add products.
    public static function addProduct($info) {
        
        $db = new dbhandle();
        try {

            $sql = "SELECT id from vendors WHERE user_id = ?";
            $id = $db->prepQuery($sql, "i", $_SESSION["user_id"])[0];

            $username = $id["id"];
            $uploadedImg = $_FILES["image"];

            $uploadDir = "../../img/";
            $storgeDir = "/img/";
            $vendorDir = $uploadDir . $username;
            $vendorStorDir = $storgeDir . $username;

            // create a folder named after the vendor.
            if(!is_dir($vendorDir)) {
                /* if not created NOTE 0755 means that the owner have full permission,
                the group and others can read and execute*/
                echo $vendorDir;
                mkdir($vendorDir, 0755, true); 
            }

            $targetFile = $vendorDir . "/" . basename($uploadedImg["name"]);
            $targetStorFile = $vendorStorDir . "/" . basename($uploadedImg["name"]);

            if(move_uploaded_file($uploadedImg["tmp_name"], $targetFile)) {
            } else {
                echo "error";
            }

            // adding to the database
            $sql = "INSERT INTO products (vendor_id, name, price, description, 
                stock_quantity, category, sub_category, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $db->prepUpdate($sql, "isdsisss", $id["id"], $info["name"], $info["price"], $info["description"], 
                $info["stock_quantity"], $info["category"], $info["sub_category"], $targetStorFile);

            //takes the last inserted id
            $sql = "SELECT LAST_INSERT_ID() AS last_id";
            $result = $db->query($sql);
            $lastInsertedId = $result[0]["last_id"];

            return $lastInsertedId;

        } catch (mysqli_sql_exception $e) {
            echo $e;
        }

        $db->close();
    }

    public static function editProduct($info) {
        $db = new dbhandle();

        try {

            $sql = "SELECT id from vendors WHERE user_id = ?";
            $id = $db->prepQuery($sql, "i", $_SESSION["user_id"])[0];

            $username = $id["id"];
            $uploadedImg = $_FILES["image"];

            $uploadDir = "../../img/";
            $storgeDir = "/img/";
            $vendorDir = $uploadDir . $username;
            $vendorStorDir = $storgeDir . $username;

            // create a folder named after the vendor.
            if(!is_dir($vendorDir)) {
                /* if not created NOTE 0755 means that the owner have full permission,
                the group and others can read and execute*/
                echo $vendorDir;
                mkdir($vendorDir, 0755, true); 
            }

            $targetFile = $vendorDir . "/" . basename($uploadedImg["name"]);
            $targetStorFile = $vendorStorDir . "/" . basename($uploadedImg["name"]);
            echo $targetStorFile;

            if(move_uploaded_file($uploadedImg["tmp_name"], $targetFile)) {
            } else {
                echo "error";
            }

            $sql = "UPDATE products SET name = ?, price = ?, description = ?, stock_quantity = ?,
                category = ?, sub_category = ?, image_url = ? WHERE id = ?";
            $db->prepUpdate($sql, "sdsisssi", $info["name"], $info["price"], $info["description"], 
                $info["stock_quantity"], $info["category"], $info["sub_category"], $targetStorFile, $info["id"]);

        } catch (mysqli_sql_exception $e) {
            echo $e;
        }
        $db->close();
    }

    public static function deleteProduct($prodID) {
        $db = new dbhandle();
        try {
            $sql = "DELETE FROM products WHERE id = ?";
            $db->prepUpdate($sql, "i", $prodID);

        } catch(mysqli_sql_exception $e) {
            echo $e;
        }

    }

}


?>