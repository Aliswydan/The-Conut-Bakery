<?php
include_once 'connect_to_server_and_database.php';

function updateUnitPrices($connect, $offer_id)
{
    // Update unit prices in ConutContainer
    $conutQuery = "UPDATE ConutContainer cc
                   JOIN ConutAdditive ca ON cc.conut_additive_id = ca.conut_additive_id
                   JOIN ConutOfferContainer coc ON ca.conut_name = coc.conut_name
                   SET cc.unit_price = cc.unit_price - (SELECT discounted_value 
                                                          FROM Offer 
                                                          WHERE offer_id = $offer_id)
                   WHERE coc.offer_id = $offer_id";

    // Update unit prices in ChimneyContainer
    $chimneyQuery = "UPDATE ChimneyContainer chc
                     JOIN ChimneyAdditive cha ON chc.chimney_additive_id = cha.chimney_additive_id
                     JOIN ChimneyOfferContainer choc ON cha.chimney_name = choc.chimney_name
                     SET chc.unit_price = chc.unit_price - (SELECT discounted_value 
                                                              FROM Offer 
                                                              WHERE offer_id = $offer_id)
                     WHERE choc.offer_id = $offer_id";

    // Update unit prices in DrinkContainer
    $drinkQuery = "UPDATE DrinkContainer dc
                   JOIN DrinkAdditive da ON dc.drink_additive_id = da.drink_additive_id
                   JOIN DrinkOfferContainer doc ON da.drink_name = doc.drink_name
                   SET dc.unit_price = dc.unit_price - (SELECT discounted_value 
                                                          FROM Offer 
                                                          WHERE offer_id = $offer_id)
                   WHERE doc.offer_id = $offer_id";


                  

    // Execute the queries
    if (!mysqli_query($connect, $conutQuery) ||
        !mysqli_query($connect, $chimneyQuery) ||
        !mysqli_query($connect, $drinkQuery)) {
        echo "Error updating unit prices: " . mysqli_error($connect);
    }
}

// Example usage: Update unit prices for offer_id = 1
updateUnitPrices($connect, 1);

?>



