<?php
include_once ("dbaccess.php");
include_once("userhandle.php");


class prodlist{
    //shows all product information depending on id
    public static function prodDesc($id){
        $db = new dbhandle();
        try{
            $sql = "SELECT * FROM products where id = ?";
            $row = $db->prepQuery($sql, "i", $id);
            return $row[0];
        } catch (mysqli_sql_exception $e) {
            error_log($e->getMessage());
        } finally {
            $db->close();
        }
    }

    //list all products or a specific amount
    public static function listProducts($amount, $order){
        $db = new dbhandle();
        try{
            if($amount == 0){
                $sql = "SELECT * FROM products where stock_quantity > 0 ORDER BY id $order";
                $row = $db->query($sql);
            } else {
                $sql = "SELECT * FROM products where stock_quantity > 0 ORDER BY id $order LIMIT ? ";
                $row = $db->prepQuery($sql, "i", $amount);
            }

            return $row;
        } catch (mysqli_sql_exception $e){
            error_log($e->getMessage());
            return [];
        } finally {
            $db->close();
        }
    }

    public static function adminListProducts($amount, $order){
        $db = new dbhandle();
        try{
            if($amount == 0){
                $sql = "SELECT * FROM products ORDER BY id $order";
                $row = $db->query($sql);
            } else {
                $sql = "SELECT * FROM products ORDER BY id $order LIMIT ? ";
                $row = $db->prepQuery($sql, "i", $amount);
            }

            return $row;
        } catch (mysqli_sql_exception $e){
            error_log($e->getMessage());
            return [];
        } finally {
            $db->close();
        }
    }

    //search function depending on price, category, name
    public static function searchProd($searchWord, $cate, $min, $max){
        $db = new dbhandle();
        $searchWord = $searchWord!="" ? $searchWord . "%" : null;
        $cate = $cate=="all" ? null : $cate;
        $min = $min!="" ? $min : null;
        $max = $max!="" ? $max : null;

        $sql = "SELECT * FROM products
            WHERE  (name LIKE ? OR ? IS NULL) AND
            (category=? OR ? IS NULL) AND
            (price>=? OR ? IS NULL) AND
            (price<=? OR ? IS NULL) AND stock_quantity > 0";
        $types = "ssssdddd";


        try{
            $row = $db->prepQuery($sql, $types, $searchWord, $searchWord, $cate, $cate, $min, $min, $max, $max);
            return $row;
        } catch (mysqli_sql_exception $e){
            error_log($e->getMessage());
        } finally {
            $db->close();
        }

    }

    public static function adminSearchProd($searchWord){
        $db = new dbhandle();
        $searchWord = $searchWord!="" ? $searchWord . "%" : null;

        $sql = "SELECT * FROM products
            WHERE  (name LIKE ? OR ? IS NULL)";
        $types = "ss";


        try{
            $row = $db->prepQuery($sql, $types, $searchWord, $searchWord);
            return $row;
        } catch (mysqli_sql_exception $e){
            error_log($e->getMessage());
        } finally {
            $db->close();
        }

    }

    public static function vendorSearchProd($searchWord){
        $db = new dbhandle();
        $searchWord = $searchWord!="" ? $searchWord . "%" : null;

        $sql = "SELECT * FROM products
            WHERE  (name LIKE ? OR ? IS NULL) AND vendor_id = ?";
        $types = "ssi";

        try{
            $row = $db->prepQuery($sql, $types, $searchWord, $searchWord, user::userToVendor());
            return $row;
        } catch (mysqli_sql_exception $e){
            error_log($e->getMessage());
        } finally {
            $db->close();
        }

    }


    //function that lists a specfic vendor products
    public static function vendorProd($amount){
        $db = new dbhandle();

        try{
            if($amount == 0){
                $sql = "SELECT * FROM products where vendor_id IN (SELECT id FROM vendors WHERE user_id = ?)";
                $row = $db->prepQuery($sql, "i" , $_SESSION["user_id"]);
            } else {
                $sql = "SELECT * FROM products where vendor_id IN (SELECT id FROM vendors WHERE user_id = ?) LIMIT ?";
                $row = $db->prepQuery($sql, "ii", $_SESSION["user_id"] , $amount);
            }
            return $row;
        } catch (mysqli_sql_exception $e){
            error_log($e->getMessage());
        } finally {
            $db->close();
        }
    }
}
?>