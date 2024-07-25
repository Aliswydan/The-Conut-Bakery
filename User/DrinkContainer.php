<?php
include_once 'connect_to_server_and_database.php';

?>

<script src="https://code.jquery.com/jquery-latest.min.js"></script>
<script>
   $(document).ready(function(){
       window.history.replaceState("","",window.location.href)
   });
</script>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['drink_additive_id']) && isset($_POST['total_price']) && !isset($_SESSION['order_id'])) {
    $user_id = $_SESSION['user_id'];
    $sqlInsertOrder = "INSERT INTO OrderList (user_id, order_state) VALUES ('$user_id', 'on card')";

    $stmtInsertOrder = mysqli_query($connect, $sqlInsertOrder);
    
    if ($stmtInsertOrder) {
        
        $lastInsertId = mysqli_insert_id($connect);
        $_SESSION['order_id'] = $lastInsertId;
        
        $drink_additive_id = $_POST['drink_additive_id'];
        $total_price = $_POST['total_price'];

        $sqlInsertContainer = "INSERT INTO DrinkContainer (order_id, drink_additive_id, quantity, unit_price, total_price) VALUES ('$lastInsertId', '$drink_additive_id', '1', '$total_price', '$total_price')";
        $stmtInsertContainer = mysqli_query($connect, $sqlInsertContainer);
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['drink_additive_id']) && isset($_POST['total_price']) && isset($_SESSION['order_id'])) {
   
    $order_id = $_SESSION['order_id'];
    $drink_additive_id = $_POST['drink_additive_id'];
    $total_price = $_POST['total_price'];

    $sqlInsertContainer = "INSERT INTO DrinkContainer (order_id, drink_additive_id, quantity, unit_price, total_price) VALUES ('$order_id', '$drink_additive_id', '1', '$total_price', '$total_price')";
    $stmtInsertContainer = mysqli_query($connect, $sqlInsertContainer);
}
?>

<?php
if (isset($_SESSION['order_id'])  && isset($_SESSION['Drinkorder']) ) {
    $order_id = $_SESSION['order_id'];

    $sql = "SELECT order_id, drink_additive_id, quantity, unit_price, total_price
            FROM DrinkContainer 
            WHERE order_id = $order_id";

    $result = mysqli_query($connect, $sql);

    if ($result) { ?>
        <div class="containerXY">
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                $row_id = $row['drink_additive_id'];

                ?>
                <div class="containerX">
                    <?php
                        $img_name_sql = "SELECT d.image, da.drink_name
                                        FROM drink d
                                        JOIN drinkadditive da ON d.drink_name = da.drink_name
                                        WHERE da.drink_additive_id = $row_id ";

                        $img_name_stmt = mysqli_query($connect, $img_name_sql);
                        $data = mysqli_fetch_assoc($img_name_stmt);

                        $img = $data['image'];
                        $name = $data['drink_name'];
                        $imageType = get_image_type($img);
                    ?>
                    <div class="containerY">
                        <h3> <?= $name ?></h3>
                        <?php if ($data) { ?>
                            <?php if ($imageType == 'jpeg') { ?>
                                <img src="data:image/jpeg;base64,<?= base64_encode($img) ?>" alt="<?= $name ?>" height="40" width="40">
                            <?php } elseif ($imageType == 'png') { ?>
                                <img src="data:image/png;base64,<?= base64_encode($img) ?>" alt="<?= $name ?>" height="40" width="40">
                            <?php } ?>
                        <?php } ?>
                    </div>

                    <?php displayToppingSpreadInfo($connect, 'drink', $row_id); ?>

                    <h4>quantity: <?= $row['quantity'] ?></h4>
                    <h3>Total Price: <?= $row['total_price'] .'$' ?></h3>

                    <div class="quantity-buttons">
                        <form method="POST" action="">
                            <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                            <input type="hidden" name="drink_additive_id" value="<?= $row['drink_additive_id'] ?>">
                            <button class="quantity-btn" type="submit" name="increase">+</button>
                        </form>
                        <form method="POST" action="">
                            <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                            <input type="hidden" name="drink_additive_id" value="<?= $row['drink_additive_id'] ?>">
                            <button class="quantity-btn" type="submit" name="decrease">-</button>
                        </form>
                        <form method="POST" action="">
                            <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                            <input type="hidden" name="drink_additive_id" value="<?= $row['drink_additive_id'] ?>">
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



if (isset($_POST['order_id']) && isset($_POST['drink_additive_id']) ) {
    $order_id = $_POST['order_id'];
    $drink_additive_id = $_POST['drink_additive_id'];

    if (isset($_POST['increase'])) {
        $sqlincrease = "UPDATE DrinkContainer SET quantity = quantity + 1 WHERE drink_additive_id = $drink_additive_id AND order_id = '$order_id' AND quantity >= 0";
        mysqli_query($connect, $sqlincrease);
        ?>
        <script>location.reload()</script>
        <?php

    } elseif (isset($_POST['decrease'])) {
        $sqldecrease = "UPDATE DrinkContainer SET quantity = quantity - 1 WHERE drink_additive_id = $drink_additive_id AND order_id = '$order_id' AND quantity > 0";
        mysqli_query($connect, $sqldecrease);
        ?>
        <script>location.reload()</script>
        <?php

    } elseif (isset($_POST['delete'])) {
        $sqldelete = "DELETE FROM DrinkContainer WHERE drink_additive_id = '$drink_additive_id' AND order_id = '$order_id'";
        mysqli_query($connect, $sqldelete);
        ?>
        <script>location.reload()</script>
        <?php

    }

    if (isset($_POST['increase']) || isset($_POST['decrease'])) {
        $quantityQuery = "SELECT quantity, unit_price FROM DrinkContainer WHERE drink_additive_id = $drink_additive_id AND order_id = '$order_id'";
        $quantityResult = mysqli_query($connect, $quantityQuery);

        if ($quantityResult) {
            $row = mysqli_fetch_assoc($quantityResult);
            $currentQuantity = $row['quantity'];
            $unit_price = $row['unit_price'];

            $newTotalPrice = $unit_price * $currentQuantity;

            $priceUpdate = "UPDATE DrinkContainer SET total_price = $newTotalPrice WHERE drink_additive_id = $drink_additive_id AND order_id = '$order_id' AND quantity >= 0";
            mysqli_query($connect, $priceUpdate);
        }
    }
}


?>
