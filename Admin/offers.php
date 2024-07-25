<?php
    session_start();

    include_once 'connect_to_server_and_database.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Offers </title>
    
    <link rel="stylesheet" type="text/css" href="CSS/menu.css">
    <link rel="stylesheet" type="text/css" href="CSS/offers.css">
</head>

<body>
    <?php
        include 'menu_bar.php'
    ?>

    <div>
        <button id="createOfferButton" onclick="window.location.href='create_offer.php'">Create Offer</button>
    </div>

    <div>
        <h2>Happy Hour Offer</h2>
        <table>
            <?php
                echo "<tr>
                        <th>Offer Id</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Discount Value</th>
                        <th>Offer State</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Change State</th>
                        <th>Delete Offer</th>
                    </tr>";

                $queryGetHappyHourOffer = "select * from offer o,happyhouroffer h where o.offer_id = h.offer_id";
                $result = mysqli_query($connect,$queryGetHappyHourOffer);

                while(($column = mysqli_fetch_array($result)) != null) {
                    echo "<tr>
                            <td>$column[offer_id]</td>
                            <td>$column[name]</td>
                            <td>$column[description]</td>
                            <td>$column[discounted_value]</td>
                            <td>$column[offer_state]</td>";
                    
                    
                    if(isset($column['start_time'])) {
                        echo "<td>$column[start_time]</td>";
                    } else {
                        echo "<td></td>";
                    }
                    
                    if(isset($column['end_time'])) {
                        echo "<td>$column[end_time]</td>";
                    } else {
                        echo "<td></td>";
                    }
                    
                    if($column['offer_state'] != 'active') {
                        echo "<td><a href=\"activate.php?offerId=$column[offer_id]&type=happyhour\">Activate</a></td>";
                    } else {
                        echo "<td><a href=\"deactivate.php?offerId=$column[offer_id]&type=happyhour\">Deactivate</a></td>";
                    }

                      echo "<td><a href=\"delete_offer.php?offerId=$column[offer_id]&type=happyhour\">Delete</a></td>";
                }

              
            ?>
        </table>

        
        <h2>Seasonal Offer</h2>

        <table>
            <?php
                echo "<tr>
                        <th>Offer Id</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Discount Value</th>
                        <th>Offer State</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>Change State</th>
                        <th>Delete Offer</th>
                    </tr>";

                $queryGetSeasonalOffer = "select * from offer o,seasonaloffer s where o.offer_id = s.offer_id";
                $result = mysqli_query($connect,$queryGetSeasonalOffer);

                while(($column = mysqli_fetch_array($result)) != null) {
                    echo "<tr>
                            <td>$column[offer_id]</td>
                            <td>$column[name]</td>
                            <td>$column[description]</td>
                            <td>$column[discounted_value]</td>
                            <td>$column[offer_state]</td>";

                    if(isset($column['fromDate'])) {
                        echo "<td>$column[fromDate]</td>";
                    } else {
                        echo "<td></td>";
                    }
                    
                    if(isset($column['toDate'])) {
                        echo "<td>$column[toDate]</td>";
                    } else {
                        echo "<td></td>";
                    }

                    if($column['offer_state'] != 'active') {
                        echo "<td><a href=\"activate.php?offerId=$column[offer_id]&type=seasonal\">Activate</a></td>";
                    } else {
                        echo "<td><a href=\"deactivate.php?offerId=$column[offer_id]&type=seasonal\">Deactivate</a></td>";
                    }


                    echo "<td><a href=\"delete_offer.php?offerId=$column[offer_id]&type=seasonal\">Delete</a></td>";
                }

            ?>
        </table>
    </div>


   
</body>


</html>