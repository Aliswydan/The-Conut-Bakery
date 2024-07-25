<?php 
include_once 'connect_to_server_and_database.php';
include 'functions.php' ;
?>



<!DOCTYPE html>
<html lang="en">
<head>
<script>
		if ( window.history.replaceState ) {
			window.history.replaceState( null, null, window.location.href );
		}
	</script> 
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> The Conut Bakery | Menu </title>
    <link rel="icon" href="img/logo.png" type="image/ico">

	<link rel="stylesheet"  href="CSS/Drinks.css">
	 <link rel="stylesheet"  href="CSS/nav_bar.css">
	<link rel="stylesheet"  href="CSS/footer.css">

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Fira+Sans+Condensed:wght@500&display=swap" rel="stylesheet">
	



</head>


<body>
<!----------------------------------------------------------- Nav ----------------------------------------------------------->
<nav> <?php include 'nav_bar.php'?></nav>

<!----------------------------------------------------------- main ----------------------------------------------------------->
<main id="container">
    <?php if(isset( $_SESSION['drink_additive_id']))   {    ?>

        <button id="cardButton" onclick="openCard()">My Card</button>

    <?php } ?>

	<div class="containers" >
		
		<p class="header">The Drinks  </p>
    

<!----------------------------------------------------------- Cards ----------------------------------------------------------->
        <?php
$query = "SELECT * FROM drink";
$result = mysqli_query($connect, $query);

if ($result) { ?>
    <h1 style="margin-top: 1.5em; text-align: center; margin-left: 1em; margin-bottom: 1em;">The Milkshakes</h1>
    <div class="cardcontainer">
        <?php
        $milkshakeQuery = "SELECT * FROM drink WHERE drink_name LIKE '%shake%' ";
        $milkshakeResult = mysqli_query($connect, $milkshakeQuery);

        function displayDrinks($result) {
            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $drinkName = $row['drink_name'];
                    $price = $row['price'];
                    $imageData = $row['image'];
                    $imageType = get_image_type($imageData);
                    ?>
                    <div class="card">
                        <?php if ($imageType) { ?>
                            <img src="data:image/<?php echo $imageType; ?>;base64,<?php echo base64_encode($imageData); ?>" alt="<?php echo $drinkName; ?>" height="140" width="140">
                        <?php } ?>
                        <h2><b><?php echo $drinkName; ?></b></h2>
                        <h1><?php echo sprintf("%.2lf",$price) . "$"; ?></h1>

                        <form method="post" action="card.php">
                            <input type="hidden" name="drink_name" value="<?php echo $drinkName; ?>">
                            <button class="btnOrder" type="submit">Order</button>
                        </form>
                    </div>
                <?php
                }
            }
        }

        // Display Milkshakes
        displayDrinks($milkshakeResult);
        ?>
    </div>

    <h1 style="margin-top: 1.5em; text-align: center; margin-left: 1em; margin-bottom: 1em;">The Frappes</h1>
    <div class="cardcontainer">
        <?php
        $frappeQuery = "SELECT * FROM drink WHERE drink_name LIKE '%rappe%'";
        $frappeResult = mysqli_query($connect, $frappeQuery);

        // Display Frappes
        displayDrinks($frappeResult);
        ?>
    </div>

    <h1 style="margin-top: 1.5em; text-align: center; margin-left: 1em; margin-bottom: 1em;">The Hot Brews</h1>
    <div class="cardcontainer">
        <?php
        $hotBrewQuery = "SELECT * FROM drink WHERE  drink_name LIKE 'hot%' OR drink_name LIKE 'Hot%' ";
        $hotBrewResult = mysqli_query($connect, $hotBrewQuery);

        // Display Hot Brews
        displayDrinks($hotBrewResult);
        ?>
    </div>

    <h1 style="margin-top: 1.5em; text-align: center; margin-left: 1em; margin-bottom: 1em;">The Iced Brews</h1>
    <div class="cardcontainer">
        <?php 
     $icedBrewQuery = "SELECT * FROM drink WHERE drink_name LIKE 'iced%' OR drink_name LIKE  'Iced%' "; 
     $icedBrewResult = mysqli_query($connect, $icedBrewQuery);
     

        // Display Iced Brews
        displayDrinks($icedBrewResult);
        ?>
    </div>
