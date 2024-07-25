<?php
    include_once 'connect_to_server_and_database.php';

    date_default_timezone_set('Asia/Beirut');


    $offerId = $_GET['offerId'];
    $tableName = $_GET['type'];

    $queryUpdatePrices = "CALL updatePrices(?)";
    
    $stmt = $connect -> prepare($queryUpdatePrices);
    
    $stmt -> bind_param("i",$offerId);

    $stmt -> execute();

   
    if($tableName == 'happyhour') {
        $queryUpdateTime = "update happyhouroffer set start_time = ?,end_time = ? where offer_id = ?";

        $startTime = date('H:i');
        $timeAfterAnHour = date('H:i', strtotime('+1 hour'));

        $stmt = $connect -> prepare($queryUpdateTime);
        
        $stmt -> bind_param("ssi",$startTime,$timeAfterAnHour,$offerId);

        $stmt -> execute();
    } elseif($tableName == 'seasonal') {
        $queryUpdateDate = "update seasonaloffer set fromDate = ? where offer_id = ?";

        $currentDate = date("Y-m-d");

        $stmt = $connect -> prepare($queryUpdateDate);

        $stmt -> bind_param("si",$currentDate,$offerId);

        $stmt -> execute();
    }


    $queryUpdateStateActive = "CALL updateOfferState(?)";

    $stmt = $connect -> prepare($queryUpdateStateActive);

    $stmt -> bind_param("i",$offerId);

    $stmt -> execute();

    header("Location: offers.php");
    exit();
?>