<?php
    include_once 'connect_to_server_and_database.php';

    $toppingErrorMessage = '';

    $valuesInForm = array(
        'topping_name' => '',
        'price' => '',
        'admin_name' => ''
    );

    function checkToppingName(&$toppingName) { 
        global $connect,$toppingErrorMessage; 

        $toppingName = trim($toppingName);
        $toppingName = ucwords($toppingName);

        if(preg_match("/^[a-zA-Z]+( [a-zA-Z]+)*$/", $toppingName)) {
            $checkQuery = "SELECT topping_name FROM topping where topping_name = '$toppingName'";
            $result = mysqli_query($connect,$checkQuery);
            
            if(mysqli_num_rows($result) != 0) {
                $toppingErrorMessage = "Name already exists in the database";

                return false;
            }

            return true;
        }

        $toppingErrorMessage = "Name cannot contain non alphabatical characters or multiple spaces";
        return false; 
    }

    function checkToppingPrice($price) {
        global $toppingErrorMessage;

        if($price > 0) {
            if(!preg_match('/\\.\\d{3,}/', $price)) { 
                return true;
            }

            $toppingErrorMessage = "Price contains more than 2 decimal places";

            return false;
        }

        $toppingErrorMessage = "Price cannot be negative";

        return false;
    }

    function addToppingToDatabase($toppingName,$toppingPrice,$toppingModifierAdmin) {
        global $connect;

        $stmt = $connect -> prepare("INSERT INTO topping VALUES (?, ?, ?)");
        $stmt -> bind_param("sss", $toppingName, $toppingPrice, $toppingModifierAdmin);
        $stmt -> execute();
    }

    function updateToppingInDatabase($newValues,$types) {
        global $connect;
        $oldValues = $_SESSION['oldValuesForEditedItem'];
        $oldItemName = $oldValues['topping_name'];

        if(empty($newValues)) {
            return false;
        }

        $updateQuery = "UPDATE topping SET ";
        foreach($newValues as $nameInDatabase => $value) {
            $updateQuery .= "$nameInDatabase = ?, ";
        }

        $updateQuery = substr($updateQuery,0,-2);

        $updateQuery .= " WHERE topping_name = ?";

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

        if($_POST['toppingName'] != $oldValues['topping_name']) {
            if(checkToppingName($_POST['toppingName'])) {
                $newValues['topping_name'] = $_POST['toppingName'];

                $types .= "s";
            } else {
                return false;
            }
        }

        if($_POST['toppingPrice'] != $oldValues['price']) {
            if(checkToppingPrice($_POST['toppingPrice'])) {
                $newValues['price'] = $_POST['toppingPrice'];

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

        $valuesInForm['topping_name'] = $_POST['toppingName'];
        $valuesInForm['price'] = $_POST['toppingPrice'];
    }

    if(isset($_POST['submit'])) {

        if(isset($_POST['editItem'])) {
            $types = '';
            $newValuesAssociativeArray = checkDifferencesAndValidateNewInputs($types);

            if($newValuesAssociativeArray !== false) {
                if(!empty($newValuesAssociativeArray)) {
                    if(updateToppingInDatabase($newValuesAssociativeArray, $types)) {
                        $toppingErrorMessage = "Item edited successfully";
                    } else {
                        $toppingErrorMessage = "Failed to update the item";
                    }
                } else {
                    $toppingErrorMessage = "No changes was made";
                }
            }

            unset($_POST['editItem']);
            unset($_SESSION['oldValuesForEditedItem']);
        } else {
            $toppingName = $_POST['toppingName'];
            $toppingPrice = $_POST['toppingPrice'];
            $adminName = $_SESSION['username'];

            if(checkToppingName($toppingName) && checkToppingPrice($toppingPrice)) {
                addToppingToDatabase($toppingName, $toppingPrice, $adminName);

                $toppingErrorMessage = "Item added successfully";
            } else {
                saveValuesInForm();
            }
        }
    }
    
?>

<div class="outer-div">   
    <div>
        <?php
            $query = "SELECT * FROM topping";

            $result = mysqli_query($connect,$query);

            echo "<table class=\"itemsTable\">
                    <tr>
                        <th> topping name </th>
                        <th> price </th>
                        <th> modified by </th>
                        <th> edit </th>
                        <th> delete </th>
                    </tr>";

            while(($column = mysqli_fetch_array($result)) != null) {
                echo "<tr>
                        <td> $column[topping_name] </td>
                        <td> $column[price] </td>
                        <td> $column[admin_name] </td>
                        <td> <a href=\"view_items.php?toppingName=$column[topping_name]&submenuPageFromEdit=toppings.php\"> edit </a> </td>
                        <td> <a href=\"delete_item.php?itemName=$column[topping_name]&nameAttributeInDb=topping_name&tableName=topping&pageSource=toppings.php\" onclick=\"return confirm('Are you sure you want to delete this item?')\"> delete </a> </td>
                    </tr>";
            }

            echo "</table>";
        ?>
    </div>

    <div>
        
        <form action="view_items.php" method="post" class="itemForm">
            <?php
               
                if(isset($_GET['submenuPageFromEdit'])) {
                    $query = "SELECT * FROM topping WHERE topping_name = '$_GET[toppingName]'";

                    $valuesInForm = mysqli_query($connect,$query);
                    $valuesInForm = mysqli_fetch_array($valuesInForm);

                    $_SESSION['oldValuesForEditedItem'] = $valuesInForm;

                    echo "<input type=\"hidden\" name=\"editItem\">";
                }

                echo "<label for=\"toppingName\">Topping Name:</label><br>
                      <input type=\"text\" name=\"toppingName\" value=\"$valuesInForm[topping_name]\" required><br>";

                echo "<label for=\"toppingPrice\">Price:</label><br>
                     <input type=\"number\" name=\"toppingPrice\" step=\"0.01\" value=\"$valuesInForm[price]\" required><br>";
                
                if(!empty($toppingErrorMessage)) {
                    echo "<p style=\"color:red;\"> $toppingErrorMessage </p>";
                }
                
                echo "<input type=\"hidden\" name=\"submenuPage\" value=\"toppings.php\">
                      <input type=\"submit\" value=\"Submit\" name=\"submit\">";

            ?>
            
        </form>
        
    </div>

    
</div> 