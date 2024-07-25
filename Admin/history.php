<?php
session_start();
require_once 'connect_to_server_and_database.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link rel="stylesheet" type="text/css" href="CSS/history.css">
    <link rel="stylesheet" type="text/css" href="CSS/menu.css">
</head>

<body>
    <?php include 'menu_bar.php'; ?>
    <div class="menu">
        <?php 
          $selectedPeriod = 'daily';

          if ($_SERVER['REQUEST_METHOD'] === 'POST') {
           // Get the selected time period from the form submission
           $selectedPeriod = isset($_POST['time-period']) ? $_POST['time-period'] : 'daily';
       }
        ?>
    </div>
    <div id="order-history">
        <h1>Order History</h1>
        
        <div id="time-period-dropdown">
           <!-- Form for selecting time period -->
<form method="post">
    <label for="time-period">Select Time Period:</label>
    <select name="time-period" id="time-period" onchange="this.form.submit()">
        <option value="daily" <?php echo ($selectedPeriod === 'daily') ? 'selected' : ''; ?>>Daily</option>
        <option value="weekly" <?php echo ($selectedPeriod === 'weekly') ? 'selected' : ''; ?>>Weekly</option>
        <option value="monthly" <?php echo ($selectedPeriod === 'monthly') ? 'selected' : ''; ?>>Monthly</option>
    </select>
</form>

        </div>

        <?php
       
        $sql = "SELECT order_id, order_date, user_id FROM orderlist WHERE order_state = 'done' ";
        $result = $connect->query($sql);

        if ($result->num_rows > 0) {
            $orders = array();

            while ($row = $result->fetch_assoc()) {
                categorizeOrders($row, $orders, $selectedPeriod);
            }

            displayOrders($selectedPeriod, $orders);
        } else {
            echo "<p>No orders found</p>";
        }

        $connect->close();

        function categorizeOrders($order, &$orders, $selectedPeriod)
        {
            $orderDate = strtotime($order['order_date']);
            $currentDate = strtotime('today');

            if ($selectedPeriod == 'daily' && $orderDate >= $currentDate) {
                $orders[] = $order;
            } elseif ($selectedPeriod == 'weekly' && $orderDate >= strtotime('-1 week', $currentDate)) {
                $orders[] = $order;
            } elseif ($selectedPeriod == 'monthly' && $orderDate >= strtotime('-1 month', $currentDate)) {
                $orders[] = $order;
            }
        }

        function displayOrders($title, $orders)
        {
            echo "<h2>$title Orders</h2>";

            if (empty($orders)) {
                echo "<p>No orders for this period</p>";
            } else {
                echo "<table>";
                echo "<tr><th>Order ID</th><th>Order Date</th><th>User ID</th><th>Action</th></tr>";

                foreach ($orders as $order) {
                    echo "<tr>";
                    echo "<td>" . $order['order_id'] . "</td>";
                    echo "<td>" . $order['order_date'] . "</td>";
                    echo "<td>" . $order['user_id'] . "</td>";
                    echo "<td><a class=\"button-details\" href=\"order_details.php?order_id=" . $order['order_id'] . "\">Order Details</a></td>";
                    
                    echo "</tr>";
                }

                echo "</table>";
            }
        }
        ?>
    </div>
</body>

</html>
