<?php
require_once 'connect_to_server_and_database.php';
    

    if (isset($_POST['acceptOrder'])) {
        $orderId = $_POST['orderId'];
        updateOrderState($orderId, 'in-progress');
    } elseif (isset($_POST['rejectOrder'])) {
        $orderId = $_POST['orderId'];
        deleteOrder($orderId);
    } elseif (isset($_POST['markDone'])) {
        $orderId = $_POST['orderId'];
        updateOrderState($orderId, 'done');
    } 


function updateOrderState($orderId, $newState) {
    global $connect;

    $queryUpdateState = "UPDATE OrderList SET order_state = '$newState' WHERE order_id = $orderId";

    if (mysqli_query($connect, $queryUpdateState)) {
        
        // Move the header redirect here after a successful update
        header("Location: orders.php");
        exit();
    } else {
        echo "Error updating order state: " . mysqli_error($connect);
    }
}



function deleteOrder($orderId) {
    global $connect;

    // Delete associated records in ConutContainer table
    $queryDeleteConut = "DELETE FROM ConutContainer WHERE order_id = $orderId";
    mysqli_query($connect, $queryDeleteConut);

    // Delete associated records in ChimineyContainer table
    $queryDeleteChiminey = "DELETE FROM ChimneyContainer WHERE order_id = $orderId";
    mysqli_query($connect, $queryDeleteChiminey);

    // Delete associated records in DrinkContainer table
    $queryDeleteDrink = "DELETE FROM DrinkContainer WHERE order_id = $orderId";
    mysqli_query($connect, $queryDeleteDrink);

    // Now, delete the order
    $queryDeleteOrder = "DELETE FROM OrderList WHERE order_id = $orderId";
    if (mysqli_query($connect, $queryDeleteOrder)) {
        echo "Order deleted successfully";
    } else {
        echo "Error deleting order: " . mysqli_error($connect);
    }

    header("Location: orders.php"); 
    exit();
}


mysqli_close($connect);
?>
