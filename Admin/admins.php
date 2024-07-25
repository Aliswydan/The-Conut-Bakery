<?php
    session_start();

    include_once 'connect_to_server_and_database.php';

    $message = '';

    function checkAdminName(&$adminName) {
        global $connect,$message;

        $adminName = trim($adminName);

        if(empty($adminName)) {
            $message = "Empty name";

            return false;
        }

        $checkUsernameQuery = "SELECT admin_name FROM admin WHERE BINARY admin_name = '$adminName'";
        $result = mysqli_query($connect,$checkUsernameQuery);

        if(mysqli_num_rows($result) == 0) {
            return true;
        }

        $message = "Admin name exists";

        return false;
    }

    function checkAdminPassword($password) {
        global $message;

        // At least 8 characters
        if(strlen($password) < 8) {
            $message = "The password should be at least 8 characters";

            return false;
        }
    
        // At least one uppercase letter
        if(!preg_match('/[A-Z]/', $password)) {
            $message = "The password should contains at least one uppercase letter";

            return false;
        }
    
        // At least one lowercase letter
        if(!preg_match('/[a-z]/', $password)) {
            $message = "The password should contains at least one lowercase letter";

            return false;
        }
    
        // At least one digit
        if(!preg_match('/\d/', $password)) {
            $message = "The password should contains at least one digit";

            return false;
        }
    
        // At least one special character
        if(!preg_match('/[^a-zA-Z\d]/', $password)) {
            $message = "The password should contains at least one special character";

            return false;
        }

        //should not contains spaces
        if(preg_match('/ /',$password)) {
            $message = "The password should not contain spaces";

            return false;
        }
    
        // If all checks pass, return true
        return true;
    }
    

    if(isset($_POST['submit'])) {
        $adminName = $_POST['adminName'];
        $password = $_POST['password'];

        if(checkAdminName(($adminName)) && checkAdminPassword($password)) {
            $insertQuery = "INSERT INTO admin VALUES('$adminName','$password')";

            mysqli_query($connect,$insertQuery);

            $message = "Admin inserted successfully";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title> Admins </title>
    
    <link rel="stylesheet" type="text/css" href="CSS/menu.css">
    <link rel="stylesheet" type="text/css" href="CSS/admins.css">
</head>

<body>

    <?php
        include 'menu_bar.php'
    ?>

    <div class="outer-div">
        <div> 
            <?php

                $query = "SELECT * FROM admin WHERE admin_name != 'root'";

                $result = @mysqli_query($connect,$query);

                echo "<table class=\"adminsTable\">";
                echo "<tr>
                        <th> Admin Name </th>
                        <th> Password </th>
                        <th> delete </th>
                      </tr>";
                while(($column = @mysqli_fetch_array($result)) != null) {
                    echo "<tr>
                            <td>  $column[0] </td>
                            <td>  $column[1] </td>
                            <td> <a href=\"delete_admin.php?adminName=$column[0]\" onclick=\"return confirm('Are you sure you want to delete this admin?')\">delete</a> </td>
                         </tr>";
                }
                echo "</table>";
            ?>
        </div>

        <div>
            <form method="post" class="adminForm">
                <h1> Add New Admin </h1>

                <label for="adminName">Admin Name:</label><br>
                <input type="text" name="adminName" required>

                <label for="password">Password:</label><br>
                <input type="password" name="password"  required>
                
  
                <input type="submit" name="submit" value="Submit">
                
                
                <?php
                    if(!empty($message)) {
                        echo "<div style=\"color:red; word-wrap:break-word;\"> $message </div>";
                    }
                    
                ?>
            </form>
        </div>
    </div>
    
</body>

</html>