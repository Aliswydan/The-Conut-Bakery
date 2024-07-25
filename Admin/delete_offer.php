<?php
    include_once 'connect_to_server_and_database.php';

    $offerId = $_GET['offerId'];
    $tableName = $_GET['type'];

    $queryCheckState = "select offer_state from offer where offer_id = $offerId";
    $result = mysqli_query($connect,$queryCheckState);
    $resultArray = mysqli_fetch_array($result);
    if($resultArray['offer_state'] == 'active') {
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
            $queryUpdateDate = "update seasonaloffer set fromDate = ? where offer_id = ?";
    
            $currentDate = date("Y-m-d");
    
            $stmt = $connect -> prepare($queryUpdateDate);
    
            $stmt -> bind_param("si",$currentDate,$offerId);
    
            $stmt -> execute();
        }
    
    
        $queryUpdateStateActive = "CALL updateOfferStateInActive(?)";
    
        $stmt = $connect -> prepare($queryUpdateStateActive);
    
        $stmt -> bind_param("i",$offerId);
    
        $stmt -> execute();
    }


    $queryDeleteFromConutOfferContainer = "delete from conutoffercontainer where offer_id = $offerId";
    mysqli_query($connect,$queryDeleteFromConutOfferContainer);

    $queryDeleteFromChimneyOfferContainer = "delete from chimneyoffercontainer where offer_id = $offerId";
    mysqli_query($connect,$queryDeleteFromChimneyOfferContainer);

    $queryDeleteFromDrinkOfferContainer = "delete from drinkoffercontainer where offer_id = $offerId";
    mysqli_query($connect,$queryDeleteFromDrinkOfferContainer);

    if($tableName == 'happyhour') {
        $queryDeleteFromHappyHourOffer = "delete from happyhouroffer where offer_id = $offerId";

        mysqli_query($connect,$queryDeleteFromHappyHourOffer);
    } elseif($tableName == 'seasonal') {
        $queryDeleteFromSeasonal = "delete from seasonaloffer where offer_id = $offerId";
        
        mysqli_query($connect,$queryDeleteFromSeasonal);
    }

    $queryDeleteFromOffer = "delete from offer where offer_id = $offerId";
    mysqli_query($connect,$queryDeleteFromOffer);

    header("Location: offers.php");
    exit();    
?>