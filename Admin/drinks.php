<?php
    include_once 'connect_to_server_and_database.php';

    $drinkErrorMessage = '';

    $valuesInForm = array(
        'drink_name' => '',
        'image' => '',
        'price' => '',
        'admin_name' => ''
    );

    if(isset($_SESSION['isValidImage'])) {
        unset($_SESSION['isValidImage']);
    }

    function checkDrinkName(&$drinkName) { 
        global $connect,$drinkErrorMessage; 

        $drinkName = trim($drinkName);
        $drinkName = ucwords($drinkName);

        if(preg_match("/^[a-zA-Z]+( [a-zA-Z]+)*$/", $drinkName)) {
            $checkQuery = "SELECT drink_name FROM drink where drink_name = '$drinkName'";
            $result = mysqli_query($connect,$checkQuery);
            
            if(mysqli_num_rows($result) != 0) {
                $drinkErrorMessage = "Name already exists in the database";

                return false;
            }

            return true;
        }

        $drinkErrorMessage = "Name cannot contain non alphabatical characters or multiple spaces";
        return false; 
    }

    function checkDrinkPrice($price) {
        global $drinkErrorMessage;

        if($price > 0) {
            if(!preg_match('/\\.\\d{3,}/', $price)) { 
                return true;
            }

            $drinkErrorMessage = "Price contains more than 2 decimal places";

            return false;
        }

        $drinkErrorMessage = "Price cannot be negative or equal to 0";

        return false;
    }

    function checkDrinkImage() {
        global $drinkErrorMessage;

        if($_FILES['drinkImage']['error'] == 0) {
            if($_FILES['drinkImage']['size'] <= $_POST['MAX_FILE_SIZE']) {
                if(getimagesize($_FILES['drinkImage']['tmp_name'])) { 
                    
                    return true;

                } else {
                    $drinkErrorMessage = "File is not an image";

                    return false;
                }
            } else {
                $drinkErrorMessage = "File size is larger than 1MB";

                return false;
            }
        } elseif($_FILES['drinkImage']['error'] == UPLOAD_ERR_NO_FILE){ 
             $drinkErrorMessage = "No file was uploaded";

            return 4;
        } else {
            $drinkErrorMessage = "Error in uploading the file";

            return false;
        }
    }

    function addDrinkToDatabase($drinkName,$drinkPrice,$drinkImageContent,$drinkModifierAdmin) {
        global $connect;

        $stmt = $connect->prepare("INSERT INTO drink VALUES (?, ?, ?, ?)");
        $null = NULL;
        $stmt->bind_param("sbss", $drinkName, $null, $drinkPrice, $drinkModifierAdmin);
        $stmt->send_long_data(1, $drinkImageContent);
        $stmt->execute();
    }

    function updateDrinkInDatabase($newValues,$types) {
        global $connect;
        $oldValues = $_SESSION['oldValuesForEditedItem'];
        $oldItemName = $oldValues['drink_name'];
    
        if(empty($newValues)) {
            return false;
        }
        
        $updateQuery = "UPDATE drink SET ";
        foreach($newValues as $nameInDatabase => $value) {
            $updateQuery .= "$nameInDatabase = ?, "; 
        }
    
        $updateQuery = substr($updateQuery,0,-2); 
    
        $updateQuery .= " WHERE drink_name = ?";
    
        $stmt = $connect -> prepare($updateQuery);
        
        $newValuesPointers = array();
    
        foreach($newValues as &$value) {
            $newValuesPointers[] = &$value;
        }
    
        $positionOfImage = strpos($types,"b"); 
        if($positionOfImage !== false) {
            $null = NULL;
            end($newValuesPointers);
            $key = key($newValuesPointers);
            $newValuesPointers[$key] = &$null;
            reset($newValuesPointers);
            $newValuesPointers[] = &$oldItemName;
            
            call_user_func_array(array($stmt, 'bind_param'), array_merge(array($types), $newValuesPointers));
            $stmt -> send_long_data($positionOfImage,$newValues['image']);
        } else {
            $newValuesPointers[] = &$oldItemName;
            call_user_func_array(array($stmt, 'bind_param'), array_merge(array($types), $newValuesPointers));
        }
        
        $stmt -> execute();

        return true;
    }

    function checkDifferencesAndValidateNewInputs(&$types) {
        $types = '';
        global $drinkErrorMessage;
        $newValues = array();
        $oldValues = $_SESSION['oldValuesForEditedItem'];

        if($_POST['drinkName'] != $oldValues['drink_name']) {
            if(checkDrinkName($_POST['drinkName'])) {
                $newValues['drink_name'] = $_POST['drinkName'];

                $types .= "s";
            } else {
                return false;
            }
        }

        if($_POST['drinkPrice'] != $oldValues['price']) {
            if(checkDrinkPrice($_POST['drinkPrice'])) {
                $newValues['price'] = $_POST['drinkPrice'];

                $types .= "s";
            } else {
                return false;
            }
        }

        if($_SESSION['username'] != $oldValues['admin_name']) {
            $newValues['admin_name'] = $_SESSION['username'];

            $types .= "s";
        }

        $checkDrinkImageResult = checkDrinkImage();
        if($checkDrinkImageResult === true) { 
            $drinkImageContent = file_get_contents($_FILES['drinkImage']['tmp_name']);

            if($drinkImageContent !== false) {
                $newValues['image'] = $drinkImageContent;

                $types .= "b";
            } else {
                $drinkErrorMessage = "Failed to read the image file";
            }
        } elseif($checkDrinkImageResult === false) {
            return false;
        }

        $types .= "s";

        return $newValues;
    }

    function saveValuesInForm() {
        global $valuesInForm,$drinkErrorMessage;

        $valuesInForm['drink_name'] = $_POST['drinkName'];
        $valuesInForm['price'] = $_POST['drinkPrice'];

        $checkDrinkImageResult = checkDrinkImage();
        if($checkDrinkImageResult === true) {
            $enteredImage = $_FILES['drinkImage']['tmp_name'];
            $drinkImageContent = file_get_contents($enteredImage);

            if($drinkImageContent !== false) {
                $valuesInForm['image'] = $drinkImageContent;
                $_SESSION['image'] = $drinkImageContent;
                $_SESSION['isValidImage'] = true;
            } else {
                $drinkErrorMessage = "Error reading the image file";
            }
        } elseif(isset($_SESSION['image'])) {
            $_SESSION['isValidImage'] = true;
            $valuesInForm['image'] = $_SESSION['image'];
        }
    }

    if(isset($_POST['submit'])) { 
        if(isset($_POST['editItem'])) { 
            $types = '';
            $newValuesAssociativeArray = checkDifferencesAndValidateNewInputs($types);

            if($newValuesAssociativeArray !== false) {
                if(!empty($newValuesAssociativeArray)) {
                    if(updateDrinkInDatabase($newValuesAssociativeArray,$types) === true) {
                        $drinkErrorMessage = "Item edited successfully";

                    } else {
                        $drinkErrorMessage = "Failed to update the item";
                    }
                } else { 
                    $drinkErrorMessage = "No changes was made";
                }
            } 

            unset($_POST['editItem']);
            unset($_SESSION['oldValuesForEditedItem']);

           
        } else { 
            $drinkName = $_POST['drinkName'];
            $drinkPrice = $_POST['drinkPrice'];
            $adminName = $_SESSION['username'];
        
            if(checkDrinkName($drinkName) && checkDrinkPrice($drinkPrice)) {

                $checkDrinkImageResult = checkDrinkImage();
                if($checkDrinkImageResult === true) {
                    $drinkImage = $_FILES['drinkImage']['tmp_name'];
                    $drinkImageContent = file_get_contents($drinkImage);

                    if($drinkImageContent !== false) {

                        addDrinkToDatabase($drinkName,$drinkPrice,$drinkImageContent,$adminName);

                        $drinkErrorMessage = "Item added successfully";

                        if(isset($_SESSION['image'])) {
                            unset($_SESSION['image']);
                        }
                        

                    } else {
                        $drinkErrorMessage = "Error reading the image file";
                    }
                } elseif($checkDrinkImageResult == 4 && isset($_SESSION['image'])) {
                    addDrinkToDatabase($drinkName,$drinkPrice,$_SESSION['image'],$adminName);

                    $drinkErrorMessage = "Item added successfully";

                    unset($_SESSION['image']);

                } elseif($checkDrinkImageResult == false  || ($checkDrinkImageResult == 4 && !isset($_SESSION['image'])) || ($checkDrinkImageResult == false && isset($_SESSION['image']))) {
                    saveValuesInForm();

                } 

            } else {
                saveValuesInForm();
            }
        }
       
    }
