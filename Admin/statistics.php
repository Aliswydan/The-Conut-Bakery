<?php
session_start();
include 'connect_to_server_and_database.php';

// Initialize $QUERY to null
$QUERY = null;
 $QUERY_Drink=null;
 $QUERY_Chimney=null;
$QUERY_Price=null;
$queryC=null;
$queryD=null;
$queryCh=null;
$Cdetails=null;
$Ddetails=null;
$Chdetails=null;


// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          if (isset($_POST['btnd'])){
                                                 $conut = "SELECT
                                                 c.conut_name,
                                                 SUM(cc.quantity) AS total_quantity
                                             FROM
                                                 conutcontainer cc
                                             JOIN
                                                 conutadditive c ON cc.conut_additive_id = c.conut_additive_id
                                             JOIN
                                                 orderlist o ON cc.order_id = o.order_id
                                             WHERE
                                                 o.order_state = 'done'
                                                 AND DATE(o.order_date) = CURDATE()
                                             GROUP BY
                                                 c.conut_name
                                             ORDER BY
                                                 total_quantity DESC
                                             LIMIT 1;
                                            ";
                                            $QUERY = mysqli_query($connect, $conut);
                                        $drinkQuery = "SELECT
                                        d.drink_name,
                                        SUM(dd.quantity) AS total_quantity
                                    FROM
                                        drinkcontainer dd
                                    JOIN
                                        drinkadditive d ON dd.drink_additive_id = d.drink_additive_id
                                    JOIN
                                        orderlist o ON dd.order_id = o.order_id
                                    WHERE
                                        o.order_state = 'done'
                                        AND DATE(o.order_date) = CURDATE()
                                    GROUP BY
                                        d.drink_name
                                    ORDER BY
                                        total_quantity DESC
                                    LIMIT 1;
                                            ";
                                            $QUERY_Drink = mysqli_query($connect, $drinkQuery);

                                   $chimneyQuery = "SELECT
                                   ch.chimney_name,
                                   SUM(ccc.quantity) AS total_quantity
                               FROM
                                   chimneycontainer ccc
                               JOIN
                                   chimneyadditive ch ON ccc.chimney_additive_id = ch.chimney_additive_id
                               JOIN
                                   orderlist o ON ccc.order_id = o.order_id
                               WHERE
                                   o.order_state = 'done'
                                   AND DATE(o.order_date) = CURDATE()
                               GROUP BY
                                   ch.chimney_name
                               ORDER BY
                                   total_quantity DESC
                               LIMIT 1;"
                                 ;
                                    $QUERY_Chimney = mysqli_query($connect, $chimneyQuery);

// Total price 
$priceT=  " SELECT SUM(total_order_price ) AS PRICE  from orderlist where DATE(order_date)=CURDATE() AND order_state='done' " ;
$QUERY_Price=mysqli_query($connect,$priceT);

$query1="SELECT SUM(cc.quantity) AS total_quantity,SUM(cc.total_price) AS total_sales FROM conutcontainer cc JOIN 
conutadditive ca ON cc.conut_additive_id = ca.conut_additive_id
JOIN 
orderlist o ON cc.order_id = o.order_id
WHERE 
o.order_state = 'done'
AND DATE(o.order_date) = CURDATE()";
$queryC=mysqli_query($connect,$query1);

$query2=" SELECT 
SUM(dc.quantity) AS total_quantity,
SUM(dc.total_price) AS total_sales
FROM 
drinkcontainer dc
JOIN 
drinkadditive da ON dc.drink_additive_id = da.drink_additive_id
JOIN 
orderlist o ON dc.order_id = o.order_id
WHERE 
o.order_state = 'done'
AND DATE(o.order_date) = CURDATE();";

$queryD=mysqli_query($connect,$query2);

