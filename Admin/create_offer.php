<?php
    include_once 'connect_to_server_and_database.php';

    session_start();

    $offerErrorMessage = '';

    /*
        About getAllNamesOfCategory() function

        Purpose : to get all names from an item category table(conut, chimney, drink) from the database to be viewed

        Return : a table of one column containing all names
    */ 
    function getAllNamesOfCategory($tableName, $itemNameInDatabase) {
        global $connect;

        $query = "SELECT $itemNameInDatabase FROM $tableName";

        $result = mysqli_query($connect, $query);

        return $result;
    }

    //Delete the spaces from beginning, end, and multiple spaces between words
    function cleanSpaces(&$text) {
        $text = trim($text);
        $text = preg_replace("/\s+/"," ",$text);
    }


    function checkOfferName(&$offerName) {
        global $offerErrorMessage;

        cleanSpaces($offerName);

        if(!empty($offerName)) {
            return true;
        }

        $offerErrorMessage = "Offer name is empty";

        return false;
    }

    function checkOfferDescription(&$offerDescription) {
        global $offerErrorMessage;

        cleanSpaces($offerDescription);

        if(!empty($offerDescription)) {
            return true;
        }

        $offerErrorMessage = "Offer Description is empty";

        return false;
    }

    function checkOfferDiscountValue($offerDiscountValue, $checkedItemNames) {
        global $offerErrorMessage, $connect;

        if(!empty($offerDiscountValue)) {
             if($offerDiscountValue > 0) {
                if(!preg_match('/\\.\\d{3,}/', $offerDiscountValue)) { 

                    $names = $checkedItemNames[0];
                    
                    $nameString = "'" . implode("','", $names) . "'";

                    $query = "SELECT conut_name,price FROM conut WHERE conut_name IN ($nameString)";
                    
                    $result = mysqli_query($connect, $query);

                    while(($column = mysqli_fetch_array($result)) != NULL) {
                        if($offerDiscountValue > $column['price']) {
                            $offerErrorMessage = "$column[conut_name] has a price: $column[price] which is greater than the discount value: $offerDiscountValue";
                            
                            return false;
                        }
                    }
                    
                    $names = $checkedItemNames[1];
                    
                    $nameString = "'" . implode("','", $names) . "'";

                    $query = "SELECT chimney_name,price FROM chimney WHERE chimney_name IN ($nameString)";
                    
                    $result = mysqli_query($connect, $query);

                    while(($column = mysqli_fetch_array($result)) != NULL) {
                        if($offerDiscountValue > $column['price']) {
                            $offerErrorMessage = "$column[chimney_name] has a price: $column[price] which is greater than the discount value: $offerDiscountValue";
                            
                            return false;
                        }
                    }

                    $names = $checkedItemNames[2];
                    
                    $nameString = "'" . implode("','", $names) . "'";

                    $query = "SELECT drink_name,price FROM drink WHERE drink_name IN ($nameString)";
                    
                    $result = mysqli_query($connect, $query);

                    while(($column = mysqli_fetch_array($result)) != NULL) {
                        if($offerDiscountValue > $column['price']) {
                            $offerErrorMessage = "$column[drink_name] has a price: $column[price] which is greater than the discount value: $offerDiscountValue";
                            
                            return false;
                        }
                    }

                    return true;

                } else {
                    $offerErrorMessage = "Discount value contains more than 2 decimal places";

                    return false;
                } 
            } else {
                $offerErrorMessage = "Discount value cannot be negative or equal to 0";

                return false;
            }
        } else {
            $offerErrorMessage = "Discount value is empty";

            return false;
        }
       
    }

    //Check all inputs if they are correct
    function checkInputs($offerName, $offerDescription, $offerDiscountValue, $checkedItemNames) {
        return checkOfferName($offerName) && checkOfferDescription($offerDescription) && checkOfferDiscountValue($offerDiscountValue, $checkedItemNames);
    }
    
    /*
        About the getCheckedItemNames() function:

        Purpose : determine the checked items and return them in a multidimensional array

        Return : 
            -false : in case no checked items
            -multidimensional array containing three arrays each one correspond to a 
            category(conut, chimney, or drink) containing all selected names       
    */
    function getChekedItemNames() {
        global $offerErrorMessage,$connect;

        $query = '';
        $result = '';

        //The following check is responsible because if the checkboxes for an array is empty then they will not present as a key in $_POST
        //Unlike other input fields
        $conutNames = isset($_POST['conuts']) ? $_POST['conuts'] : [];
        $chimneyNames = isset($_POST['chimneys']) ? $_POST['chimneys'] : [];
        $drinkNames = isset($_POST['drinks']) ? $_POST['drinks'] : [];
        

        $checkedItemNames = array();

        if(empty($conutNames) && empty($chimneyNames) && empty($drinkNames)) {
            $offerErrorMessage = "No chosen items";

            return false;
        }

        if(in_array("allConuts", $conutNames)) {
            unset($conutNames);
            $conutNames = array();

            $query = "SELECT conut_name FROM conut";

            $result = mysqli_query($connect, $query);

            while(($column = mysqli_fetch_array($result)) != null) {
                $conutNames[] = $column['conut_name'];
            }
        }

        if(in_array("allChimneyCakes", $chimneyNames)) {
            unset($chimneyNames);
            $chimneyNames = array();

            $query = "SELECT chimney_name FROM chimney";

            $result = mysqli_query($connect, $query);

            while(($column = mysqli_fetch_array($result)) != null) {
                $chimneyNames[] = $column['chimney_name'];
            }            
        }

        if(in_array("allDrinks", $drinkNames)) {
            unset($drinkNames);
            $drinkNames = array();

            $query = "SELECT drink_name FROM drink";

            $result = mysqli_query($connect, $query);

            while(($column = mysqli_fetch_array($result)) != null) {
                $drinkNames[] = $column['drink_name'];
            }
        }
            
        $checkedItemNames = array($conutNames, $chimneyNames, $drinkNames);

        return $checkedItemNames;
    }

    /*
        About readInputs() function

        Purpose : read input fields, validate them, and return them

        Return :
            -false in case there is an input error
            -array containing the values offerName, offerDescription, offerDiscountValue
        
        How It Works :
            -The function first reads the values from $_POST
            -Then it calls checkInputs() function to validate the input fields
        
        Precondition:
            -The function must be called after getting the checked items
            -Because it takes as parameter the checked items
            -The parameter will then be passed to checkOfferDiscountValue() function
    */
    function readInputs($checkedItemNames) {
        $offerName = $_POST['offerName'];
        $offerDescription = $_POST['offerDescription'];
        $offerDiscountValue = $_POST['offerDiscountValue'];

        if(checkInputs($offerName, $offerDescription, $offerDiscountValue, $checkedItemNames)) {

            $enteredData = [
                $offerName,
                $offerDescription,
                $offerDiscountValue,
            ];

            return $enteredData;
        }

        return false;
    }

    function createOffer($inputData, $checkedItemNames, $offerChosen, $dataOrTimeArray) {
        global $connect,$offerErrorMessage;

        $offerName = $inputData[0];
        $offerDescription = $inputData[1];
        $offerDiscountValue = $inputData[2];

        $insertQuery = "INSERT INTO offer (name,description,discounted_value) VALUES(?,?,?)";

        $stmt = $connect -> prepare($insertQuery);
        $stmt -> bind_param("ssd", $offerName, $offerDescription, $offerDiscountValue);
        $stmt -> execute();

        $lastId = mysqli_insert_id($connect);

        if($offerChosen == 'happyHour') {

            $startTime = isset($dataOrTimeArray['start_time']) ? $dataOrTimeArray['start_time'] : null;
            $endTime = isset($dataOrTimeArray['end_time']) ? $dataOrTimeArray['end_time'] : null;

            $insertQuery = "INSERT INTO happyhouroffer (offer_id, start_time, end_time) VALUES(?, ?, ?)";
            $stmt = $connect -> prepare($insertQuery);
            $stmt -> bind_param("iss", $lastId, $startTime, $endTime); 
    

        } elseif($offerChosen == 'seasonal') {

            $startDate = isset($dataOrTimeArray['fromDate']) ? $dataOrTimeArray['fromDate'] : null;
            $endDate = isset($dataOrTimeArray['toDate']) ? $dataOrTimeArray['toDate'] : null;
            
            $insertQuery = "INSERT INTO seasonaloffer (offer_id,fromDate,toDate) VALUES(?,?,?)";
            
            $stmt = $connect -> prepare($insertQuery);
            $stmt -> bind_param("iss",$lastId, $startDate, $endDate);
        }

        $stmt -> execute();


        $insertQuery = "INSERT INTO conutoffercontainer (offer_id, conut_name) VALUES(?,?)";
        $stmt = $connect -> prepare($insertQuery);
        
        $conutNames = $checkedItemNames[0];
        
        foreach($conutNames as $name) {
            $stmt -> bind_param("is", $lastId, $name);
            $stmt -> execute();
        }

        $insertQuery = "INSERT INTO chimneyoffercontainer (offer_id, chimney_name) VALUES(?,?)";
        $stmt = $connect -> prepare($insertQuery);

        $chimneyNames = $checkedItemNames[1];

        foreach($chimneyNames as $name) {
            $stmt -> bind_param("is",$lastId, $name);
            $stmt -> execute();
        }

        $insertQuery = "INSERT INTO drinkoffercontainer (offer_id, drink_name) VALUES(?,?)";
        $stmt = $connect -> prepare($insertQuery);

        $drinkNames = $checkedItemNames[2];

        foreach($drinkNames as $name) {
            $stmt -> bind_param("is",$lastId, $name);
            $stmt -> execute();
        }

        $offerErrorMessage = "Offer created successfully";

    }

    function getTimeArray() {
        global $offerErrorMessage;

        $startTime = $_POST['happyHourStartTime'];
        $startAndEndTime = array();
        $currentTime = date("H:i");

        if(!empty($startTime)) {
            if($startTime > $currentTime) {
                $startAndEndTime['start_time'] = $startTime;
                $startAndEndTime['end_time'] = date('H:i', strtotime($startTime . ' +1 hour'));

                return $startAndEndTime;
            } else {
                $offerErrorMessage = "Time in past";
                
                return false;
            }
        }

        return $startAndEndTime;
    }

    function getDateArray() {
        global $offerErrorMessage;

        $startDate = $_POST['seasonalStartDate'];
        $endDate = $_POST['seasonalEndDate'];
        $currentDate = date('Y-m-d');
        $startAndEndDate = array();

        if(!empty($startDate)) {
            if(!empty($endDate)) {
                if($startDate >= $currentDate) {
                    if($startDate <= $endDate) {
                        $startAndEndDate['fromDate'] = $startDate;
                        $startAndEndDate['toDate'] = $endDate;
                    } else {
                        $offerErrorMessage = "Start date is greater than the end date";

                        return false;
                    }
                } else {
                    $offerErrorMessage = "Start date in the past";

                    return false;
                }   
            } else {
                if($startDate >= $currentDate) {
                    $startAndEndDate['fromDate'] = $startDate;
                } else {
                    $offerErrorMessage = "Start date in the past";

                    return false;
                }
            }
        } else {
            if(!empty($endDate)) {
                if($endDate >= $currentDate) {
                    $startAndEndDate['toDate'] = $endDate;
                } else {
                    $offerErrorMessage = "End date in past";

                    return false;
                }
            }
        }

        return $startAndEndDate;
        
    }

    if(isset($_POST['offerSaveButton'])) {
        $offerChosen = $_POST['offerType'];

        if($offerChosen == 'happyHour' || $offerChosen == 'seasonal') {
            $checkedItemNames = getChekedItemNames();
            
            if($checkedItemNames != false) {
                $inputData = readInputs($checkedItemNames);

                if($inputData != false) {
                    $timeArray = getTimeArray();
                    $dateArray = getDateArray();

                    if($offerChosen == 'happyHour' && $timeArray !== false) {
                        createOffer($inputData, $checkedItemNames, $offerChosen, $timeArray);
                    } elseif($offerChosen == 'seasonal' && $dateArray !== false) {
                        createOffer($inputData, $checkedItemNames, $offerChosen, $dateArray);
                    }
                }
            }
        } else {
            $offerErrorMessage = "Please choose an offer type";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Create Offer </title>
    <link type="text/css" rel="stylesheet" href="CSS/create_offer.css">

    <script type="text/javascript">
       function showFields() {
            var offerType = document.getElementById('offer_type').value;

            if (offerType == 'happyHour') {

                document.getElementById('happy_hour_fields').style.display = 'block';
                document.getElementById('seasonal_offer_fields').style.display = 'none';

            } else if (offerType == 'seasonal') {
                 
                document.getElementById('happy_hour_fields').style.display = 'none';
                document.getElementById('seasonal_offer_fields').style.display = 'block';
               
            } else {
                
                document.getElementById('happy_hour_fields').style.display = 'none';
                document.getElementById('seasonal_offer_fields').style.display = 'none';
                
            }
        }

        function toggleCheckboxes(category) {
            var selectAllCheckbox = document.getElementById('selectAll' + category);
            var checkboxes = document.querySelectorAll('input[name="' + category + '[]"]');
            
            for(var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = selectAllCheckbox.checked;
            }
        }

    </script>
</head>

<body>

    <form method="post">

        <div class="outer_div">

            <div class="inputFields">  

                <label for="offer_type">Offer Type:</label>
                <select id="offer_type" name="offerType" onchange="showFields();">
                    <option value="">Select an offer type</option>
                    <option value="happyHour">Happy Hour</option>
                    <option value="seasonal">Seasonal</option>
                </select><br>

                <label for="offerName">Offer Name:<br>
                <input type="text" id="offerName" name="offerName"><br>

                <label for="offerDescription">Description</label><br>
                <textarea id="offerDescription" name="offerDescription"></textarea><br>

                <label for="offerDiscountedValue">Discount Value</label><br>
                <input type="number" id="offerDiscountValue" name="offerDiscountValue" step="0.01"><br>

                <div id="happy_hour_fields" style="display: none;">

                    <label for="happyHourStartTime">Start Time:</label><br>
                    <input type="time" id="happyHourStartTime" name="happyHourStartTime"><br>

                </div>

                <div id="seasonal_offer_fields" style="display: none;">

                    <label for="seasonalStartDate">Start Date:</label><br>
                    <input type="date" id="seasonalStartDate" name="seasonalStartDate"><br>

                    <label for="seasonalEndDate">End Date:</label><br>
                    <input type="date" id="seasonalEndDate" name="seasonalEndDate"><br>
                    
                </div>
                
                <?php
                    if(!empty($offerErrorMessage)) {
                        echo "<p style=\"color:red;\">$offerErrorMessage</p>";
                    }
                ?>

                <input type="submit" value="Save" name="offerSaveButton">

            </div>

            <div class="conuts">
                <h3>Conuts:</h3>

                <?php
                    $conutNamesResult = getAllNamesOfCategory('conut', 'conut_name');

                    echo "<input type=\"checkbox\" id=\"selectAllconuts\" name=\"conuts[]\" value=\"allConuts\" onclick=\"toggleCheckboxes('conuts')\"> Select All Conuts<br><br>";
                    while(($column = mysqli_fetch_array($conutNamesResult)) != null) {
                        echo "<input type=\"checkbox\" name=\"conuts[]\" value=\"$column[conut_name]\">$column[conut_name] <br>";
                    }
                ?>
            </div>

            <div class="chimney_cakes">
                <h3>Chimney Cakes:</h3>

                <?php
                    $chimneyNamesResult = getAllNamesOfCategory('chimney', 'chimney_name');
                    
                    echo "<input type=\"checkbox\" id=\"selectAllchimneys\" name=\"chimneys[]\" value=\"allChimneyCakes\" onclick=\"toggleCheckboxes('chimneys')\"> Select All Chimney Cakes<br><br>";
                    while(($column = mysqli_fetch_array($chimneyNamesResult)) != null) {
                        echo "<input type=\"checkbox\" name=\"chimneys[]\" value=\"$column[chimney_name]\">$column[chimney_name] <br>";
                    }
                ?>

            </div>

            <div class="drinks">
                <h3>Drinks:</h3>

                <?php
                    $drinkNamesResult = getAllNamesOfCategory('drink', 'drink_name');

                    echo "<input type=\"checkbox\" id=\"selectAlldrinks\" name=\"drinks[]\" value=\"allDrinks\" onclick=\"toggleCheckboxes('drinks')\"> Select All Drinks<br><br>";
                    while(($column = mysqli_fetch_array($drinkNamesResult)) != null) {
                        echo "<input type=\"checkbox\" name=\"drinks[]\" value=\"$column[drink_name]\">$column[drink_name] <br>";
                    }
                ?>
            </div>

        </div>


    </form>
    
    
</body>

</html>