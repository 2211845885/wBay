<?php
include_once("dbaccess.php");

class ratinghandle {
   
   // Function to add a new rating for a specific product
   public static function addRating($rating, $product_id){
      if(self::hasRated($product_id)){
         self::updateRating($rating, $product_id);
         return;
      }
  
      // Creating a new database object
      $db = new dbhandle();

      try {
         // SQL query to insert a new rating into the 'rating' table
         $sql = "INSERT INTO rating (rating, product_id, customer_id) VALUES (?, ?, ?)";
         
         // Executing the query with the appropriate types for the parameters
         
         $db->prepUpdate($sql, "dii", $rating, $product_id, user::userToCustomer()); 

      } catch (mysqli_sql_exception $e) {
         // If an error occurs, the error message will be displayed
         echo "Error: " . $e->getMessage();
      }

      // Closing the database connection after completing the operation
      $db->close();
   }

   // Function to retrieve the average rating for a product
   public static function getRating($product_id){
      
      // Creating a new database object
      $db = new dbhandle();
      
      // Defining a variable to store the average rating
      $average_rating = 0;

      try {
         // SQL query to calculate the average rating for a product
         $sql = "SELECT AVG(rating) as average_rating FROM rating WHERE product_id = ?";

         // Executing the query with the product_id parameter
         $row = $db->prepQuery($sql, "i", $product_id);
         
         // Storing the average rating value in the variable
         $average_rating = $row[0]['average_rating'];
      } catch (mysqli_sql_exception $e) {
         // If an error occurs, the error message will be displayed
         echo "Error: " . $e->getMessage(); 
      }

      // Closing the database connection after completing the operation
      $db->close();
      
      // Returning the average rating
      return $average_rating;
   }

   public static function hasRated($product_id){
      $db = new dbhandle();
      try {
         $sql = "SELECT customer_id FROM rating where customer_id = ? AND product_id = ?";
         $row = $db->prepQuery($sql, "ii", user::userToCustomer(), $product_id);
         if(count($row) > 0){
            return true;
         } else {
            return false;
         }

      } catch (mysqli_sql_exception $e){
         error_log($e->getMessage());
      } finally {
         $db->close();
      }
   }

   public static function updateRating($rating, $product_id){
      $db = new dbhandle();
      try {
         $sql = "UPDATE rating SET rating = ? WHERE product_id = ? AND customer_id = ?";
         $db->prepUpdate($sql, "dii", $rating, $product_id, user::userToCustomer());
      } catch (mysqli_sql_exception $e){
         error_log($e->getMessage());
      } finally {
         $db->close();
      }
   }
}
?>