$query3="SELECT 
SUM(ccc.quantity) AS total_quantity,
SUM(ccc.total_price) AS total_sales
FROM 
chimneycontainer ccc
JOIN 
chimneyadditive cha ON ccc.chimney_additive_id = cha.chimney_additive_id
JOIN 
orderlist o ON ccc.order_id = o.order_id
WHERE 
o.order_state = 'done'
AND Date(o.order_date) = CURDATE();";
$queryCh=mysqli_query($connect,$query3);


$query10="SELECT ca.conut_name, SUM(cc.quantity) AS total_quantity, SUM(cc.total_price) AS total_sales FROM conutcontainer cc JOIN conutadditive ca ON cc.conut_additive_id = ca.conut_additive_id JOIN orderlist o ON cc.order_id = o.order_id WHERE o.order_state = 'done' AND DATE(o.order_date) = CURDATE() GROUP BY ca.conut_name ORDER BY total_sales DESC; ";
$Cdetails=mysqli_query($connect,$query10);

$query11="SELECT da.drink_name, SUM(dc.quantity) AS total_quantity, SUM(dc.total_price) AS total_sales
FROM drinkcontainer dc
JOIN drinkadditive da ON dc.drink_additive_id = da.drink_additive_id
JOIN orderlist o ON dc.order_id = o.order_id
WHERE o.order_state = 'done' AND DATE(o.order_date) = CURDATE()
GROUP BY da.drink_name
ORDER BY total_sales DESC;
";
$Ddetails=mysqli_query($connect,$query11);

$query12="SELECT cha.chimney_name, SUM(ccc.quantity) AS total_quantity, SUM(ccc.total_price) AS total_sales
FROM chimneycontainer ccc
JOIN chimneyadditive cha ON ccc.chimney_additive_id = cha.chimney_additive_id
JOIN orderlist o ON ccc.order_id = o.order_id
WHERE o.order_state = 'done' AND DATE(o.order_date) = CURDATE()
GROUP BY cha.chimney_name
ORDER BY total_sales DESC;
";
$Chdetails=mysqli_query($connect,$query12);
}

