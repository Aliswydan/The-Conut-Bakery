
<!----------------------------------------------------------- Toppings_conut ----------------------------------------------------------->

<?php


if (isset($_POST['conut_additive_id']) && isset($_POST['topping_name']) && isset($_POST['increment'])) {
    $conutId = mysqli_real_escape_string($connect, $_POST['conut_additive_id']);
    $toppingName = mysqli_real_escape_string($connect, $_POST['topping_name']);

    $check = "SELECT quantity FROM toppingadditivecount WHERE conut_additive_id = $conutId AND topping_name = '$toppingName'";
    $checkTopping = mysqli_query($connect, $check);

    if ($checkTopping) {
        if (mysqli_num_rows($checkTopping) > 0) {
            $row = mysqli_fetch_assoc($checkTopping);
            $quantity = $row['quantity'] + 1;

            // Update the quantity in the database
            $updateQuery = "UPDATE toppingadditivecount SET quantity = $quantity WHERE conut_additive_id = $conutId AND topping_name = '$toppingName'";
            mysqli_query($connect, $updateQuery);
        } else {
            // Topping not ordered yet, insert a new row
            $query = "INSERT INTO toppingadditivecount (conut_additive_id, topping_name, quantity) VALUES ('$conutId', '$toppingName', '1')";
            mysqli_query($connect, $query);
        }
    }
}
if (isset($_POST['conut_additive_id']) && isset($_POST['topping_name']) && isset($_POST['decrement']) ) {
    $conutId = mysqli_real_escape_string($connect, $_POST['conut_additive_id']);
    $toppingName = mysqli_real_escape_string($connect, $_POST['topping_name']);
    $query = "UPDATE toppingadditivecount SET quantity = quantity - 1 WHERE conut_additive_id = $conutId AND topping_name = '$toppingName' AND quantity > 0";
    mysqli_query($connect, $query);
}

?>



<!-- Add a search bar above the spread table -->
<div class="search-bar" id="s2">
    <form method="get" action="Conuts.php#s2">
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
            <caption>The <span>TOPPINGS</span> Conut</caption>
            <tr>
                <?php
                $columnCount = 0;
                while ($row = mysqli_fetch_assoc($toppingresult)) {
                    $toppingName= $row['topping_name']  ;
                    $searchTerm = isset($_GET['searchTopping']) ? ucwords($_GET['searchTopping']) : '';

                    $highlightClass = $searchTerm && strpos($toppingName, $searchTerm) !== false ? 'searched' : '';
                ?>
                    <td>
                        <?php if(isset($_SESSION['conut_additive_id'])) { ?>
                            <form method="post" action="Conuts.php#s2">
                                <input type="hidden" name="conut_additive_id" value="<?= $_SESSION['conut_additive_id'] ?>">
                                <input type="hidden" name="topping_name" value="<?= $row['topping_name'] ?>">
                                <button class="btntd <?= $highlightClass ?>" type="submit" name="increment"><?= $row['topping_name'] ?></button>
                            </form>
                        <?php } else { ?>
                            <button class="btntd <?= $highlightClass ?>" type="submit"><?= $row['topping_name'] ?></button>
                        <?php } ?>
                    </td>
                <?php
                    $columnCount++;
                    if ($columnCount % 7 == 0) {
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

