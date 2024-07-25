

<?php

if (isset($_POST['conut_additive_id']) && isset($_POST['spread_name']) && isset($_POST['increment']) ) {
    $conutId = mysqli_real_escape_string($connect, $_POST['conut_additive_id']);
    $spreadName = mysqli_real_escape_string($connect, $_POST['spread_name']);

	$check = "SELECT quantity FROM spreadadditivecount WHERE conut_additive_id = $conutId AND spread_name = '$spreadName'";
	$checkspread = mysqli_query($connect, $check);
	
	if ($checkspread) {
		if (mysqli_num_rows($checkspread) > 0) {
			$row = mysqli_fetch_assoc($checkspread);
			$quantity = $row['quantity'] + 1;
	
			// Update the quantity in the database
			$updateQuery = "UPDATE spreadadditivecount SET quantity = $quantity WHERE conut_additive_id = $conutId AND spread_name = '$spreadName'";
			mysqli_query($connect, $updateQuery);
		} else {
			// Spread not ordered yet, insert a new row
			$query = "INSERT INTO spreadadditivecount (conut_additive_id, spread_name, quantity) VALUES ('$conutId', '$spreadName', '1')";
			mysqli_query($connect, $query);
		}
	} 
	

}

if (isset($_POST['conut_additive_id']) && isset($_POST['spread_name']) && isset($_POST['decrement']) ) {
    $conutId = mysqli_real_escape_string($connect, $_POST['conut_additive_id']);
    $spreadName = mysqli_real_escape_string($connect, $_POST['spread_name']);
    $query = "UPDATE spreadadditivecount SET quantity = quantity - 1 WHERE conut_additive_id = $conutId AND spread_name = '$spreadName' AND quantity > 0";
    mysqli_query($connect, $query);
}


?>







<!-- Add a search bar above the spread table -->
<div class="search-bar" id="s1">
    <form method="get" action="Conuts.php#s1">
        <input type="text" name="searchSpread" placeholder="Search for a spread">
        <button type="submit">Search</button>
    </form>
</div>

<div class="spreadtable">
    <?php
    $spreadquery = "SELECT * FROM Spread";
    $spreadresult = mysqli_query($connect, $spreadquery);

    if ($spreadresult && mysqli_num_rows($spreadresult) > 0) :
        $row = mysqli_fetch_assoc($spreadresult);
    ?>
        <table>
            <caption>The <span>SPREADS</span> conut <span> &nbsp;/ <?= $row['price'] ?> $ </span></caption>
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
                        <?php if (isset($_SESSION['conut_additive_id'])) : ?>
                            <form method="post" action="Conuts.php#s1">
                                <input type="hidden" name="conut_additive_id" value="<?= $_SESSION['conut_additive_id'] ?>">
                                <input type="hidden" name="spread_name" value="<?=$spreadName?>">
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