<?php } ?>



<!----------------------------------------------------------- Toppings ----------------------------------------------------------->
<?php include 'ToppingDrink.php'?>



<!----------------------------------------------------------- card ----------------------------------------------------------->
<?php if(isset($_SESSION['drink_additive_id']) ){
	?>

	<div class="card-div" id="cardDiv">
		<h1>My Card</h1>
		<h2>&star;drink name: </h2> 
        <div class="cardbtn">

        <button><?php echo $_SESSION['drink_name'] ?> </button>

            </div>


<!--------------------------------------- Toppings --------------------------------------->
<?php
$drink_additive_id = ($_SESSION['drink_additive_id']);
$toppingQuery = "SELECT topping_name, quantity FROM toppingadditivedrink WHERE drink_additive_id = $drink_additive_id";
$toppingResult = mysqli_query($connect, $toppingQuery);
?>

<h2>&star;Toppings:</h2>
<div class= "cardbtn" >

<?php
if ($toppingResult) {
    while ($toppingRow = mysqli_fetch_assoc($toppingResult)) {
        $toppingName = $toppingRow['topping_name'];
        $toppingQuantity = $toppingRow['quantity'];
        ?>
        <form method="post">
            <input type="hidden" name="drink_additive_id" value="<?= $drink_additive_id ?>">
            <input type="hidden" name="topping_name" value="<?= $toppingName ?>"> 
            <button type="submit" name="decrement"> <?= $toppingName ?> x <?= $toppingQuantity ?></button>
        </form>

    <?php
    }
}
?>

</div>


<h1> price : <?php 
$drink_additve_id = $_SESSION['drink_additive_id'];

$total_price = get_price_drink_with_toppings($drink_additve_id, $connect); 

echo $total_price .' $' ;
?>

</h1>

<!-- <?php echo isset($_SESSION['drink_additive_id']) ? ($_SESSION['drink_additive_id']) : 'Not set'; ?> -->

<!-- Close button -->


<button class="closebtn" onclick="closeSecondDiv()">Close</button>


<!-- Submit To Order button -->

        
<form method="POST" action="Ordernow.php" >
    <input type="hidden" name="drink_additive_id" value="<?= $drink_additive_id ?>">
    <input type="hidden" name="total_price" value="<?= $total_price ?>">
    <button class="closebtn" >Submit To Order</button>
</form>





<!-- cancel button -->
<form id="deleteForm" method="POST" action="card.php">
    <input type="hidden" name="drink_additive_id_delete" value="<?= $drink_additive_id ?>">
    <button class="closebtn" type="Submit" >Cancel</button>
</form>
 

</div>
 

<?php } ?>





<!----------------------------------------------------------- script ----------------------------------------------------------->

<script>
        document.addEventListener('DOMContentLoaded', function () {
            function checklogin(event) {
                <?php if (!isset($_SESSION['user_id'])) { ?>
                    event.preventDefault(); // Prevent the form submission
                    window.location.href = 'login.php';
                <?php } ?>
            }

            // Attach the checklogin function to all forms with class 'btnOrder'
            var btnOrderForms = document.querySelectorAll('.btnOrder');
            btnOrderForms.forEach(function (form) {
                form.closest('form').addEventListener('submit', checklogin);
            });
        });
    </script>


<script>

function openCard() {
    document.getElementById('cardDiv').classList.add('show');
    document.getElementById('container').classList.add('show');
}
<?php if (isset($_SESSION['card_div'])) { ?>
document.getElementById('cardDiv').classList.add('show');
document.getElementById('container').classList.add('show');
<?php } else { ?>

document.getElementById('cardDiv').classList.remove('show');
document.getElementById('container').classList.remove('show');
<?php } ?>

function closeSecondDiv() {

document.getElementById('cardDiv').classList.remove('show');
document.getElementById('container').classList.remove('show');
}

</script>


</main>



<!----------------------------------------------------------- Footer ----------------------------------------------------------->
<footer>
    <?php include 'Footer.php'; ?>
</footer>
    </body>
    </html>
    <!----------------------------------------------------------- End ----------------------------------------------------------->



