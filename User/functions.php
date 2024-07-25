
<?php
function get_image_type($binary_data)
{
	$mime_types = array(
		'jpeg' => "\xFF\xD8\xFF",
		'png' => "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A",
	);
	
foreach ($mime_types as $type => $header) {
	if (substr($binary_data, 0, strlen($header)) === $header) {
return $type;
}
}

return null;  // Unknown image type
}


//                   get total price of chimney
function get_price_chimney($chimney_additive_id, $connect){
    $sql1 = "SELECT chimney_name FROM ChimneyAdditive WHERE chimney_additive_id = $chimney_additive_id ;";
    $sqlname = mysqli_query($connect, $sql1);

    if ($sqlname) {
        $row = mysqli_fetch_assoc($sqlname);
        $chimney_name = $row['chimney_name'];

        $sql2 = "SELECT price FROM chimney WHERE chimney_name = '$chimney_name' ;";
        $sqlpricechimney = mysqli_query($connect, $sql2);

        
        if ($sqlpricechimney) {
            $row = mysqli_fetch_assoc($sqlpricechimney);
            $pricechimney = $row['price'];
          
// <!------------------ spread ----------------------------------------------------------->
        
        
        $sql3 = "SELECT * FROM Spread;"; 
        $sqlpricespread = mysqli_query($connect, $sql3);

        if ($sqlpricespread) {
            $row = mysqli_fetch_assoc($sqlpricespread);
            $priceSpread = $row['price'];




            $sql4 = "SELECT sum(quantity) as sumq FROM SpreadAdditiveChimney WHERE chimney_additive_id = $chimney_additive_id ;"; 
            $sqlchimney = mysqli_query($connect, $sql4);
    
            if ($sqlchimney) {
                $row = mysqli_fetch_assoc($sqlchimney);
                $quantity = $row['sumq'] ;
                if($quantity <= 1){
                    $totalSpreadPrice = 0;
                }
                else{
                    $totalSpreadPrice =  ($quantity -1)  * $priceSpread;
                    
                }

                                    }

                            }  

// <!-------------- topping ----------------------------------------------------------->


                            $sql5 = "SELECT * FROM Topping ;"; 
                            $sqlpricetopping = mysqli_query($connect, $sql5);
                            

                            if ($sqlpricetopping) {
                                $row = mysqli_fetch_assoc($sqlpricetopping);
                                $priceTopping = $row['price'];
                    
                    
                    
                    
                                $sql6 = "SELECT sum(quantity) as sumq FROM ToppingAdditiveChimney WHERE chimney_additive_id = $chimney_additive_id ;"; 
                                $sqlchimney = mysqli_query($connect, $sql6);
                        
                                if ($sqlchimney) {
                                    $row = mysqli_fetch_assoc($sqlchimney);
                                    $quantity = $row['sumq'] ;

                                    if($quantity <= 2){
                                        $totalToppingPrice = 0;
                                    }
                                    else{
                                        
                                        $totalToppingPrice = ($quantity -2 ) * $priceTopping;
                                    }
                    
                                                        }
                    
                                                }  

                            }

        return  $pricechimney + $totalSpreadPrice + $totalToppingPrice  ;
}

}









//                   get total price of count

function get_price_conut($conut_additive_id, $connect){
    $sql1 = "SELECT conut_name FROM ConutAdditive WHERE conut_additive_id = $conut_additive_id ;";
    $sqlname = mysqli_query($connect, $sql1);

    if ($sqlname) {
        $row = mysqli_fetch_assoc($sqlname);
        $conut_name = $row['conut_name'];

        $sql2 = "SELECT price FROM Conut WHERE conut_name = '$conut_name' ;";
        $sqlpriceconut = mysqli_query($connect, $sql2);

        
        if ($sqlpriceconut) {
            $row = mysqli_fetch_assoc($sqlpriceconut);
            $priceConut = $row['price'];
          
// <!----------------- spread ----------------------------------------------------------->
        
        
        $sql3 = "SELECT * FROM Spread ;"; 
        $sqlpricespread = mysqli_query($connect, $sql3);

        if ($sqlpricespread) {
            $row = mysqli_fetch_assoc($sqlpricespread);
            $priceSpread = $row['price'];




            $sql4 = "SELECT sum(quantity) as sumq FROM SpreadAdditiveCount WHERE conut_additive_id = $conut_additive_id ;"; 
            $sqlcount = mysqli_query($connect, $sql4);
    
            if ($sqlcount) {
                $row = mysqli_fetch_assoc($sqlcount);
                $quantity = $row['sumq'] ;
                if($quantity <= 1){
                    $totalSpreadPrice = 0;
                }
                else{
                    $totalSpreadPrice =  ($quantity -1)  * $priceSpread;
                    
                }

                                    }

                            }  

// <!------------- topping ----------------------------------------------------------->


                            $sql5 = "SELECT * FROM Topping ;"; 
                            $sqlpricetopping = mysqli_query($connect, $sql5);
                    
                            if ($sqlpricetopping) {
                                $row = mysqli_fetch_assoc($sqlpricetopping);
                                $priceTopping = $row['price'];
                    
                    
                    
                    
                                $sql6 = "SELECT sum(quantity) as sumq FROM ToppingAdditiveCount WHERE conut_additive_id = $conut_additive_id ;"; 
                                $sqlcount = mysqli_query($connect, $sql6);
                        
                                if ($sqlcount) {
                                    $row = mysqli_fetch_assoc($sqlcount);
                                    $quantity = $row['sumq'] ;

                                    if($quantity <= 2){
                                        $totalToppingPrice = 0;
                                    }
                                    else{
                                        
                                        $totalToppingPrice = ($quantity -2 ) * $priceTopping;
                                    }
                    
                                                        }
                    
                                                }  

                            }

        return  $priceConut + $totalSpreadPrice + $totalToppingPrice ;
}

}





