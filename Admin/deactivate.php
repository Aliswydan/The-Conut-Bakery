<?php
    include_once 'connect_to_server_and_database.php';

    $offerId = $_GET['offerId'];
    $tableName = $_GET['type'];

    $queryUpdatePrices = "CALL returnPricesToDefault(?)";
    
    $stmt = $connect -> prepare($queryUpdatePrices);
    
    $stmt -> bind_param("i",$offerId);

    $stmt -> execute();

    if($tableName == 'happyhour') {
        $queryUpdateTime = "update happyhouroffer set start_time = ?,end_time = ? where offer_id = ?";

        $startTime = NULL;
        $endTime = NULL;

        $stmt = $connect -> prepare($queryUpdateTime);
        
        $stmt -> bind_param("ssi",$startTime,$timeAfterAnHour,$offerId);

        $stmt -> execute();
    } elseif($tableName == 'seasonal') {
        $queryUpdateDate = "update seasonaloffer set fromDate = ?,toDate = ? where offer_id = ?";

        $fromDate = NULL;
        $toDate = NULL;

        $stmt = $connect -> prepare($queryUpdateDate);

        $stmt -> bind_param("ssi",$fromDate,$toDate,$offerId);

        $stmt -> execute();
    }


    $queryUpdateStateActive = "CALL updateOfferStateInActive(?)";

    $stmt = $connect -> prepare($queryUpdateStateActive);

    $stmt -> bind_param("i",$offerId);

    $stmt -> execute();

    header("Location: offers.php");
    exit();
?>