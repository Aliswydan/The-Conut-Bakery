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
	
	<link rel="stylesheet"  href="CSS/Conuts.css">
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
    <?php if(isset( $_SESSION['conut_additive_id']))   {    ?>

        <button id="cardButton" onclick="openCard()">My Card</button>

    <?php } ?>

	<div class="containers" >
		
		<p class="header">Our specialty Hungarian dough, conical shaped, golden grilled and cinnamon-sugar coated.</p>
		


<!----------------------------------------------------------- Cards ----------------------------------------------------------->

<?php
$query = "SELECT * FROM Conut";
$result = mysqli_query($connect, $query);

if ($result) { ?>
    <div class="cardcontainer">
        <?php while ($row = mysqli_fetch_assoc($result)) {
            $conutName = $row['conut_name'];
            $description = $row['description'];
            $price = $row['price'];

            $imageData = $row['image'];
            // Determine the image type
            $imageType = get_image_type($imageData);
        ?>
            <div class="card">
                <?php if ($imageType == 'jpeg') { ?>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($imageData); ?>" alt="<?php echo $conutName; ?>" height="140" width="140">
                <?php } elseif ($imageType == 'png') { ?>
                    <img src="data:image/png;base64,<?php echo base64_encode($imageData); ?>" alt="<?php echo $conutName; ?>" height="140" width="140">
                <?php } ?>
                <h2><b><?php echo $conutName; ?></b></h2>
                <p><?php echo $description; ?></p>
                <h1><?php echo sprintf("%.2lf",$price) . "$"; ?></h1>

                <form method="post" action="card.php">
                    <input type="hidden" name="conut_name" value="<?php echo $conutName; ?>">
                    <button class="btnOrder" type="submit">Order </button>
                </form>
            </div>
        <?php } ?>
    </div>

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
<?php } ?>






<!----------------------------------------------------------- Spraeds ----------------------------------------------------------->

<?php include 'SpreadConut.php'?>


<!----------------------------------------------------------- Toppings ----------------------------------------------------------->
<?php include 'ToppingConut.php'?>


<!----------------------------------------------------------- card ----------------------------------------------------------->
<?php if(isset($_SESSION['conut_additive_id']) ){
	?>

	<div class="card-div" id="cardDiv">
		<h1>My Card</h1>
		<h2>&star;Conut name: </h2> 
        <div class="cardbtn">

        <button><?php echo $_SESSION['conut_name'] ?> </button>

            </div>


<!--------------------------------------- Spreads --------------------------------------->

<?php
$conut_additive_id = ($_SESSION['conut_additive_id']);
$spreadquery = "SELECT spread_name, quantity FROM spreadadditivecount WHERE conut_additive_id = $conut_additive_id";
$result = mysqli_query($connect, $spreadquery);
?>

<h2>&star;Spreads:</h2>
<div class= "cardbtn" >
<?php
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $spreadName = $row['spread_name'];
        $quantity = $row['quantity'];
        ?>
        <form  method="post">
            <input type="hidden" name="conut_additive_id" value="<?= $conut_additive_id ?>">
            <input type="hidden" name="spread_name" value="<?= $spreadName ?>">
            <button  type="submit" name="decrement"> <?=$spreadName ?> x <?= $quantity ?></button>
        </form>

        <?php
    }
}?>

</div>



<!--------------------------------------- Toppings --------------------------------------->
<?php
$conut_additive_id = ($_SESSION['conut_additive_id']);
$toppingQuery = "SELECT topping_name, quantity FROM toppingadditivecount WHERE conut_additive_id = $conut_additive_id";
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
            <input type="hidden" name="conut_additive_id" value="<?= $conut_additive_id ?>">
            <input type="hidden" name="topping_name" value="<?= $toppingName ?>"> 
            <button type="submit" name="decrement"> <?= $toppingName ?> x <?= $toppingQuantity ?></button>
        </form>

    <?php
    }
}
?>

</div>


<h1> price : <?php 
$conut_additve_id = $_SESSION['conut_additive_id'];
$total_price = get_price_conut($conut_additve_id, $connect) ;

echo $total_price .' $'; ?>
</h1>

<!-- <?php echo isset($_SESSION['conut_additive_id']) ? ($_SESSION['conut_additive_id']) : 'Not set'; ?> -->




<!-- Close button -->


<button class="closebtn" onclick="closeSecondDiv()">Close</button>


<!-- Submit To Order button -->

<form method="POST" action="Ordernow.php"    >
    <input type="hidden" name="conut_additive_id" value="<?= $conut_additive_id ?>">
    <input type="hidden" name="total_price" value="<?= $total_price ?>">
    <button class="closebtn" >Submit To Order</button>

</form>


<!-- cancel button -->
<form id="deleteForm" method="POST" action="card.php">
    <input type="hidden" name="conut_additive_id_delete" value="<?= $conut_additive_id ?>">
    <button class="closebtn" type="Submit" >Cancel</button>
</form>
 
</div>
 

<?php }



?>





</main>

 
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



</body>  
<!----------------------------------------------------------- Footer ----------------------------------------------------------->
<footer>
	<?php include 'Footer.php'; ?>
</footer>

</html>