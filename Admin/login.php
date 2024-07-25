<?php
    /*
        1-The session_start() firstly to use $_SESSION , i.e to set a session variables

        2-Connect to the server and database

        3-The first time an admin enters or it has logged out the cookie is not setted yet 
        and the script is waiting for the information to be filled

        4-Once the admin fills the info and clicks the button the script checks if the admin exist in the database

        5-If the admin exists in the database a session variables is setted, cookie is setted, and the redirection occurs to orders.php

        6-The second time the admin enters the webpage 
        the browser will send the cookie in the http request and the redirection happens directly

        7-When the admin logout the cookie is deleted and the process starts from the beginning

    */
    session_start();

    require_once 'connect_to_server_and_database.php';

    $cookieName = 'loginInformation';
    $cookieValue = '';
    $errorMessage = ''; 

    if(isset($_COOKIE[$cookieName])) {        
        $_SESSION['username'] = $_COOKIE[$cookieName];

        header('Location: orders.php');

        exit(0);
    } else { 

        if(isset($_POST['submit'])) {
            

            $username = $_POST['username'];
            $password = $_POST['password'];
 
            $query = "SELECT * FROM admin WHERE BINARY admin_name = '$username' AND BINARY password ='$password'";
            
            $result = @mysqli_query($connect,$query);

            if(@mysqli_num_rows($result) > 0) {
                $_SESSION['username'] = $username;
                
                $cookieValue = $username;
                setcookie($cookieName,$cookieValue,time() + 3600);

                header('Location: orders.php');
                exit(0);
            } else {
                $errorMessage = "invalid Username or Password";
            }
            
            
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title> Login </title>

    <link rel="stylesheet" type="text/css" href="CSS/login.css">
</head>

<body>
    <div id="login">
        <form method="post">
            <h1>Login</h1>

            <div class="form-text">
                <label for="username">Username:</label><br>
                <input type="text" name="username" required>
            </div>

            <div class="form-text">
                <label for="password">Password:</label><br>
                <input type="password" name="password"  required>
            </div>

            <div class="form-text">  
                <input class="submit_button" type="submit" name="submit" value="Submit">
            </div>
            
            <?php
                if(!empty($errorMessage)) {
                    echo "<div style=\"color:red;\"> $errorMessage </div>";
                }
                
            ?>
        </form>
    </div>
</body>

</html>