//                   get total price of drink

// Modified function for calculating the total price of a drink with toppings
function get_price_drink_with_toppings($drink_additive_id, $connect) {
    // Retrieve the drink name based on the drink_additive_id
    $sql_get_drink_name = "SELECT drink_name FROM drinkadditive WHERE drink_additive_id = $drink_additive_id";
    $result_drink_name = mysqli_query($connect, $sql_get_drink_name);

    if ($result_drink_name) {
        $row_drink_name = mysqli_fetch_assoc($result_drink_name);
        $drink_name = $row_drink_name['drink_name'];

        // Retrieve the base price of the drink
        $sql_get_base_price = "SELECT price FROM drink WHERE drink_name = '$drink_name'";
        $result_base_price = mysqli_query($connect, $sql_get_base_price);

        if ($result_base_price) {
            $row_base_price = mysqli_fetch_assoc($result_base_price);
            $base_price = (float) $row_base_price['price'];

            // Initialize total topping price
            $total_topping_price = 0;

            // Retrieve the price of a specific topping (e.g., 'Kinder')
            $sql_get_topping_price = "SELECT * FROM Topping";
            $result_topping_price = mysqli_query($connect, $sql_get_topping_price);

            if ($result_topping_price) {
                $row_topping_price = mysqli_fetch_assoc($result_topping_price);
                $topping_price = (float) $row_topping_price['price'];

                // Retrieve the total quantity of toppings for the drink
                $sql_get_total_quantity = "SELECT SUM(quantity) as total_quantity FROM ToppingAdditivedrink WHERE drink_additive_id = $drink_additive_id";
                $result_total_quantity = mysqli_query($connect, $sql_get_total_quantity);

                if ($result_total_quantity) {
                    $row_total_quantity = mysqli_fetch_assoc($result_total_quantity);
                    $total_quantity = (int) $row_total_quantity['total_quantity'];

                    // Calculate the total topping price based on quantity (minus 2)
                    if ($total_quantity > 2) {
                        $total_topping_price = ($total_quantity - 2) * $topping_price;
                    }
                }
            }

            // Calculate the total price (base price + total topping price)
            $total_price = $base_price + $total_topping_price;

            // Format the total price to two decimal places
            return number_format($total_price, 2);
        }
    }

    // Return null if any query fails
    return null;
}


?>





  <!----------------------------------------------------------- function for ordernow----------------------------------------------------------->

  <?php
function displayToppingSpreadInfo($connect, $container_type, $container_additive_id) {
    $topping_table = "";
    $spread_table = "";

    $topping_column = "";
    $spread_column = "";

    // Determine the table and column names based on container type
    switch ($container_type) {
        case 'chimney':
            $topping_table = "toppingadditivechimney";
            $spread_table = "spreadadditivechimney";
            $topping_column = "chimney_additive_id";
            $spread_column = "chimney_additive_id";
            break;
        
        case 'drink':
            $topping_table = "toppingadditivedrink";
            $topping_column = "drink_additive_id";
            // For drinks, there is no spread, so no need to set $spread_table and $spread_column
            break;

        case 'conut':
            $topping_table = "toppingadditivecount";
            $spread_table = "spreadadditivecount";
            $topping_column = "conut_additive_id";
            $spread_column = "conut_additive_id";
            break;

        // Add other cases for different container types as needed

        default:
            return; // Invalid container type
    }

    // Check if the container type is not 'drink' before proceeding with spread-related code
    if ($container_type !== 'drink') {
        $sql_find_spread = "SELECT spread_name FROM $spread_table WHERE $spread_column = $container_additive_id";

        $topping_spread_result = mysqli_query($connect, $sql_find_spread);

        $spread_row = mysqli_fetch_assoc($topping_spread_result);

        if ($spread_row) { ?>
            <h4> Spread: </h4> 
            <?php
            while ($spread_row !== null) {
                echo $spread_row['spread_name'] . '-';
                $spread_row = mysqli_fetch_assoc($topping_spread_result);
            }
        } else { ?>
            <h4> Spread: </h4> <?php echo 'no spread';
        }
    }

    // Continue with the rest of the code for both topping and drink

    $sql_find_topping = "SELECT topping_name FROM $topping_table WHERE $topping_column = $container_additive_id";

    $topping_topping_result = mysqli_query($connect, $sql_find_topping);

    $topping_row = mysqli_fetch_assoc($topping_topping_result);

    if ($topping_row) { ?> 
        <h4> Topping: </h4> 
        <?php
        while ($topping_row !== null) {
            echo $topping_row['topping_name'] . '-';
            $topping_row = mysqli_fetch_assoc($topping_topping_result);
        }
    } else { ?>
        <h4> Topping: </h4> <?php echo 'no topping';
    }
}
?>

    