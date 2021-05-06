<?php

class API {
    

    /**
     * Static varibles to be used throughout the API class
     */
    public static $errors = array();
    public static $products = array();
    public static $categories = array(
        'mens clothing',
        'jewelery',
        'electronics',
        'womens clothing'
    );


    /**
     * Main class
     * Try / Catch query validation through validate functions
     * Setting products array to all products if query is NOT set
     * Setting products array to CATEGORY and or SHOW limit if query IS set
     * Printing array or error 
     */
    public static function main(){
        
        $show = self::query('show');
        $category = self::query('category');
        
        try{
            !self::validateCategory($category);
        } catch (exception $e) {
            $error = $e->getMessage();
            array_push(self::$errors, $error);
        }
        
        try{
            !self::validateShow($show);
        } catch (exception $e) {
            $error = $e->getMessage();
            array_push(self::$errors, $error);
        }
            
        self::$products = self::getProducts();
        if($category) self::$products = self::getCategory($category);        
        if($show) self::$products = self::getAmount($show);
        
        $data = self::$errors ? self::$errors : self::$products;

        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
    

    /**
     * Fetching PHP array of all products 
     * Returning array
     */
    public static function getProducts(){        
        include("productsArray.php");
        return $productsArray;
    }


    /**
     * Saving initial products to a new array to randomize through shuffle
     * Splicing array depending on value of show
     * Returning the new array
     */
    public static function getAmount($show){

        $randomizeArray = self::$products;   

        shuffle($randomizeArray);
        array_splice($randomizeArray, $show); 

        return $randomizeArray;    
    }


    /**
     * New array to save the values of selected category index
     * Looping through the products to find corresponding categories
     * Returning the new array  
     */
    public static function getCategory($category){

        $selectedCategory = array();
  
        foreach(self::$products as $key => $product){
            if($product['category'] === $category){
                array_push($selectedCategory, $product);
            }
        }

        return $selectedCategory;
    }
    

    /**
     * Checks if category is in query and tries to match it with the categories array
     * Throws exception if not true
     */
    public static function validateCategory($category){
        
        if($category && !in_array($category, self::$categories)){
            throw new exception("Category not found!");
        }
    }


    /**
     * Checks to see if show is a number
     * Checks to see if between 1 and 20
     * Throws exception if not true
     */
    public static function validateShow($show){
        
        if($show && !is_numeric($show)){
            throw new exception("Must be a number!");
        }
        elseif($show && ($show < 1 || $show > 20)){
            throw new exception("Must be a number between 1 and 20!");
        }
    }

    /**
     * Returns query string IF set
     * Returns false if query is NOT set
     */
    public static function query($query){
        return isset($_GET[$query]) ? filter_var($_GET[$query], FILTER_SANITIZE_STRING) : false;
    }
}

?>