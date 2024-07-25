<?php
include_once 'connect_to_server_and_database.php';
include 'functions.php';
$errorMessage = '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
<!-- <meta http-equiv="refresh" content="5"> -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Conut Bakery | Ordernow</title>
    <link rel="icon" href="img/logo.png" type="image/ico">

    <link rel="stylesheet" href="CSS/Ordernow.css">
    <link rel="stylesheet" href="CSS/nav_bar.css">
    <link rel="stylesheet" href="CSS/footer.css">

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


<main class="container">
    <div >  

            <div class="title">  
                <h2>Your Order</h2> 
            </div> 

        
<?php 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['conut_additive_id']) && isset($_POST['total_price'])) {
    $_SESSION['Conutorder'] = true;
    unset($_SESSION['conut_additive_id']);
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['chimney_additive_id']) && isset($_POST['total_price'])) {
    $_SESSION['Chimneyorder'] = true;
    unset($_SESSION['chimney_additive_id']);
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['drink_additive_id']) && isset($_POST['total_price'])) {
    $_SESSION['Drinkorder'] = true;
    unset($_SESSION['drink_additive_id']);
}


 // if there is no order yet > please make an order
if (!isset($_SESSION['Conutorder']) && !isset($_SESSION['Drinkorder']) && !isset($_SESSION['Chimneyorder'])) {
    ?>
    <h2>No order yet! Please make an order.</h2>
<?php }  
 


include 'ConutContainer.php' ;
include 'ChimneyContainer.php' ;
include 'DrinkContainer.php' ;

?>


<!-- To read the total price for ConutContainer, ChimneyContainer, and DrinkContainer -->

<?php
$totalSumConut = 0;
$totalSumChimney = 0;
$totalSumDrink = 0;

if (isset($_SESSION['order_id'])) {
    $order_id = $_SESSION['order_id'];

    // Read total price for ConutContainer
    $sqlTotalPriceConut = "SELECT total_price FROM ConutContainer WHERE order_id = $order_id";
    $sqlResultConut = mysqli_query($connect, $sqlTotalPriceConut);

    if ($sqlResultConut) {
        while ($rowConut = mysqli_fetch_assoc($sqlResultConut)) {
            $totalSumConut += $rowConut['total_price'];
        }
    }

    // Read total price for ChimneyContainer
    $sqlTotalPriceChimney = "SELECT total_price FROM ChimneyContainer WHERE order_id = $order_id";
    $sqlResultChimney = mysqli_query($connect, $sqlTotalPriceChimney);

    if ($sqlResultChimney) {
        while ($rowChimney = mysqli_fetch_assoc($sqlResultChimney)) {
            $totalSumChimney += $rowChimney['total_price'];
        }
    }

    // Read total price for DrinkContainer
    $sqlTotalPriceDrink = "SELECT total_price FROM DrinkContainer WHERE order_id = $order_id";
    $sqlResultDrink = mysqli_query($connect, $sqlTotalPriceDrink);

    if ($sqlResultDrink) {
        while ($rowDrink = mysqli_fetch_assoc($sqlResultDrink)) {
            $totalSumDrink += $rowDrink['total_price'];
        }
    }
}
?>


<!-- To read the total price for ConutContainer, ChimneyContainer, and DrinkContainer -->
<div class="Yorder">
    <?php 
    $total_order_price = $totalSumConut + $totalSumChimney + $totalSumDrink;
    ?>
    <h1>Total price: <span><?= $total_order_price ?> $</span></h1>

    <br>  
    <div>  
        <input type="radio" name="dbt" value="dbt" checked> Direct Bank Transfer  
    </div>  

    <p>  
        Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order will not be shipped until the funds have cleared in our account.  
    </p>  

    <div>  
        <input type="radio" name="dbt" value="cd"> Cash on Delivery  
    </div>  
</div>



  





                        <!-- To read the user details  -->
