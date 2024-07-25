
<?php

if (isset($_POST['chimney_additive_id']) && isset($_POST['spread_name']) && isset($_POST['increment']) ) {
    $chimneyId = mysqli_real_escape_string($connect, $_POST['chimney_additive_id']);
    $spreadName = mysqli_real_escape_string($connect, $_POST['spread_name']);

	$check = "SELECT quantity FROM spreadadditivechimney WHERE chimney_additive_id = $chimneyId AND spread_name = '$spreadName'";
	$checkspread = mysqli_query($connect, $check);
	
	if ($checkspread) {
		if (mysqli_num_rows($checkspread) > 0) {
			$row = mysqli_fetch_assoc($checkspread);
			$quantity = $row['quantity'] + 1;
	
			// Update the quantity in the database
			$updateQuery = "UPDATE spreadadditivechimney SET quantity = $quantity WHERE chimney_additive_id = $chimneyId AND spread_name = '$spreadName'";
			mysqli_query($connect, $updateQuery);
		} else {
			// Spread not ordered yet, insert a new row
			$query = "INSERT INTO spreadadditivechimney (chimney_additive_id, spread_name, quantity) VALUES ('$chimneyId', '$spreadName', '1')";
			mysqli_query($connect, $query);
		}
	} 
	

}

if (isset($_POST['chimney_additive_id']) && isset($_POST['spread_name']) && isset($_POST['decrement']) ) {
    $chimneyId = mysqli_real_escape_string($connect, $_POST['chimney_additive_id']);
    $spreadName = mysqli_real_escape_string($connect, $_POST['spread_name']);
    $query = "UPDATE spreadadditivechimney SET quantity = quantity - 1 WHERE chimney_additive_id = $chimneyId AND spread_name = '$spreadName' AND quantity > 0";
    mysqli_query($connect, $query);
}


?>




<!-- Add a search bar above the spread table -->
<div class="search-bar" id="s1">
    <form method="get" action="Chimneys.php#s1">
        <input type="text" name="searchSpread" placeholder="Search for a spread">
        <button type="submit">Search</button>
    </form>
</div>

<div class="spreadtable" >
    <?php
    $spreadquery = "SELECT * FROM Spread";
    $spreadresult = mysqli_query($connect, $spreadquery);

    if ($spreadresult && mysqli_num_rows($spreadresult) > 0) :
        $row = mysqli_fetch_assoc($spreadresult);
    ?>
        <table>
            <caption>The <span>SPREADS</span> chimney <span> &nbsp;/ <?= $row['price'] ?> $ </span></caption>
            <tr>
                <?php
                $spreadquery = "SELECT * FROM Spread";
                $spreadresult = mysqli_query($connect, $spreadquery);
                while ($row = mysqli_fetch_assoc($spreadresult)) :
                    $spreadName = $row['spread_name'];
                    $searchTerm = isset($_GET['searchSpread']) ? ucwords($_GET['searchSpread']) : '';
                    $highlightClass = $searchTerm && strpos($spreadName, $searchTerm) !== false ? 'searched' : '';
                ?>
                    <td>
                        <?php if (isset($_SESSION['chimney_additive_id'])) : ?>
                            <form method="post" action="Chimneys.php#s1">
                                <input type="hidden" name="chimney_additive_id" value="<?= $_SESSION['chimney_additive_id'] ?>">
                                <input type="hidden" name="spread_name" value="<?= $spreadName ?>">
                                <button class="btntd <?= $highlightClass ?>" type="submit" name="increment"><?= $spreadName ?></button>
                            </form>
                        <?php else : ?>
                            <button class="btntd <?= $highlightClass ?>" type="submit"><?= $spreadName ?></button>
                        <?php endif; ?>
                    </td>
                <?php endwhile; ?>
            </tr>
        </table>
    <?php else : ?>
        <p>No spread available.</p>
    <?php endif; ?>
</div>