?>

<div class="outer-div">   
    <div>
        <?php

            function get_image_type($binary_data) {
                $mime_types = array(
                    'jpeg' => "\xFF\xD8\xFF",
                    'png' => "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A",
                );

                foreach ($mime_types as $type => $header) {
                    if (substr($binary_data, 0, strlen($header)) === $header) {
                        return $type;
                    }
                }

                return null;  
            }


            $query = "SELECT * FROM drink";

            $result = mysqli_query($connect,$query);

            echo "<table class=\"itemsTable\">
                    <tr>
                        <th> drink name </th>
                        <th> price </th>
                        <th> image </th>
                        <th> modified by </th>
                        <th> edit </th>
                        <th> delete </th>
                    </tr>";

            while(($column = mysqli_fetch_array($result)) != null) {
                echo "<tr>
                        <td> $column[drink_name] </td>
                        <td> $column[price] </td>";
                    
                    $drinkImageData = $column['image'];
                    $imageType = get_image_type($drinkImageData);

                    if($imageType == 'jpeg') {
                        echo "<td> <img height=\"40px\" width=\"40px\" src='data:image/jpeg;base64," . base64_encode($drinkImageData) . "'> </td>";
                    } else if($imageType == 'png') {
                        echo "<td> <img height=\"40px\" width=\"40px\" src='data:image/png;base64," . base64_encode($drinkImageData) . "'> </td>";
                    }
                    
                    
                    echo "
                        <td> $column[admin_name] </td>
                        <td> <a href=\"view_items.php?drinkName=$column[drink_name]&submenuPageFromEdit=drinks.php\"> edit </a> </td>
                        <td> <a href=\"delete_item.php?itemName=$column[drink_name]&nameAttributeInDb=drink_name&tableName=drink&pageSource=drinks.php\" onclick=\"return confirm('Are you sure you want to delete this item?')\"> delete </a> </td>
                    </tr>";
            }

            echo "</table>";
        ?>
    </div>

    <div>
        
        <form action="view_items.php" method="post" enctype="multipart/form-data" class="itemForm">
            <?php

                if(isset($_GET['submenuPageFromEdit'])) {
                    $query = "SELECT * FROM drink WHERE drink_name = '$_GET[drinkName]'";

                    $valuesInForm = mysqli_query($connect,$query);
                    $valuesInForm = mysqli_fetch_array($valuesInForm);
                    
                    $_SESSION['oldValuesForEditedItem'] = $valuesInForm;

                    echo "<input type=\"hidden\" name=\"editItem\">";
                }

                echo "<label for=\"drinkName\">Drink Name:</label><br>
                      <input type=\"text\" name=\"drinkName\" value=\"$valuesInForm[drink_name]\" required><br>";

                echo "<label for=\"drinkPrice\">Price:</label><br>
                     <input type=\"number\" name=\"drinkPrice\" step=\"0.01\" value=\"$valuesInForm[price]\" required><br>";

                if(isset($_GET['submenuPageFromEdit']) || isset($_SESSION['isValidImage'])) {
                    $drinkImageData = $valuesInForm['image'];
                    $imageType = get_image_type($drinkImageData);

                    echo "<div style=\"display:flex;align-items:center;margin-top:30px;\">Old Image:";
                    if($imageType == 'jpeg') {
                        echo "<img height=\"50px\" width=\"50px\" src='data:image/jpeg;base64," . base64_encode($drinkImageData) . "'>";
                    } else if($imageType == 'png') {
                        echo "<img style=\"margin-top:50px;\" height=\"50px\" width=\"50px\" src='data:image/png;base64," . base64_encode($drinkImageData) . "'>";
                    } 
                    
                    echo "</div>";
                }
                echo "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"1048576\">
                      <label for=\"drinkImage\">Image:</label><br>
                      <input type=\"file\" name=\"drinkImage\" accept=\"image/*\"><br>";
                
                if(!empty($drinkErrorMessage)) {
                    echo "<p style=\"color:red;\"> $drinkErrorMessage </p>";
                }
                
                echo "<input type=\"hidden\" name=\"submenuPage\" value=\"drinks.php\">
                      <input type=\"submit\" value=\"Submit\" name=\"submit\">";

            ?>
            
        </form>
        
    </div>

    
</div>