elseif(isset($_POST['btnw'])){
                                    $conut = "SELECT
                                    c.conut_name,
                                    SUM(cc.quantity) AS total_quantity
                                FROM
                                    conutcontainer cc
                                JOIN
                                    conutadditive c ON cc.conut_additive_id = c.conut_additive_id
                                JOIN
                                    orderlist o ON cc.order_id = o.order_id
                                WHERE
                                    o.order_state = 'done'
                                    AND WEEK(o.order_date) =WEEK (CURDATE())
                                        -- AND o.order_date BETWEEN DATE_SUB(NOW(), INTERVAL 7 DAY) AND NOW()

                                GROUP BY
                                    c.conut_name
                                ORDER BY
                                    total_quantity DESC
                                LIMIT 1;
                                    ";
                                    $QUERY = mysqli_query($connect, $conut);
                                $drinkQuery = "SELECT
                                d.drink_name,
                                SUM(dd.quantity) AS total_quantity
                            FROM
                                drinkcontainer dd
                            JOIN
                                drinkadditive d ON dd.drink_additive_id = d.drink_additive_id
                            JOIN
                                orderlist o ON dd.order_id = o.order_id
                            WHERE
                                o.order_state = 'done'
                                AND WEEK(o.order_date) =WEEK(CURDATE())
                            GROUP BY
                                d.drink_name
                            ORDER BY
                                total_quantity DESC
                            LIMIT 1;
                                    ";
                                    $QUERY_Drink = mysqli_query($connect, $drinkQuery);
                                $chimneyQuery = "SELECT
                                ch.chimney_name,
                                SUM(ccc.quantity) AS total_quantity
                            FROM
                                chimneycontainer ccc
                            JOIN
                                chimneyadditive ch ON ccc.chimney_additive_id = ch.chimney_additive_id
                            JOIN
                                orderlist o ON ccc.order_id = o.order_id
                            WHERE
                                o.order_state = 'done'
                                AND WEEK(o.order_date) = WEEK(CURDATE())
                            GROUP BY
                                ch.chimney_name
                            ORDER BY
                                total_quantity DESC
                            LIMIT 1;
                        ";
                            $QUERY_Chimney = mysqli_query($connect, $chimneyQuery);
         // Total price 

$priceT=  " SELECT SUM(total_order_price ) AS PRICE  from orderlist where WEEK(order_date)=WEEK(CURDATE()) AND order_state='done' " ;
$QUERY_Price=mysqli_query($connect,$priceT);
$query4="SELECT 
SUM(cc.quantity) AS total_quantity,
SUM(cc.total_price) AS total_sales
FROM 
conutcontainer cc
JOIN 
conutadditive ca ON cc.conut_additive_id = ca.conut_additive_id
JOIN 
orderlist o ON cc.order_id = o.order_id
WHERE 
o.order_state = 'done'
AND WEEK(o.order_date) = WEEK(CURDATE())";
$queryC=mysqli_query($connect,$query4);

$query5="SELECT 
SUM(dc.quantity) AS total_quantity,
SUM(dc.total_price) AS total_sales
FROM 
drinkcontainer dc
JOIN 
drinkadditive da ON dc.drink_additive_id = da.drink_additive_id
JOIN 
orderlist o ON dc.order_id = o.order_id
WHERE 
o.order_state = 'done'
AND WEEK(o.order_date) = WEEK(CURDATE())";
$queryD=mysqli_query($connect,$query5);

$query6="SELECT 
SUM(ccc.quantity) AS total_quantity,
SUM(ccc.total_price) AS total_sales
FROM 
chimneycontainer ccc
JOIN 
chimneyadditive cha ON ccc.chimney_additive_id = cha.chimney_additive_id
JOIN 
orderlist o ON ccc.order_id = o.order_id
WHERE 
o.order_state = 'done'
AND WEEK(o.order_date) = WEEK(CURDATE())";
$queryCh=mysqli_query($connect,$query6);


$query10="SELECT ca.conut_name, SUM(cc.quantity) AS total_quantity, SUM(cc.total_price) AS total_sales FROM conutcontainer cc JOIN conutadditive ca ON cc.conut_additive_id = ca.conut_additive_id JOIN orderlist o ON cc.order_id = o.order_id WHERE o.order_state = 'done' AND WEEK(o.order_date) = WEEK(CURDATE()) GROUP BY ca.conut_name ORDER BY total_sales DESC; ";
$Cdetails=mysqli_query($connect,$query10);

$query11="SELECT da.drink_name, SUM(dc.quantity) AS total_quantity, SUM(dc.total_price) AS total_sales
FROM drinkcontainer dc
JOIN drinkadditive da ON dc.drink_additive_id = da.drink_additive_id
JOIN orderlist o ON dc.order_id = o.order_id
WHERE o.order_state = 'done' AND WEEK(o.order_date) =WEEK( CURDATE())
GROUP BY da.drink_name
ORDER BY total_sales DESC;
";
$Ddetails=mysqli_query($connect,$query11);

$query12="SELECT cha.chimney_name, SUM(ccc.quantity) AS total_quantity, SUM(ccc.total_price) AS total_sales
FROM chimneycontainer ccc
JOIN chimneyadditive cha ON ccc.chimney_additive_id = cha.chimney_additive_id
JOIN orderlist o ON ccc.order_id = o.order_id
WHERE o.order_state = 'done' AND WEEK(o.order_date) =WEEK(CURDATE())
GROUP BY cha.chimney_name
ORDER BY total_sales DESC;
";
$Chdetails=mysqli_query($connect,$query12);
}

