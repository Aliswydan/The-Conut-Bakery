
<!----------------------------------------------------------- Toppings Chimeny ----------------------------------------------------------->

<?php

if (isset($_POST['chimney_additive_id']) && isset($_POST['topping_name']) && isset($_POST['increment'])) {
    $chimneyId = mysqli_real_escape_string($connect, $_POST['chimney_additive_id']);
    $toppingName = mysqli_real_escape_string($connect, $_POST['topping_name']);

    $check = "SELECT quantity FROM toppingadditiveChimney WHERE chimney_additive_id = $chimneyId AND topping_name = '$toppingName'";
    $checkTopping = mysqli_query($connect, $check);

    if ($checkTopping) {
        if (mysqli_num_rows($checkTopping) > 0) {
            $row = mysqli_fetch_assoc($checkTopping);
            $quantity = $row['quantity'] + 1;

            // Update the quantity in the database
            $updateQuery = "UPDATE toppingadditiveChimney SET quantity = $quantity WHERE chimney_additive_id = $chimneyId AND topping_name = '$toppingName'";
            mysqli_query($connect, $updateQuery);
        } else {
            // Topping not ordered yet, insert a new row
            $query = "INSERT INTO toppingadditiveChimney (chimney_additive_id, topping_name, quantity) VALUES ('$chimneyId', '$toppingName', '1')";
            mysqli_query($connect, $query);
        }
    }
}
if (isset($_POST['chimney_additive_id']) && isset($_POST['topping_name']) && isset($_POST['decrement']) ) {
    $chimneyId = mysqli_real_escape_string($connect, $_POST['chimney_additive_id']);
    $toppingName = mysqli_real_escape_string($connect, $_POST['topping_name']);
    $query = "UPDATE toppingadditiveChimney SET quantity = quantity - 1 WHERE chimney_additive_id = $chimneyId AND topping_name = '$toppingName' AND quantity > 0";
    mysqli_query($connect, $query);
}

?>




<!-- Add a search bar above the spread table -->
<div class="search-bar" id="s2">
    <form method="get" action="Chimneys.php#s2">
        <input type="text" name="searchTopping" placeholder="Search for a Topping">
        <button type="submit">Search</button>
    </form>
</div>


<div class="toppingtable">
    <?php
    $toppingquery = "SELECT * FROM Topping";
    $toppingresult = mysqli_query($connect, $toppingquery);

    if ($toppingresult && mysqli_num_rows($toppingresult) > 0) :
    ?>
        <table>
            <caption>The <span>TOPPINGS</span> chimney</caption>
            <tr>
                <?php
                $columnchimney = 0;
                while ($row = mysqli_fetch_assoc($toppingresult)) {
                    $toppingName= $row['topping_name']  ;
                    $searchTerm = isset($_GET['searchTopping']) ? ucwords($_GET['searchTopping']) : '';
                    $highlightClass = $searchTerm && strpos($toppingName, $searchTerm) !== false ? 'searched' : '';
                ?>
                    <td>
                        <?php if(isset($_SESSION['chimney_additive_id'])) { ?>
                            <form method="post" action="Chimneys.php#s2">
                                <input type="hidden" name="chimney_additive_id" value="<?= $_SESSION['chimney_additive_id'] ?>">
                                <input type="hidden" name="topping_name" value="<?= $row['topping_name'] ?>">
                                <button class="btntd <?= $highlightClass ?>" type="submit" name="increment"><?= $row['topping_name'] ?></button>
                            </form>
                        <?php } else { ?>
                            <button class="btntd <?= $highlightClass ?>" type="submit"><?= $row['topping_name'] ?></button>
                        <?php } ?>
                    </td>
                <?php
                    $columnchimney++;
                    if ($columnchimney % 7 == 0) {
                        echo '</tr><tr>';
                    }
                }
                ?>
            </tr>
        </table>
    <?php else : ?>
        <p>No toppings available.</p>
    <?php endif; ?>
</div>


</div>

