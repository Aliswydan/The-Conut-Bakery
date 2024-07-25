<?php
    include_once 'connect_to_server_and_database.php';

    $chimneyErrorMessage = '';

    $valuesInForm = array(
        'chimney_name' => '',
        'description' => '',
        'image' => '',
        'price' => '',
        'admin_name' => ''
    );

    if(isset($_SESSION['isValidImage'])) {
        unset($_SESSION['isValidImage']);
    }

    function checkChimneyName(&$chimneyName) { 
        global $connect,$chimneyErrorMessage; 

        $chimneyName = trim($chimneyName);
        $chimneyName = ucwords($chimneyName);

        if(preg_match("/^[a-zA-Z]+( [a-zA-Z]+)*$/", $chimneyName)) {
            $checkQuery = "SELECT chimney_name FROM chimney where chimney_name = '$chimneyName'";
            $result = mysqli_query($connect,$checkQuery);
            
            if(mysqli_num_rows($result) != 0) {
                $chimneyErrorMessage = "Name already exists in the database";

                return false;
            }

            return true;
        }

        $chimneyErrorMessage = "Name cannot contain non alphabatical characters or multiple spaces";
        return false; 
    }

    function checkChimneyDescription(&$chimneyDescription) {
        global $chimneyErrorMessage;

        $chimneyDescription = strtolower($chimneyDescription);
        $chimneyDescription = trim($chimneyDescription);
        if(preg_match("/^[a-z]+( [a-z]+)*$/", $chimneyDescription)) {
            return true;
        }

        $chimneyErrorMessage = "Multiple spaces between words or wrong entry in description";

        return false;
    }

    function checkChimneyPrice($price) {
        global $chimneyErrorMessage;

        if($price > 0) {
            if(!preg_match('/\\.\\d{3,}/', $price)) { 
                return true;
            }

            $chimneyErrorMessage = "Price contains more than 2 decimal places";

            return false;
        }

        $chimneyErrorMessage = "Price cannot be negative or equal to 0";

        return false;
    }

    function checkChimneyImage() {
        global $chimneyErrorMessage;

        if($_FILES['chimneyImage']['error'] == 0) {
            if($_FILES['chimneyImage']['size'] <= $_POST['MAX_FILE_SIZE']) {
                if(getimagesize($_FILES['chimneyImage']['tmp_name'])) { 
                    
                    return true;

                } else {
                    $chimneyErrorMessage = "File is not an image";

                    return false;
                }
            } else {
                $chimneyErrorMessage = "File size is larger than 1MB";

                return false;
            }
        } elseif($_FILES['chimneyImage']['error'] == UPLOAD_ERR_NO_FILE){ 
             $chimneyErrorMessage = "No file was uploaded";

            return 4;
        } else {
            $chimneyErrorMessage = "Error in uploading the file";

            return false;
        }
    }

    function addChimneyToDatabase($chimneyName,$chimneyDescription,$chimneyPrice,$chimneyImageContent,$chimneyModifierAdmin) {
        global $connect;

        $stmt = $connect->prepare("INSERT INTO chimney VALUES (?, ?, ?, ?, ?)");
        $null = NULL;
        $stmt->bind_param("ssbss", $chimneyName, $chimneyDescription, $null, $chimneyPrice, $chimneyModifierAdmin);
        $stmt->send_long_data(2, $chimneyImageContent);
        $stmt->execute();
    }

    function updateChimneyInDatabase($newValues,$types) {
        global $connect;
        $oldValues = $_SESSION['oldValuesForEditedItem'];
        $oldItemName = $oldValues['chimney_name'];
    
        if(empty($newValues)) {
            return false;
        }
        
        $updateQuery = "UPDATE chimney SET ";
        foreach($newValues as $nameInDatabase => $value) {
            $updateQuery .= "$nameInDatabase = ?, "; 
        }
    
        $updateQuery = substr($updateQuery,0,-2); 
    
        $updateQuery .= " WHERE chimney_name = ?";
    
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
        global $chimneyErrorMessage;
        $newValues = array();
        $oldValues = $_SESSION['oldValuesForEditedItem'];

        if($_POST['chimneyName'] != $oldValues['chimney_name']) {
            if(checkChimneyName($_POST['chimneyName'])) {
                $newValues['chimney_name'] = $_POST['chimneyName'];

                $types .= "s";
            } else {
                return false;
            }
        }

        if($_POST['chimneyDescription'] != $oldValues['description']) {
            if(checkChimneyDescription($_POST['chimneyDescription'])) {
                $newValues['description'] = $_POST['chimneyDescription'];

                $types .= "s";
            } else {
                return false;
            }
        }

        if($_POST['chimneyPrice'] != $oldValues['price']) {
            if(checkChimneyPrice($_POST['chimneyPrice'])) {
                $newValues['price'] = $_POST['chimneyPrice'];

                $types .= "s";
            } else {
                return false;
            }
        }

        if($_SESSION['username'] != $oldValues['admin_name']) {
            $newValues['admin_name'] = $_SESSION['username'];

            $types .= "s";
        }

        $checkChimneyImageResult = checkChimneyImage();
        if($checkChimneyImageResult === true) { 
            $chimneyImageContent = file_get_contents($_FILES['chimneyImage']['tmp_name']);

            if($chimneyImageContent !== false) {
                $newValues['image'] = $chimneyImageContent;

                $types .= "b";
            } else {
                $chimneyErrorMessage = "Failed to read the image file";
            }
        } elseif($checkChimneyImageResult === false) {
            return false;
        }

        $types .= "s";

        return $newValues;
    }

    function saveValuesInForm() {
        global $valuesInForm,$chimneyErrorMessage;

        $valuesInForm['chimney_name'] = $_POST['chimneyName'];
        $valuesInForm['description'] = $_POST['chimneyDescription'];
        $valuesInForm['price'] = $_POST['chimneyPrice'];

        $checkChimneyImageResult = checkChimneyImage();
        if($checkChimneyImageResult === true) {
            $enteredImage = $_FILES['chimneyImage']['tmp_name'];
            $chimneyImageContent = file_get_contents($enteredImage);

            if($chimneyImageContent !== false) {
                $valuesInForm['image'] = $chimneyImageContent;
                $_SESSION['image'] = $chimneyImageContent;
                $_SESSION['isValidImage'] = true;
            } else {
                $chimneyErrorMessage = "Error reading the image file";
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
                    if(updateChimneyInDatabase($newValuesAssociativeArray,$types) === true) {
                        $chimneyErrorMessage = "Item edited successfully";

                    } else {
                        $chimneyErrorMessage = "Failed to update the item";
                    }
                } else { 
                    $chimneyErrorMessage = "No changes was made";
                }
            } 

            unset($_POST['editItem']);
            unset($_SESSION['oldValuesForEditedItem']);

           
        } else { 
            $chimneyName = $_POST['chimneyName'];
            $chimneyDescription = $_POST['chimneyDescription'];
            $chimneyPrice = $_POST['chimneyPrice'];
            $adminName = $_SESSION['username'];
        
            if(checkChimneyName($chimneyName) && checkChimneyDescription($chimneyDescription) && checkChimneyPrice($chimneyPrice)) {

                $checkChimneyImageResult = checkChimneyImage();
                if($checkChimneyImageResult === true) {
                    $chimneyImage = $_FILES['chimneyImage']['tmp_name'];
                    $chimneyImageContent = file_get_contents($chimneyImage);

                    if($chimneyImageContent !== false) {

                        addChimneyToDatabase($chimneyName,$chimneyDescription,$chimneyPrice,$chimneyImageContent,$adminName);

                        $chimneyErrorMessage = "Item added successfully";

                        if(isset($_SESSION['image'])) {
                            unset($_SESSION['image']);
                        }
                        

                    } else {
                        $chimneyErrorMessage = "Error reading the image file";
                    }
                } elseif($checkChimneyImageResult == 4 && isset($_SESSION['image'])) {
                    addChimneyToDatabase($chimneyName,$chimneyDescription,$chimneyPrice,$_SESSION['image'],$adminName);

                    $chimneyErrorMessage = "Item added successfully";

                    unset($_SESSION['image']);

                } elseif($checkChimneyImageResult == false  || ($checkChimneyImageResult == 4 && !isset($_SESSION['image'])) || ($checkChimneyImageResult == false && isset($_SESSION['image']))) {
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


            $query = "SELECT * FROM chimney";

            $result = mysqli_query($connect,$query);

            echo "<table class=\"itemsTable\">
                    <tr>
                        <th> chimney cake name </th>
                        <th> description </th>
                        <th> price </th>
                        <th> image </th>
                        <th> modified by </th>
                        <th> edit </th>
                        <th> delete </th>
                    </tr>";

            while(($column = mysqli_fetch_array($result)) != null) {
                echo "<tr>
                        <td> $column[chimney_name] </td>
                        <td> $column[description] </td>
                        <td> $column[price] </td>";
                    
                    $chimneyImageData = $column['image'];
                    $imageType = get_image_type($chimneyImageData);

                    if($imageType == 'jpeg') {
                        echo "<td> <img height=\"40px\" width=\"40px\" src='data:image/jpeg;base64," . base64_encode($chimneyImageData) . "'> </td>";
                    } else if($imageType == 'png') {
                        echo "<td> <img height=\"40px\" width=\"40px\" src='data:image/png;base64," . base64_encode($chimneyImageData) . "'> </td>";
                    }
                    
                    
                    echo "
                        <td> $column[admin_name] </td>
                        <td> <a href=\"view_items.php?chimneyName=$column[chimney_name]&submenuPageFromEdit=chimney_cakes.php\"> edit </a> </td>
                        <td> <a href=\"delete_item.php?itemName=$column[chimney_name]&nameAttributeInDb=chimney_name&tableName=chimney&pageSource=chimney_cakes.php\" onclick=\"return confirm('Are you sure you want to delete this item?')\"> delete </a> </td>
                    </tr>";
            }

            echo "</table>";
        ?>
    </div>

    <div>
        
        <form action="view_items.php" method="post" enctype="multipart/form-data" class="itemForm">
            <?php

                if(isset($_GET['submenuPageFromEdit'])) {
                    $query = "SELECT * FROM chimney WHERE chimney_name = '$_GET[chimneyName]'";

                    $valuesInForm = mysqli_query($connect,$query);
                    $valuesInForm = mysqli_fetch_array($valuesInForm);
                    
                    $_SESSION['oldValuesForEditedItem'] = $valuesInForm;

                    echo "<input type=\"hidden\" name=\"editItem\">";
                }

                echo "<label for=\"chimneyName\">Chimney Cake Name:</label><br>
                      <input type=\"text\" name=\"chimneyName\" value=\"$valuesInForm[chimney_name]\" required><br>";
            
                echo "<label for=\"chimneyDescription\">Description:</label><br>
                      <textarea name=\"chimneyDescription\" required>$valuesInForm[description]</textarea><br>";

                echo "<label for=\"chimneyPrice\">Price:</label><br>
                     <input type=\"number\" name=\"chimneyPrice\" step=\"0.01\" value=\"$valuesInForm[price]\" required><br>";

                if(isset($_GET['submenuPageFromEdit']) || isset($_SESSION['isValidImage'])) {
                    $chimneyImageData = $valuesInForm['image'];
                    $imageType = get_image_type($chimneyImageData);

                    echo "<div style=\"display:flex;align-items:center;margin-top:30px;\">Old Image:";
                    if($imageType == 'jpeg') {
                        echo "<img height=\"50px\" width=\"50px\" src='data:image/jpeg;base64," . base64_encode($chimneyImageData) . "'>";
                    } else if($imageType == 'png') {
                        echo "<img style=\"margin-top:50px;\" height=\"50px\" width=\"50px\" src='data:image/png;base64," . base64_encode($chimneyImageData) . "'>";
                    } 
                    
                    echo "</div>";
                }
                echo "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"1048576\">
                      <label for=\"chimneyImage\">Image:</label><br>
                      <input type=\"file\" name=\"chimneyImage\" accept=\"image/*\"><br>";
                
                if(!empty($chimneyErrorMessage)) {
                    echo "<p style=\"color:red;\"> $chimneyErrorMessage </p>";
                }
                
                echo "<input type=\"hidden\" name=\"submenuPage\" value=\"chimney_cakes.php\">
                      <input type=\"submit\" value=\"Submit\" name=\"submit\">";

            ?>
            
        </form>
        
    </div>

    
</div>