elseif(isset($_POST['btnm'])){
                                        $conut = "SELECT
                                        c.conut_name,
                                        SUM(cc.quantity) AS total_quantity
                                    FROM
                                        conutcontainer cc
                                    JOIN
                                        conutadditive c ON cc.conut_additive_id = c.conut_additive_id
                                    JOIN
                                        orderlist o ON cc.order_id = o.order_id
                                    WHERE
                                        o.order_state = 'done'
                                        AND MONTH(o.order_date) =MONTH(CURDATE())
                                    GROUP BY
                                        c.conut_name
                                    ORDER BY
                                        total_quantity DESC
                                    LIMIT 1;
                                    ";
                                    $QUERY = mysqli_query($connect, $conut);

                                    $drinkQuery = "SELECT
                                    d.drink_name,
                                    SUM(dd.quantity) AS total_quantity
                                FROM
                                    drinkcontainer dd
                                JOIN
                                    drinkadditive d ON dd.drink_additive_id = d.drink_additive_id
                                JOIN
                                    orderlist o ON dd.order_id = o.order_id
                                WHERE
                                    o.order_state = 'done'
                                    AND MONTH(o.order_date) =MONTH(CURDATE())
                                GROUP BY
                                    d.drink_name
                                ORDER BY
                                    total_quantity DESC
                                LIMIT 1;
                                    ";

                                    $QUERY_Drink = mysqli_query($connect, $drinkQuery);

                                    $chimneyQuery = "SELECT
                                    ch.chimney_name,
                                    SUM(ccc.quantity) AS total_quantity
                                FROM
                                    chimneycontainer ccc
                                JOIN
                                    chimneyadditive ch ON ccc.chimney_additive_id = ch.chimney_additive_id
                                JOIN
                                    orderlist o ON ccc.order_id = o.order_id
                                WHERE
                                    o.order_state = 'done'
                                    AND MONTH(o.order_date) = MONTH(CURDATE())
                                GROUP BY
                                    ch.chimney_name
                                ORDER BY
                                    total_quantity DESC
                                LIMIT 1;";
                                    $QUERY_Chimney = mysqli_query($connect, $chimneyQuery);
                                    
// Total price 
 $priceT=  " SELECT SUM(total_order_price ) AS PRICE  from orderlist where MONTH(order_date)=MONTH(CURDATE()) AND order_state='done' " ;
 $QUERY_Price=mysqli_query($connect,$priceT);
 $query7="SELECT 
 SUM(cc.quantity) AS total_quantity,
 SUM(cc.total_price) AS total_sales
FROM 
 conutcontainer cc
JOIN 
 conutadditive ca ON cc.conut_additive_id = ca.conut_additive_id
JOIN 
 orderlist o ON cc.order_id = o.order_id
WHERE 
 o.order_state = 'done'
 AND MONTH(o.order_date) = MONTH(CURDATE());";
 $queryC=mysqli_query($connect,$query7);

 $query8="SELECT     SUM(dc.quantity) AS total_quantity,           SUM(dc.total_price) AS total_sales FROM  drinkcontainer dc JOIN 
                            drinkadditive da ON dc.drink_additive_id = da.drink_additive_id
                        JOIN 
                            orderlist o ON dc.order_id = o.order_id
                        WHERE 
                            o.order_state = 'done'
                            AND MONTH(o.order_date) = MONTH(CURDATE());";
$queryD=mysqli_query($connect,$query8);

$query9="SELECT 
SUM(ccc.quantity) AS total_quantity,
SUM(ccc.total_price) AS total_sales
FROM 
chimneycontainer ccc
JOIN 
chimneyadditive cha ON ccc.chimney_additive_id = cha.chimney_additive_id
JOIN 
orderlist o ON ccc.order_id = o.order_id
WHERE 
o.order_state = 'done'
AND MONTH(o.order_date) = MONTH(CURDATE());";

$queryCh=mysqli_query($connect,$query9);


$query10="SELECT ca.conut_name, SUM(cc.quantity) AS total_quantity, SUM(cc.total_price) AS total_sales FROM conutcontainer cc JOIN conutadditive ca ON cc.conut_additive_id = ca.conut_additive_id JOIN orderlist o ON cc.order_id = o.order_id WHERE o.order_state = 'done' AND MONTH(o.order_date) = MONTH(CURDATE()) GROUP BY ca.conut_name ORDER BY total_sales DESC; ";
$Cdetails=mysqli_query($connect,$query10);

$query11="SELECT da.drink_name, SUM(dc.quantity) AS total_quantity, SUM(dc.total_price) AS total_sales
FROM drinkcontainer dc
JOIN drinkadditive da ON dc.drink_additive_id = da.drink_additive_id
JOIN orderlist o ON dc.order_id = o.order_id
WHERE o.order_state = 'done' AND MONTH(o.order_date) =MONTH( CURDATE())
GROUP BY da.drink_name
ORDER BY total_sales DESC;
";
$Ddetails=mysqli_query($connect,$query11);

$query12="SELECT cha.chimney_name, SUM(ccc.quantity) AS total_quantity, SUM(ccc.total_price) AS total_sales
FROM chimneycontainer ccc
JOIN chimneyadditive cha ON ccc.chimney_additive_id = cha.chimney_additive_id
JOIN orderlist o ON ccc.order_id = o.order_id
WHERE o.order_state = 'done' AND MONTH(o.order_date) =MONTH(CURDATE())
GROUP BY cha.chimney_name
ORDER BY total_sales DESC;
";
$Chdetails=mysqli_query($connect,$query12);
}
}
?>