<?php

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sqlUser = "SELECT user_name, phone_number, email, password, address FROM User WHERE user_id = $user_id";

    $sqlResult = mysqli_query($connect, $sqlUser);

    // Check if the query was successful
    if ($sqlResult) {
        $row = mysqli_fetch_assoc($sqlResult)
     ?>
    <form method="POST" id="form">

                    <div class="fform">
                            <label>     
                            <span class="fname"> user Name <span class="required1"> * </span></span>  
                            <input required  type="text" name="fname" value="<?= $row['user_name'] ?>">  
                            </label>  

                                        
                            <label>  
                            <span>Branches <span class="required1">*</span></span>  
                            <select name="selection">  
                                <option value="BER">Beirut: Bliss Street, Hamra</option>
                                <option value="CIF">Chouifat: The Spot Chouifat</option>
                                <option value="DHY">Dahyeh: Delivery Branch</option>
                            </select>  
                            </label>        
                            
                            <label>  
                            <span> Address <span class="required1"> * </span></span>  
                            <input required   type="text" name="houseadd" value="<?= $row['address'] ?> ">  
                            </label>  

                       
                            <label>  
                            <span> Phone Number <span class="required1">*</span></span>  
                            <input required type="tel"  name="Phone"  value="<?= $row['phone_number'] ?>">   
                            </label> 

                            <label for="myEmail">     
                            <span> Email Address <span class="required1">*</span></span>  
                            <input    class="required email"  type="email" name="myEmail" id="myEmail" value="<?= $row['email'] ?>">   
                            </label> 

                </div>
        

<?php } }else { ?>
    <form method="POST" id="form">
                    <div class="fform"  >
                            <label>     
                            <span class="fname"> user Name <span class="required1"> * </span></span>  
                            <input required  type="text" name="fname" >  
                            </label>  

                                        
                            <label>  
                            <span>Branches <span class="required1">*</span></span>  
                            <select name="selection">  
                                <option value="BER">Beirut: Bliss Street, Hamra</option>
                                <option value="CIF">Chouifat: The Spot Chouifat</option>
                                <option value="DHY">Dahyeh: Delivery Branch</option>
                            </select>  
                            </label>        
                            
                            <label>  
                            <span> Address <span class="required1"> * </span></span>  
                            <input required   type="text" name="houseadd" >  
                            </label>  

                       
                            <label>  
                            <span> Phone Number <span class="required1">*</span></span>  
                            <input required type="tel"  name="Phone"  >   
                            </label> 

                            <label for="myEmail">     
                            <span> Email Address <span class="required1">*</span></span>  
                            <input    class="required email"  type="email" name="myEmail" id="myEmail">   
                            </label> 

                </div>
                <?php } ?>

t>


           <!-- To submit the order of the user and change the state -->



<?php
           if (isset($_POST['submitTheOrder'])) {
                if (isset($_SESSION['order_id'])) {
                    $order_id = $_SESSION['order_id'];
                    $errorMessage = 'Your Order has been Submitted';

                    // Update order state
                    $stateUpdate = "UPDATE OrderList SET order_state = 'ordered' WHERE order_id = $order_id";
                    $stateResult = mysqli_query($connect, $stateUpdate);

                    $stateUpdate = "UPDATE OrderList SET total_order_price = '$total_order_price' WHERE order_id = $order_id";
                    $stateResult = mysqli_query($connect, $stateUpdate);

                    // Update order date
                    date_default_timezone_set('Asia/Beirut');
                    $dateUpdate = "UPDATE OrderList SET order_date = NOW() WHERE order_id = $order_id";
                    $dateResult = mysqli_query($connect, $dateUpdate);

                    if (!$dateResult) {
                        $errorMessage = 'Error updating order date: ' . mysqli_error($connect);
                    } else {

                        unset($_SESSION['order_id']);
                        unset($_SESSION['Conutorder']);
                        unset($_SESSION['Chimneyorder']);
                        unset($_SESSION['Drinkorder']);
                        $errorMessage = 'Your order has been submitted successfully.';
                        ?>
                        <script>
                            // Call the refreshPage function after displaying the message
                            showMessageAndRefresh();
                    
                            function showMessageAndRefresh() {
                                alert("<?php echo $errorMessage; ?>");
                                location.reload(true);
                            }
                        </script>
                         
                        <?php
                    }
                } else {
                    $errorMessage = 'Make an order first.';
                }
            }
            ?>

            <!-- Form for submitting the order -->
            <form method="post" action="Ordernow.php#form">
                <!-- Your form fields go here -->
                <button type="submit" name="submitTheOrder">Place Order</button>

            </form>

            <!-- Display error message if any -->
            <?php
            if (!empty($errorMessage)) {
                echo '<script>alert("' . $errorMessage . '");</script>';
            }
            ?>

            
        
      
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <?php include 'Footer.php'; ?>
    </footer>
</body>

</html>