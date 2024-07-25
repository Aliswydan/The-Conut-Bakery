<?php
    /* 
        1.You cannot ensure that the admin will enter the right data
        So we need to perform some checks on the data
        i.e Perform a process of filtering and cleaning the entered data

        2.For the name of the conut, it should not exist in the database already, it should be all of small letters,
        does not contain a non alphabatical characters(numbers or special characters), does not begin or end with a space,
        and finally one space between words

        3.For the description of the conut, it should be all of small letters, hava only one space between words

        4.For the price of the conut, it should be positive value, and max of 2 decimal places

        5.For the image of the conut, the size should be at most 1MB, the file should be of type image, and there should be
        no errors in the uploading process
    */

    include_once 'connect_to_server_and_database.php';

    $conutErrorMessage = '';

    $valuesInForm = array(
        'conut_name' => '',
        'description' => '',
        'image' => '',
        'price' => '',
        'admin_name' => ''
    );

    if(isset($_SESSION['isValidImage'])) {
        unset($_SESSION['isValidImage']);
    }

    function checkConutName(&$conutName) { //By using & we are taking a reference to the variable
        global $connect,$conutErrorMessage; //This is to make the variable $connect.$conutErrorMessage accessible inside the function

        $conutName = trim($conutName);
        $conutName = ucwords($conutName);

        if(preg_match("/^[a-zA-Z]+( [a-zA-Z]+)*$/", $conutName)) {
            $checkQuery = "SELECT conut_name FROM conut where conut_name = '$conutName'";
            $result = mysqli_query($connect,$checkQuery);
            
            if(mysqli_num_rows($result) != 0) {
                $conutErrorMessage = "Name already exists in the database";

                return false;
            }

            return true;
        }

        $conutErrorMessage = "Name cannot contain non alphabatical characters or multiple spaces";
        return false; 
    }

    function checkConutDescription(&$conutDescription) {
        global $conutErrorMessage;

        $conutDescription = strtolower($conutDescription);
        $conutDescription = trim($conutDescription);
        if(preg_match("/^[a-z]+( [a-z]+)*$/", $conutDescription)) {
            return true;
        }

        $conutErrorMessage = "Multiple spaces between words or wrong entry in description";

        return false;
    }

    function checkConutPrice($price) {
        global $conutErrorMessage;

        if($price > 0) {
            if(!preg_match('/\\.\\d{3,}/', $price)) { //secondary check because the first check is done using step attribue
                return true;
            }

            $conutErrorMessage = "Price contains more than 2 decimal places";

            return false;
        }

        $conutErrorMessage = "Price cannot be negative or equal to 0";

        return false;
    }

    function checkConutImage() {
        global $conutErrorMessage;

        if($_FILES['conutImage']['error'] == 0) {
            if($_FILES['conutImage']['size'] <= $_POST['MAX_FILE_SIZE']) {
                if(getimagesize($_FILES['conutImage']['tmp_name'])) { //getimagesize will return false if the file is not an image
                    
                    return true;

                } else {
                    $conutErrorMessage = "File is not an image";

                    return false;
                }
            } else {
                $conutErrorMessage = "File size is larger than 1MB";

                return false;
            }
        } elseif($_FILES['conutImage']['error'] == UPLOAD_ERR_NO_FILE){ //which is equal to 4
             $conutErrorMessage = "No file was uploaded";

            return 4;
        } else {
            $conutErrorMessage = "Error in uploading the file";

            return false;
        }
    }

    /*
        file_get_contents read the file and returns its contents as a string
        this method should be used before inserting the file in the database
        We are also checking if the function succeed to read the file using === not == because
        the function might return an expression that evaluates to false but not boolean false itself
    */

    /*
        About addConutToDatabase() function:

        Purpose: to make a query that adds a new item to the database
    */

    function addConutToDatabase($conutName,$conutDescription,$conutPrice,$conutImageContent,$conutModifierAdmin) {
        global $connect;

        $stmt = $connect->prepare("INSERT INTO conut VALUES (?, ?, ?, ?, ?)");
        $null = NULL;
        $stmt->bind_param("ssbss", $conutName, $conutDescription, $null, $conutPrice, $conutModifierAdmin);
        $stmt->send_long_data(2, $conutImageContent);
        $stmt->execute();
    }

    /*
        About updateConutInDatabase() function:

        Purpose: to update the attributes of an item in the database, it is used with checkDifferencesAndValidate() function

        Return Types:
            1-false : in case the $newValues is empty meaning there is no attributes to update
            2-true : in case the function succeeds to update the attributes of an item

        How it works:
            -First it constructs the update statement
            -bind_param is a function that takes reference to the variables
            -meaning it needs them as $variable_name not value so we use $newValuesPointers
            -Then the function checks if there an image to use send_long_data() function
            -After that it calls the function call_user_func_array that is used to call the bind_param() function
            -in the $stmt object sending it an array of references
    */

    function updateConutInDatabase($newValues,$types) {
        global $connect;
        $oldValues = $_SESSION['oldValuesForEditedItem'];
        $oldItemName = $oldValues['conut_name'];
    
        if(empty($newValues)) {
            return false;
        }
        
        $updateQuery = "UPDATE conut SET ";
        foreach($newValues as $nameInDatabase => $value) {
            $updateQuery .= "$nameInDatabase = ?, "; 
        }
    
        $updateQuery = substr($updateQuery,0,-2); //to delete the last 2 characters
    
        $updateQuery .= " WHERE conut_name = ?";
    
        $stmt = $connect -> prepare($updateQuery);
        
        $newValuesPointers = array();
    
        foreach($newValues as &$value) {
            $newValuesPointers[] = &$value;
        }
    
        $positionOfImage = strpos($types,"b"); //strpos will return false if the substring is not found
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

    
    function saveValuesInForm() {
        global $valuesInForm,$conutErrorMessage;

        $valuesInForm['conut_name'] = $_POST['conutName'];
        $valuesInForm['description'] = $_POST['conutDescription'];
        $valuesInForm['price'] = $_POST['conutPrice'];

        $checkConutImageResult = checkConutImage();
        if($checkConutImageResult === true) {
            $enteredImage = $_FILES['conutImage']['tmp_name'];
            $conutImageContent = file_get_contents($enteredImage);

            if($conutImageContent !== false) {
                $valuesInForm['image'] = $conutImageContent;
                $_SESSION['image'] = $conutImageContent;
                $_SESSION['isValidImage'] = true;
            } else {
                $conutErrorMessage = "Error reading the image file";
            }
        } elseif(isset($_SESSION['image'])) {
            $_SESSION['isValidImage'] = true;
            $valuesInForm['image'] = $_SESSION['image'];
        }
    }    

    /*
        About checkDifferencesAndValidate() function:

        Purpose: to check what are the edited values of an item and validate them

        Return Types:
            1- false : in case there is an input issue when validating them or an issue of reading the image using file_get_content
            2-empty variable : $newValues will be returned empty if the admin does not make changes on the item
            3-variable with values : $newValues will be returned containing at least one value with its corresponding key if the admin
            makes a changes on the item
        
        How it works:
            -The function takes the old values of the edited item via a session variable
            -Also it takes a reference to the a variable $types to set the types of variable to be used in bind-param
            -Compare each value with the ones that are sent with the POST
            -If any changes to a value it validate the the value
            -If there is no problem in the value it adds it to the array with a key and add its type to $types
            -The key is the name of the input in the database
            -Finally it sets an "s" to types which is the item_name to be updated
    */
    function checkDifferencesAndValidateNewInputs(&$types) {
        $types = '';
        global $conutErrorMessage;
        $newValues = array();
        $oldValues = $_SESSION['oldValuesForEditedItem'];

        if($_POST['conutName'] != $oldValues['conut_name']) {
            if(checkConutName($_POST['conutName'])) {
                $newValues['conut_name'] = $_POST['conutName'];

                $types .= "s";
            } else {
                return false;
            }
        }

        if($_POST['conutDescription'] != $oldValues['description']) {
            if(checkConutDescription($_POST['conutDescription'])) {
                $newValues['description'] = $_POST['conutDescription'];

                $types .= "s";
            } else {
                return false;
            }
        }

        if($_POST['conutPrice'] != $oldValues['price']) {
            if(checkConutPrice($_POST['conutPrice'])) {
                $newValues['price'] = $_POST['conutPrice'];

                $types .= "s";
            } else {
                return false;
            }
        }

        if($_SESSION['username'] != $oldValues['admin_name']) {
            $newValues['admin_name'] = $_SESSION['username'];

            $types .= "s";
        }

        $checkConutImageResult = checkConutImage();
        if($checkConutImageResult === true) { //checkConutImage() function returned true not false or 4 
                                              //meaning a new image is uploaded with no errors
            $conutImageContent = file_get_contents($_FILES['conutImage']['tmp_name']);

            if($conutImageContent !== false) {
                $newValues['image'] = $conutImageContent;

                $types .= "b";
            } else {
                $conutErrorMessage = "Failed to read the image file";
            }
        } elseif($checkConutImageResult === false) {
            return false;
        }

        $types .= "s";

        return $newValues;
    }
    
    if(isset($_POST['submit'])) { //This is the precondition in the two cases: adding a new item or editing an existing item
        if(isset($_POST['editItem'])) { //This is if the admin wants to edit an existing item
            $types = '';
            $newValuesAssociativeArray = checkDifferencesAndValidateNewInputs($types);

            if($newValuesAssociativeArray !== false) {
                if(!empty($newValuesAssociativeArray)) {
                    if(updateConutInDatabase($newValuesAssociativeArray,$types) === true) {
                        $conutErrorMessage = "Item edited successfully";

                    } else {
                        $conutErrorMessage = "Failed to update the item";
                    }
                } else { //no changes was made to the item
                    $conutErrorMessage = "No changes was made";
                }
            } 

            unset($_POST['editItem']);
            unset($_SESSION['oldValuesForEditedItem']);

           
        } else { //Admin wants to add a new item
            $conutName = $_POST['conutName'];
            $conutDescription = $_POST['conutDescription'];
            $conutPrice = $_POST['conutPrice'];
            $adminName = $_SESSION['username'];
        
            if(checkConutName($conutName) && checkConutDescription($conutDescription) && checkConutPrice($conutPrice)) {

                $checkConutImageResult = checkConutImage();
                if($checkConutImageResult === true) {
                    $conutImage = $_FILES['conutImage']['tmp_name'];
                    $conutImageContent = file_get_contents($conutImage);

                    if($conutImageContent !== false) {

                        addConutToDatabase($conutName,$conutDescription,$conutPrice,$conutImageContent,$adminName);

                        $conutErrorMessage = "Item added successfully";

                        if(isset($_SESSION['image'])) {
                            unset($_SESSION['image']);
                        }
                        

                    } else {
                        $conutErrorMessage = "Error reading the image file";
                    }
                } elseif($checkConutImageResult == 4 && isset($_SESSION['image'])) {
                    addConutToDatabase($conutName,$conutDescription,$conutPrice,$_SESSION['image'],$adminName);

                    $conutErrorMessage = "Item added successfully";

                    unset($_SESSION['image']);

                } elseif($checkConutImageResult == false  || ($checkConutImageResult == 4 && !isset($_SESSION['image'])) || ($checkConutImageResult == false && isset($_SESSION['image']))) {
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

                return null;  // Unknown image type
            }


            $query = "SELECT * FROM conut";

            $result = mysqli_query($connect,$query);

            echo "<table class=\"itemsTable\">
                    <tr>
                        <th> conut name </th>
                        <th> description </th>
                        <th> price </th>
                        <th> image </th>
                        <th> modified by </th>
                        <th> edit </th>
                        <th> delete </th>
                    </tr>";

            while(($column = mysqli_fetch_array($result)) != null) {
                echo "<tr>
                        <td> $column[conut_name] </td>
                        <td> $column[description] </td>
                        <td> $column[price] </td>";
                    
                    $conutImageData = $column['image'];
                    $imageType = get_image_type($conutImageData);

                    if($imageType == 'jpeg') {
                        echo "<td> <img height=\"40px\" width=\"40px\" src='data:image/jpeg;base64," . base64_encode($conutImageData) . "'> </td>";
                    } else if($imageType == 'png') {
                        echo "<td> <img height=\"40px\" width=\"40px\" src='data:image/png;base64," . base64_encode($conutImageData) . "'> </td>";
                    }
                    
                    
                    echo "
                        <td> $column[admin_name] </td>
                        <td> <a href=\"view_items.php?conutName=$column[conut_name]&submenuPageFromEdit=conuts.php\"> edit </a> </td>
                        <td> <a href=\"delete_item.php?itemName=$column[conut_name]&nameAttributeInDb=conut_name&tableName=conut&pageSource=conuts.php\" onclick=\"return confirm('Are you sure you want to delete this item?')\"> delete </a> </td>
                    </tr>";
            }

            echo "</table>";
        ?>
    </div>

    <div>
        
        <form action="view_items.php" method="post" enctype="multipart/form-data" class="itemForm">
            <?php

                if(isset($_GET['submenuPageFromEdit'])) {
                    $query = "SELECT * FROM conut WHERE conut_name = '$_GET[conutName]'";

                    $valuesInForm = mysqli_query($connect,$query);
                    $valuesInForm = mysqli_fetch_array($valuesInForm);
                    
                    $_SESSION['oldValuesForEditedItem'] = $valuesInForm;

                    echo "<input type=\"hidden\" name=\"editItem\">";
                }

                echo "<label for=\"conutName\">Conut Name:</label><br>
                      <input type=\"text\" name=\"conutName\" value=\"$valuesInForm[conut_name]\" required><br>";
            
                echo "<label for=\"conutDescription\">Description:</label><br>
                      <textarea name=\"conutDescription\" required>$valuesInForm[description]</textarea><br>";

                echo "<label for=\"conutPrice\">Price:</label><br>
                     <input type=\"number\" name=\"conutPrice\" step=\"0.01\" value=\"$valuesInForm[price]\" required><br>";

                if(isset($_GET['submenuPageFromEdit']) || isset($_SESSION['isValidImage'])) {
                    $conutImageData = $valuesInForm['image'];
                    $imageType = get_image_type($conutImageData);

                    echo "<div style=\"display:flex;align-items:center;margin-top:30px;\">Old Image:";
                    if($imageType == 'jpeg') {
                        echo "<img height=\"50px\" width=\"50px\" src='data:image/jpeg;base64," . base64_encode($conutImageData) . "'>";
                    } else if($imageType == 'png') {
                        echo "<img style=\"margin-top:50px;\" height=\"50px\" width=\"50px\" src='data:image/png;base64," . base64_encode($conutImageData) . "'>";
                    } 
                    
                    echo "</div>";
                }
                echo "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"1048576\">
                      <label for=\"conutImage\">Image:</label><br>
                      <input type=\"file\" name=\"conutImage\" accept=\"image/*\"><br>";
                
                if(!empty($conutErrorMessage)) {
                    echo "<p style=\"color:red;\"> $conutErrorMessage </p>";
                }
                
                echo "<input type=\"hidden\" name=\"submenuPage\" value=\"conuts.php\">
                      <input type=\"submit\" value=\"Submit\" name=\"submit\">";

            ?>
            
        </form>
        
    </div>

    
</div> 