<script>
    function refreshPage() {
        window.location.reload();
    }
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistical Sales Preview </title>
    <link rel="stylesheet" href="CSS/stat.css">
    <link rel="stylesheet" href="CSS/menu.css">
</head>
<body>
<?php
        include 'menu_bar.php'
    ?>
<div class="title">
    <h1>Statistical Sales Preview </h1>
    <div class="header"> The Supreme Leader of Items, the Essential VIP of Our Store!</div>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <button name="btnd" onclick="refreshPage()">Daily</button>
        <button name="btnw">Weekly</button>
        <button name="btnm">Monthly</button>
    </form>
</div>
<div class="container">
    <div class="left">
        <?php if ($QUERY && mysqli_num_rows($QUERY) > 0) { ?>
            <div class="left1"><h2>Most Selling Conut</h2></div>
            <?php
            while ($row = mysqli_fetch_assoc($QUERY)) {
                $conutName = $row['conut_name'];
                $quantity = $row['total_quantity'];
                // Get the image data
                $query1 = "SELECT image FROM conut WHERE conut_name = '$conutName'";
                $result1 = mysqli_query($connect, $query1);
                $row1 = mysqli_fetch_assoc($result1);
                $imageData = $row1['image'];
                // Get the image type
                $imageType = get_image_type($imageData);
                ?>
                <div class="Conut">
                    <?php if ($imageType == 'jpeg') { ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($imageData); ?>"
                             alt="<?php echo $conutName; ?>" height="200" width="250">
                    <?php } elseif ($imageType == 'png') { ?>
                        <img src="data:image/png;base64,<?php echo base64_encode($imageData); ?>"
                             alt="<?php echo $conutName; ?>" height="200" width="250">
                    <?php } ?>
                    <h2><b><?php echo capitalizeWords($conutName); ?></b></h2>
                    <h1><?php echo "Sold " . $quantity . " Units"; ?></h1>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
    <div class="center">
        <div class="left1"><h2>Most Selling Drink</h2></div>
        <?php if ($QUERY_Drink && mysqli_num_rows($QUERY_Drink) > 0) { ?>
        <?php
        while ($row = mysqli_fetch_assoc($QUERY_Drink)) {
            $drinkName = $row['drink_name'];
            $quantity = $row['total_quantity'];
            // Get the image data for drinks
            $queryDrinkImage = "SELECT image FROM drink WHERE drink_name = '$drinkName'";
            $resultDrinkImage = mysqli_query($connect, $queryDrinkImage);
            $rowDrinkImage = mysqli_fetch_assoc($resultDrinkImage);
            $imageDataDrink = $rowDrinkImage['image'];
            // Get the image type for drinks
            $imageTypeDrink = get_image_type($imageDataDrink);
            ?>
            <div class="Drink">
                <?php if ($imageTypeDrink == 'jpeg') { ?>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($imageDataDrink); ?>"
                         alt="<?php echo $drinkName; ?>" height="190" width="250">
                <?php } elseif ($imageTypeDrink == 'png') { ?>
                    <img src="data:image/png;base64,<?php echo base64_encode($imageDataDrink); ?>"
                         alt="<?php echo $drinkName; ?>" height="190" width="250">
                <?php } ?>
                <h2><b><?php echo capitalizeWords($drinkName); ?></b></h2>
                <h1><?php echo "Sold " . $quantity . " Units"; ?></h1>
            </div>
        <?php } ?>
    <?php } ?>
    </div>
        <div class="right">
    <div class="left1"> <h2>Most Selling Chimney</h2></div>
    <?php if ($QUERY_Chimney && mysqli_num_rows($QUERY_Chimney) > 0) { ?>
        <?php
        while ($row = mysqli_fetch_assoc($QUERY_Chimney)) {
            $chimneyName = $row['chimney_name'];
            $quantity = $row['total_quantity'];
            // Additional code to get image data and type for chimneys
            $queryChimneyImage = "SELECT image FROM chimney WHERE chimney_name = '$chimneyName'";
            $resultChimneyImage = mysqli_query($connect, $queryChimneyImage);
            $rowChimneyImage = mysqli_fetch_assoc($resultChimneyImage);
            $chimneyImageData = $rowChimneyImage['image'];
            $chimneyImageType = get_image_type($chimneyImageData);
            ?>
            <div class="Chimney">
                <?php if ($chimneyImageType == 'jpeg') { ?>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($chimneyImageData); ?>"
                         alt="<?php echo $chimneyName; ?>" height="200" width="250">
                <?php } elseif ($chimneyImageType == 'png') { ?>
                    <img src="data:image/png;base64,<?php echo base64_encode($chimneyImageData); ?>"
                         alt="<?php echo $chimneyName; ?>" height="200" width="250">
                <?php } ?>
                <h2><b><?php echo capitalizeWords($chimneyName); ?></b></h2>
                <h1><?php echo "Sold " . $quantity . " Units"; ?></h1>
            </div>
        <?php } ?>
    <?php } ?>
</div>
    </div>
</div>
<div class="container1">
    <h1>Total Sales Price  <?php if($QUERY_Price && mysqli_num_rows($QUERY_Price)>0){

     ?> 
    <?php while($row=mysqli_fetch_assoc($QUERY_Price)){
        $TOT=$row['PRICE'] ;  ?> 
        <?php } ?>
       <p> <?php  echo $TOT . "$" ?></p>
    
  <?php } ?> </h1>

    <div class="leftconut">
        <div class="left1"><h2> Conut Sales Details </h2>
       
    </div> 
    
    <?php   if($queryC && mysqli_num_rows($queryC)>0){ ?> 
        
                 <?php   while($row=mysqli_fetch_assoc($queryC)){  
                        $conutnum=$row['total_quantity'];
                        $conutS=$row['total_sales'];
                        ?>
                    <h2 style="margin-top: 2em; color:azure;" ><?php echo"Quantity Of Conuts Sold : " . $conutnum ?> </h2> <br>  
                              <?php  if($Cdetails && mysqli_num_rows($Cdetails)>0){ ?>
                           <?php while($row=mysqli_fetch_assoc($Cdetails)) { ?>
                    <?php    $cname=$row['conut_name']; 
                            $tquantity=$row['total_quantity'];
                            $tprice=$row['total_sales']; ?>
                    <h3 style="margin-top: 1em; color:#6d1717; font-weight:bold; background-color:#dcdcdc;" > <?php  echo "Conut name: " . "  " .  $cname . " <br> " .  "quantity :  "  .  $tquantity . "<br>". "price :  " . $tprice ."$ "."<br>" ; ?></h3><br>
                    <?php } ?>
                     <?php } ?>                

                    <h3 style="margin_top:5em ; fontsize: 5em ; background-color:#dcdcdc ;color:#6d1717; "><?php echo"Total Sales Price : " .  $conutS . "$" ?></h3>
                    <?php } ?>
                    <?php } ?>

</div>
    <div class="centerdrink">
        <div class="left1"><h2>Drinks Sales Details</h2></div>
        
    <?php   if($queryD && mysqli_num_rows($queryD)>0){ ?> 
        
        <?php   while($row=mysqli_fetch_assoc($queryD)){  
               $drinknum=$row['total_quantity'];
               $drinkS=$row['total_sales'];
               ?>
           <h2 style="margin-top: 2em; color:azure;" ><?php echo"Quantity Of Drinks Sold : " . $drinknum ?> </h2> <br>  
                     <?php  if($Ddetails && mysqli_num_rows($Ddetails)>0){ ?>
                  <?php while($row=mysqli_fetch_assoc($Ddetails)) { ?>
           <?php    $dname=$row['drink_name']; 
                   $tquantity=$row['total_quantity'];
                   $tprice=$row['total_sales']; ?>
           <h4 style="margin-top: 1em; color:#6d1717;font-weight:bold;background-color:#dcdcdc;" > <?php  echo "Drink name: " . "  " .  $dname . " <br> " .  "quantity :  "  .  $tquantity . "<br>". "price :  " . $tprice ."$". "<br>" ; ?></h4><br>
           <?php } ?>
            <?php } ?>                

           <h3 style="margin_top:5em ; fontsize: 5em ;  color:brown;  background-color:#dcdcdc; color:#6d1717"><?php echo"Total Sales Price : " .  $drinkS . "$" ?></h3>
           <?php } ?>
           <?php } ?>

    </div>
    <div class="rightchim">
        <div class="left1"> <h2>Chimney Sales Details </h2></div>
        <?php   if($queryCh && mysqli_num_rows($queryCh)>0){ ?> 
        
        <?php   while($row=mysqli_fetch_assoc($queryCh)){  
               $chimneynum=$row['total_quantity'];
               $chimneyS=$row['total_sales'];
               ?>
           <h2 style="margin-top: 2em; color:azure;" ><?php echo"Quantity Of Chimneys Sold : " . $chimneynum ?> </h2> <br>  
                     <?php  if($Chdetails && mysqli_num_rows($Chdetails)>0){ ?>
                  <?php while($row=mysqli_fetch_assoc($Chdetails)) { ?>
           <?php    $chname=$row['chimney_name']; 
                   $tquantity=$row['total_quantity'];
                   $tprice=$row['total_sales']; ?>
           <h4 style="margin-top: 1em; color:#6d1717;font-weight:bold; background-color:#dcdcdc;" > <?php  echo "Chimney name: " . "  " .  $chname . " <br> " .  "quantity :  "  .  $tquantity . "<br>". "price :  " . $tprice ."$". "<br>" ; ?></h4><br>
           <?php } ?>
            <?php } ?>                

           <h3 style="margin_top:5em ; fontsize: 5em ; color:#6d1717;  background-color:#dcdcdc; "><?php echo"Total Sales Price : " .  $chimneyS . "$" ?></h3>
           <?php } ?>
           <?php } ?>

    </div> 
</div>
</body>
</html>
<?php
// Function to get the image type based on image data
function get_image_type($imageData)
{
    $imageInfo = getimagesizefromstring($imageData);
    if ($imageInfo !== false) {
        $mime = $imageInfo['mime'];
        switch ($mime) {
            case 'image/jpeg':
                return 'jpeg';
            case 'image/png':
                return 'png';
            default:
                return null; // Unknown image type
        }
    }
    return null; //
}
function capitalizeWords($str) {
    // Use a custom function to capitalize the first letter of each word
    return preg_replace_callback('/\b\w/', function ($match) {
        return strtoupper($match[0]);
    }, $str);
}
?>
