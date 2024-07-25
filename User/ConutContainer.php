<script src="https://code.jquery.com/jquery-latest.min.js"></script>
<script>
   $(document).ready(function(){
   window.history.replaceState("","",window.location.href)
   });
</script>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['conut_additive_id']) && isset($_POST['total_price']) && !isset($_SESSION['order_id'])) {
    $User_id = $_SESSION['user_id'];
    $sqlInsertOrder = "INSERT INTO OrderList (user_id, order_state) VALUES ('$User_id', 'on card')";

    $stmtInsertOrder = mysqli_query($connect, $sqlInsertOrder);
    
    if ($stmtInsertOrder) {
        
        $lastInsertId = mysqli_insert_id($connect);
        $_SESSION['order_id'] = $lastInsertId;
        
        $conut_additive_id = $_POST['conut_additive_id'];
        $total_price = $_POST['total_price'];

        $sqlInsertContainer = "INSERT INTO ConutContainer (order_id, conut_additive_id, quantity, unit_price , total_price) VALUES ('$lastInsertId', '$conut_additive_id','1', '$total_price' , '$total_price')";
        $stmtInsertContainer = mysqli_query($connect, $sqlInsertContainer);
    }

} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['conut_additive_id']) && isset($_POST['total_price']) && isset($_SESSION['order_id'])) {
   
    $order_id = $_SESSION['order_id'];
    $conut_additive_id = $_POST['conut_additive_id'];
    $total_price = $_POST['total_price'];

    $sqlInsertContainer = "INSERT INTO ConutContainer (order_id, conut_additive_id, quantity, unit_price , total_price) VALUES ('$order_id', '$conut_additive_id','1', '$total_price' , '$total_price')";
    $stmtInsertContainer = mysqli_query($connect, $sqlInsertContainer);
}
?>






<?php
if (isset($_SESSION['order_id']) && isset($_SESSION['Conutorder'])) {
    $order_id = $_SESSION['order_id'];

    $sql = "SELECT order_id, conut_additive_id, quantity, unit_price , total_price
            FROM ConutContainer 
            WHERE order_id = $order_id";

    $result = mysqli_query($connect, $sql);


    if ($result) { ?>

            <div class="containerXY">
        <?php
        // to make the containers
            while ($row = mysqli_fetch_assoc($result)) {
                $row_id =  $row['conut_additive_id'] ;

                
?>
                <div class="containerX">


                    <!-- to print the image and conut name -->
                    <?php
                        $img_name_sql = "SELECT c.image, ca.conut_name
                                        FROM conut c
                                        JOIN conutadditive ca ON c.conut_name = ca.conut_name
                                        WHERE ca.conut_additive_id = $row_id ";

                        $img_name_stmt = mysqli_query($connect, $img_name_sql);
                        $data = mysqli_fetch_assoc($img_name_stmt);

                        $img = $data['image'];
                        $name = $data['conut_name'];
                        $imageType = get_image_type($img);
                    ?>
                    <div class="containerY" >
                        <h3> <?= $name ?></h3>
                    <?php if ($data) { ?>
                        <?php if ($imageType == 'jpeg') { ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($img) ?>" alt="<?= $name ?>" height="40" width="40">
                        <?php } elseif ($imageType == 'png') { ?>
                            <img src="data:image/png;base64,<?= base64_encode($img) ?>" alt="<?= $name ?>" height="40" width="40">
                        <?php } ?>
                    <?php } ?>

                        </div>



                        
                        <!-- Function to fetch topping and spread information -->
                        <?php displayToppingSpreadInfo($connect, 'conut', $row_id); ?>
                        
                        <h4>quantity: <?= $row['quantity'] ?></h4>
                        <h3>Total Price: <?= $row['total_price'] .'$' ?></h3>

                            <!-- all  buttons -->


                        <div class="quantity-buttons">
                            <form method="POST" action="">

                                <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                                <input type="hidden" name="conut_additive_id" value="<?= $row['conut_additive_id'] ?>">
                                <button class="quantity-btn" type="submit" name="increase">+</button>
                            </form>
                            <form method="POST" action="">

                                <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                                <input type="hidden" name="conut_additive_id" value="<?= $row['conut_additive_id'] ?>">
                                <button class="quantity-btn" type="submit" name="decrease">-</button>
                            </form>
                            <form method="POST" action="">
                            <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                            <input type="hidden" name="conut_additive_id" value="<?= $row['conut_additive_id'] ?>">
                            <button class="delete-btn" name="delete" onclick="return confirm('Are you sure you want to delete this item?')">Delete</button>
                        </form>
                    
                        </div>

                        
                </div>
<?php
            }
?>
            </div>
<?php
        }
}



?>










<!-- delete - Increase quantity  -  Decrease quantity  -->
<?php
if (isset($_POST['order_id']) && isset($_POST['conut_additive_id']) ) {
    $order_id = $_POST['order_id'];
    $conut_additive_id = $_POST['conut_additive_id'];

    if (isset($_POST['increase']) ) {
        // Increase quantity
        $sqlincrease = "UPDATE ConutContainer SET quantity = quantity + 1 WHERE conut_additive_id = $conut_additive_id AND order_id = '$order_id' AND quantity >= 0";
        mysqli_query($connect, $sqlincrease);
        ?>
        <script>location.reload()</script>
        <?php

    } elseif (isset($_POST['decrease'])) {
        // Decrease quantity
        $sqldecrease = "UPDATE ConutContainer SET quantity = quantity - 1 WHERE conut_additive_id = $conut_additive_id AND order_id = '$order_id' AND quantity > 0";
        mysqli_query($connect, $sqldecrease);
        ?>
        <script>location.reload()</script>
        <?php
        
    } elseif (isset($_POST['delete'])) {
        // Delete the item
        $sqldelete = "DELETE FROM ConutContainer WHERE conut_additive_id = '$conut_additive_id' AND order_id = '$order_id'";
        mysqli_query($connect, $sqldelete);
        ?>
        <script>location.reload()</script>
        <?php

        
        


    }
    if (isset($_POST['increase']) || isset($_POST['decrease']))  {
        // Fetch the updated quantity
        $quantityQuery = "SELECT quantity , unit_price FROM ConutContainer WHERE conut_additive_id = $conut_additive_id AND order_id = '$order_id'";
        $quantityResult = mysqli_query($connect, $quantityQuery);

        if ($quantityResult) {
            $row = mysqli_fetch_assoc($quantityResult);
            $currentQuantity = $row['quantity'];
            $unit_price = $row['unit_price'];

            // Calculate the new total price based on the current quantity and unit price
            $newTotalPrice = $unit_price * $currentQuantity;

            // Update the total price in the database
            $priceUpdate = "UPDATE ConutContainer SET total_price = $newTotalPrice WHERE conut_additive_id = $conut_additive_id AND order_id = '$order_id' AND quantity >= 0";
            mysqli_query($connect, $priceUpdate);
        }

        }

}
?>









