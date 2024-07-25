<?php
    include_once 'connect_to_server_and_database.php';

    $spreadErrorMessage = '';

    $valuesInForm = array(
        'spread_name' => '',
        'price' => '',
        'admin_name' => ''
    );

    function checkSpreadName(&$spreadName) { 
        global $connect,$spreadErrorMessage; 

        $spreadName = trim($spreadName);
        $spreadName = ucwords($spreadName);

        if(preg_match("/^[a-zA-Z]+( [a-zA-Z]+)*$/", $spreadName)) {
            $checkQuery = "SELECT spread_name FROM spread where spread_name = '$spreadName'";
            $result = mysqli_query($connect,$checkQuery);
            
            if(mysqli_num_rows($result) != 0) {
                $spreadErrorMessage = "Name already exists in the database";

                return false;
            }

            return true;
        }

        $spreadErrorMessage = "Name cannot contain non alphabatical characters or multiple spaces";
        return false; 
    }

    function checkSpreadPrice($price) {
        global $spreadErrorMessage;

        if($price > 0) {
            if(!preg_match('/\\.\\d{3,}/', $price)) { 
                return true;
            }

            $spreadErrorMessage = "Price contains more than 2 decimal places";

            return false;
        }

        $spreadErrorMessage = "Price cannot be negative";

        return false;
    }

    function addSpreadToDatabase($spreadName,$spreadPrice,$spreadModifierAdmin) {
        global $connect;

        $stmt = $connect -> prepare("INSERT INTO spread VALUES (?, ?, ?)");
        $stmt -> bind_param("sss", $spreadName, $spreadPrice, $spreadModifierAdmin);
        $stmt -> execute();
    }

    function updateSpreadInDatabase($newValues,$types) {
        global $connect;
        $oldValues = $_SESSION['oldValuesForEditedItem'];
        $oldItemName = $oldValues['spread_name'];

        if(empty($newValues)) {
            return false;
        }

        $updateQuery = "UPDATE spread SET ";
        foreach($newValues as $nameInDatabase => $value) {
            $updateQuery .= "$nameInDatabase = ?, ";
        }

        $updateQuery = substr($updateQuery,0,-2);

        $updateQuery .= " WHERE spread_name = ?";

        $stmt = $connect -> prepare($updateQuery);

        $newValuesPointers = array();

        foreach($newValues as &$value) {
            $newValuesPointers[] = &$value;
        }

        $newValuesPointers[] = &$oldItemName;

        call_user_func_array(array($stmt, 'bind_param'), array_merge(array($types), $newValuesPointers));

        $stmt -> execute();

        return true;
    }

    function checkDifferencesAndValidateNewInputs(&$types) {
        $types = '';
        $newValues = array();
        $oldValues = $_SESSION['oldValuesForEditedItem'];

        if($_POST['spreadName'] != $oldValues['spread_name']) {
            if(checkSpreadName($_POST['spreadName'])) {
                $newValues['spread_name'] = $_POST['spreadName'];

                $types .= "s";
            } else {
                return false;
            }
        }

        if($_POST['spreadPrice'] != $oldValues['price']) {
            if(checkSpreadPrice($_POST['spreadPrice'])) {
                $newValues['price'] = $_POST['spreadPrice'];

                $types .= "s";
            } else {
                return false;
            }
        }

        if($_SESSION['username'] != $oldValues['admin_name']) {
            $newValues['admin_name'] = $_SESSION['username'];

            $types .= "s";
        }

        $types .= "s";

        return $newValues;
    }

    function saveValuesInForm() {
        global $valuesInForm;

        $valuesInForm['spread_name'] = $_POST['spreadName'];
        $valuesInForm['price'] = $_POST['spreadPrice'];
    }

    if(isset($_POST['submit'])) {

        if(isset($_POST['editItem'])) {
            $types = '';
            $newValuesAssociativeArray = checkDifferencesAndValidateNewInputs($types);

            if($newValuesAssociativeArray !== false) {
                if(!empty($newValuesAssociativeArray)) {
                    if(updateSpreadInDatabase($newValuesAssociativeArray, $types)) {
                        $spreadErrorMessage = "Item edited successfully";
                    } else {
                        $spreadErrorMessage = "Failed to update the item";
                    }
                } else {
                    $spreadErrorMessage = "No changes was made";
                }
            }

            unset($_POST['editItem']);
            unset($_SESSION['oldValuesForEditedItem']);
        } else {
            $spreadName = $_POST['spreadName'];
            $spreadPrice = $_POST['spreadPrice'];
            $adminName = $_SESSION['username'];

            if(checkSpreadName($spreadName) && checkSpreadPrice($spreadPrice)) {
                addSpreadToDatabase($spreadName, $spreadPrice, $adminName);

                $spreadErrorMessage = "Item added successfully";
            } else {
                saveValuesInForm();
            }
        }
    }
    
?>

<div class="outer-div">   
    <div>
        <?php
            $query = "SELECT * FROM spread";

            $result = mysqli_query($connect,$query);

            echo "<table class=\"itemsTable\">
                    <tr>
                        <th> spread name </th>
                        <th> price </th>
                        <th> modified by </th>
                        <th> edit </th>
                        <th> delete </th>
                    </tr>";

            while(($column = mysqli_fetch_array($result)) != null) {
                echo "<tr>
                        <td> $column[spread_name] </td>
                        <td> $column[price] </td>
                        <td> $column[admin_name] </td>
                        <td> <a href=\"view_items.php?spreadName=$column[spread_name]&submenuPageFromEdit=spreads.php\"> edit </a> </td>
                        <td> <a href=\"delete_item.php?itemName=$column[spread_name]&nameAttributeInDb=spread_name&tableName=spread&pageSource=spreads.php\" onclick=\"return confirm('Are you sure you want to delete this item?')\"> delete </a> </td>
                    </tr>";
            }

            echo "</table>";
        ?>
    </div>

    <div>
        
        <form action="view_items.php" method="post" class="itemForm">
            <?php
                
                if(isset($_GET['submenuPageFromEdit'])) {
                    $query = "SELECT * FROM spread WHERE spread_name = '$_GET[spreadName]'";

                    $valuesInForm = mysqli_query($connect,$query);
                    $valuesInForm = mysqli_fetch_array($valuesInForm);

                    $_SESSION['oldValuesForEditedItem'] = $valuesInForm;

                    echo "<input type=\"hidden\" name=\"editItem\">";
                }

                echo "<label for=\"spreadName\">Spread Name:</label><br>
                      <input type=\"text\" name=\"spreadName\" value=\"$valuesInForm[spread_name]\" required><br>";

                echo "<label for=\"spreadPrice\">Price:</label><br>
                     <input type=\"number\" name=\"spreadPrice\" step=\"0.01\" value=\"$valuesInForm[price]\" required><br>";
                
                if(!empty($spreadErrorMessage)) {
                    echo "<p style=\"color:red;\"> $spreadErrorMessage </p>";
                }
                
                echo "<input type=\"hidden\" name=\"submenuPage\" value=\"spreads.php\">
                      <input type=\"submit\" value=\"Submit\" name=\"submit\">";

            ?>
            
        </form>
        
    </div>

    
</div> 