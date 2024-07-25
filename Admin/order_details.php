<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" type="text/css" href="CSS/orderdetail.css">

</head>
<body>

<?php

require_once 'connect_to_server_and_database.php';

if (isset($_GET['orderId'])) {
    $orderId = $_GET['orderId'];

    // Fetch order details from OrderList table
    $queryGetOrderDetails = "SELECT oh.user_name, oh.phone_number, oh.address, ho.order_id, ho.user_id, ho.order_date, ho.total_order_price
     FROM orderlist ho JOIN user oh ON ho.user_id = oh.user_id WHERE order_id = $orderId";
    
    $resultOrderDetails = mysqli_query($connect, $queryGetOrderDetails);
    
    if ($resultOrderDetails) {
        $orderDetails = mysqli_fetch_assoc($resultOrderDetails);
    
        echo "Order ID: " . $orderDetails['order_id'] . "<br>";
        echo "User ID: " . $orderDetails['user_id'] . "<br>";
        echo "Order Date: " . $orderDetails['order_date'] . "<br>";
        echo "username:" .  $orderDetails['user_name'] . "<br>"; 
        echo "phone number:" .  $orderDetails['phone_number'] . "<br>";
        echo "adress:" .  $orderDetails['address'] . "<br>";
        echo "Total price: " . $orderDetails['total_order_price'] . "<br>";
        // Fetch details from related tables using JOIN operations
       
        $query = "SELECT 
        ol.user_id,
        ol.order_id, ol.order_date,
        chc.chimney_additive_id, ch.chimney_name, chc.quantity AS chimney_quantity,
        cac.conut_additive_id, cac.conut_name, cc.quantity AS conut_quantity,
        dac.drink_additive_id, dac.drink_name, dc.quantity AS drink_quantity,
        scac.spread_name AS conut_spread, tcac.topping_name AS conut_topping, tcac.quantity AS conut_topping_quantity,
        schcac.spread_name AS chimney_spread, tchcac.topping_name AS chimney_topping, tchcac.quantity AS chimney_topping_quantity,
        tdac.topping_name AS drink_topping
    FROM 
        ConutContainer cc
    LEFT JOIN 
        ConutAdditive cac ON cc.conut_additive_id = cac.conut_additive_id
    LEFT JOIN 
        SpreadAdditiveCount scac ON cc.conut_additive_id = scac.conut_additive_id
    LEFT JOIN 
        ToppingAdditiveCount tcac ON cc.conut_additive_id = tcac.conut_additive_id
    LEFT JOIN 
        ChimneyContainer chc ON cc.order_id = chc.order_id
    LEFT JOIN 
        ChimneyAdditive ch ON chc.chimney_additive_id = ch.chimney_additive_id
    LEFT JOIN 
        SpreadAdditiveChimney schcac ON chc.chimney_additive_id = schcac.chimney_additive_id
    LEFT JOIN 
        ToppingAdditiveChimney tchcac ON chc.chimney_additive_id = tchcac.chimney_additive_id
    LEFT JOIN 
        DrinkContainer dc ON cc.order_id = dc.order_id
    LEFT JOIN 
        DrinkAdditive dac ON dc.drink_additive_id = dac.drink_additive_id
    LEFT JOIN 
        ToppingAdditiveDrink tdac ON dc.drink_additive_id = tdac.drink_additive_id
    JOIN
        OrderList ol ON cc.order_id = ol.order_id
    WHERE 
        cc.order_id = $orderId
        AND (ch.chimney_name IS NOT NULL OR cac.conut_name IS NOT NULL OR dac.drink_name IS NOT NULL)";
    
    
    
    



        $result = mysqli_query($connect, $query);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
            
              
// Chimney details
if ($row['chimney_name'] !== null) {
    echo "Chimney Name: " . $row['chimney_name'] . " - Quantity: " . $row['chimney_quantity'] . "<br>";
}

// Conut details
if ($row['conut_name'] !== null) {
    echo "Conut Name: " . $row['conut_name'] . " - Quantity: " . $row['conut_quantity'] . "<br>";
}

// Drink details
if ($row['drink_name'] !== null) {
    echo "Drink Name: " . $row['drink_name'] . " - Quantity: " . $row['drink_quantity'] . "<br>";
}

// Conut Spread and Topping
if ($row['conut_spread'] !== null) {
    echo "Conut Spread: " . $row['conut_spread'] . " - Conut Topping: " . $row['conut_topping'] . "<br>";
}

// Chimney Spread and Topping
if ($row['chimney_spread'] !== null) {
    echo "Chimney Spread: " . $row['chimney_spread'] . " - Chimney Topping: " . $row['chimney_topping'] . "<br>";
}

// Drink Topping
if ($row['drink_topping'] !== null) {
    echo "Drink Topping: " . $row['drink_topping'] . "<br>";
}

                


            
        } 
    }else {
            echo "Error retrieving details: " . mysqli_error($connect);
        }

    } else {
        echo "Error retrieving order details: " . mysqli_error($connect);
    }
} else {
    echo "Invalid request. Order ID not provided.";
}

mysqli_close($connect);
?>

</body>
</html>
