<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Orders </title>
    <link rel="stylesheet" type="text/css" href="CSS/menu.css">
    <link rel="stylesheet" type="text/css" href="CSS/orders.css">

</head>

<body>

    <?php
        include 'menu_bar.php'
    ?>
<?php
require_once 'connect_to_server_and_database.php';

// Fetch orders in 'ordered' and 'in-progress' states
$queryGetOrders = "SELECT * FROM OrderList WHERE order_state IN ('ordered', 'in-progress')";
$result = mysqli_query($connect, $queryGetOrders);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Display order details and admin actions
        echo "<div class='order-container'>";
        echo "Order ID: " . $row['order_id'] . "<br>";
        echo "Order Date: " . $row['order_date'] . "<br>";
        echo "Order State: " . $row['order_state'] . "<br>";
        echo "<a href='order_details.php?orderId=" . $row['order_id'] . "'>View Order Details</a>";
        // Add buttons or forms for admin actions
        echo "<form action='admin_process_order.php' method='POST'>";
        echo "<input type='hidden' name='orderId' value='" . $row['order_id'] . "'>";

        if ($row['order_state'] === 'ordered') {
            echo "<button type='submit' name='acceptOrder'>Accept</button>";
            echo "<button type='submit' name='rejectOrder'>Reject</button>";
        } elseif ($row['order_state'] === 'in-progress') {
            echo "<button type='submit' name='markDone'>Mark as Done</button>";
        }

        echo "</form>";
        echo "</div>";
        echo "<hr>";
    }
} else {
    echo "Error retrieving orders: " . mysqli_error($connect);
}

// Close the database connection
mysqli_close($connect);
?>

   
    
    
</body>

</html>