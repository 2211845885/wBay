<?php
include_once("dbaccess.php");
include_once("userhandle.php");

class CartHandle{
    public static function addToCart($product_id) {
        return self::addToCartByQuantity($product_id, 1);
    }
    public static function addToCartByQuantity($product_id, $quantity) {
       if(!self::isAvilable($product_id, $quantity)){
           return false;
       }

        $db = new dbhandle();
        $db->prepUpdate("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?", "ii", $quantity, $product_id);
        $result= $db->prepQuery("SELECT * FROM products WHERE id = ?", "i", $product_id)[0]["price"];
        $db->prepUpdate("INSERT INTO cart (customer_id, product_id, quantity, total_price)  VALUES (?, ?,?,?)",   "iiid",  user::userToCustomer(), $product_id ,$quantity,$result*$quantity);
        $db->close();
        return true;
    }
    public static function isAvilable($product_id, $quantity){
        $db = new dbhandle();
        $result= $db->prepQuery("SELECT * FROM products WHERE id = ?", "i", $product_id)[0]["stock_quantity"];
        $db->close();
        return $result>=$quantity;
    }
    public static function isAdded($product_id){
        $db = new dbhandle();
        $result= $db->prepQuery("SELECT * FROM cart WHERE product_id = ? AND customer_id = ?", "ii", $product_id, user::userToCustomer());
        $db->close();
        return count($result)>0;
    }

    public static function removeFromCart($product_id){
        $db = new dbhandle();
        try{
            $sql = "DELETE FROM cart WHERE product_id = ? AND customer_id = ?";
            $db->prepUpdate($sql, "ii", $product_id, user::userToCustomer());
        } catch (mysqli_sql_exception $e){
            error_log($e->getMessage());
        } finally {
            $db->close();
        }
    }

    public static function total(){
        $db = new dbhandle();
        try{
            $sql = "SELECT SUM(total_price) AS total_value FROM cart WHERE customer_id = ?";
            $row = $db->prepQuery($sql, "i", user::userToCustomer());
            return $row[0]['total_value'];
        } catch (mysqli_sql_exception $e){
            error_log($e->getMessage());
        } finally {
            $db->close();
        }
    }

    public static function amountInCart(){
        if (!isset($_SESSION["user_id"])) {
            return 0;
        }

        $db = new dbhandle();
        try{
            $sql = "SELECT COUNT(*) AS count FROM cart WHERE customer_id = ?";
            $row = $db->prepQuery($sql, "i", user::userToCustomer());

            return $row[0]["count"];
        } catch (mysqli_sql_exception $e){
            error_log($e->getMessage());
        } finally {
            $db->close();
        }
    }

    public static function getCartItems(){
        $db = new dbhandle();
        try{
            $sql = "SELECT 
                    p.id AS product_id,
                    p.name AS product_name,
                    p.price AS product_price,
                    p.description AS product_description,
                    p.image_url AS product_image,
                    c.quantity AS cart_quantity,
                    c.total_price AS cart_total_price
                    FROM cart c
                    JOIN 
                    products p ON c.product_id = p.id
                    WHERE c.customer_id = ?;";
            $row = $db->prepQuery($sql, "i", user::userToCustomer());

            return $row;
        } catch (mysqli_sql_exception $e){
            error_log($e->getMessage());
        } finally {
            $db->close();
        }
    }

    public static function removeAll(){
        $db = new dbhandle();
        try{
            $sql = "DELETE FROM cart where customer_id = ?";
            $db->prepUpdate($sql, "i", user::userToCustomer());
        } catch (mysqli_sql_exception $e){
            error_log($e->getMessage());
        } finally {
            $db->close();
        }
    }

    public static function checkout(){
        $db = new dbhandle();

        try{
            $sql = "INSERT INTO orders (customer_id, product_id, quantity, total_price, status, time) VALUES (?, ?, ?, ?, ?, ?)";
            $row = self::getCartItems();
            foreach($row as $cartItem){
                $db->prepUpdate($sql, "iiidss", user::userToCustomer(), $cartItem["product_id"], $cartItem["cart_quantity"], $cartItem["cart_total_price"], "pending", date("Y-m-d H:i:s"));
            }
            self::removeAll();
        } catch (mysqli_sql_exception $e){
            error_log($e->getMessage());
        } finally {
            $db->close();
        }
    }

    public static function orders(){
        $db = new dbhandle();

        try{
            $sql = "SELECT SUM(orders.total_price) as 'total', count(*) as 'items', orders.status, products.vendor_id, orders.time FROM orders
                    JOIN products ON products.id = orders.product_id
                    WHERE customer_id = ? GROUP BY products.vendor_id, orders.time";
            $row = $db->prepQuery($sql, "i", user::userToCustomer());
            return $row;
        } catch (mysqli_sql_exception $e){
            error_log($e->getMessage());
        } finally {
            $db->close();
        }
    }

    public static function getAllOrders(){
        $db = new dbhandle();

        try{
            $sql = "SELECT orders.customer_id, products.vendor_id, SUM(total_price) AS 'total', count(*) AS 'items', orders.status FROM orders
                    JOIN products ON products.id = orders.product_id
                    GROUP BY products.vendor_id, orders.customer_id, orders.time";
            $row = $db->query($sql);
            return $row;
        } catch (mysqli_sql_exception $e){
            error_log($e->getMessage());
        } finally {
            $db->close();
        }
    }

    public static function vendorOrders(){
        $db = new dbhandle();
        try {
            $sql = "SELECT products.name as 'name',SUM(orders.total_price) AS 'total', count(*) AS 'items', orders.status, products.id, orders.time FROM orders
                    JOIN products ON products.id = orders.product_id
                    WHERE products.vendor_id = ? GROUP BY products.id, orders.time";
            $row = $db->prepQuery($sql, "i", user::userToVendor());
            return $row;
        } catch (mysqli_sql_exception $e) {
            error_log($e->getMessage());
        }
    }
    public static function updateVendorOrder($prodId, $state, $time) {
        if ($state != "pending") return;

        $db = new dbhandle();
        $sql = "UPDATE orders SET status='on_the_way' WHERE product_id=? AND time=?";
        $db->prepUpdate($sql, "is", $prodId, $time);
        $db->close();
    }

    public static function cancelOrder($prodId, $time) {
        $db = new dbhandle();
        $sql = "UPDATE orders SET status='cancelled' WHERE product_id=? AND time=?";
        $db->prepUpdate($sql, "is", $prodId, $time);
        $db->close();
    }

    public static function updateCustomerOrder($vendId, $state, $time) {
        if ($state != "on_the_way") return;

        $db = new dbhandle();

        $sql = "SELECT orders.status FROM orders JOIN products ON products.id=orders.product_id WHERE products.vendor_id=? AND orders.time=?";
        $result = $db->prepQuery($sql, "is", $vendId, $time);

        $equalState = true;
        $firstState = $result[0]["status"];
        foreach ($result as $row) {
            if ($row["status"] !== $firstState) $equalState = false;
        }

        if (!$equalState) return;

        // ignore
        $sql = "UPDATE orders SET status='delivered' WHERE product_id in (SELECT id FROM products WHERE vendor_id=?) AND time=?";
        $db->prepUpdate($sql, "is", $vendId, $time);
    }
}
?>