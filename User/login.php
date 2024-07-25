<?php
require_once 'connect_to_server_and_database.php';
session_start();

$cookieName = 'loginInformation';
setcookie($cookieName,'',time() - 3600);
unset($_SESSION['user_id']);
unset($_SESSION['conut_additive_id']);


$cookieValue = '';
$errorMessage = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $email = $_POST['email'];
    $pass = $_POST['password'];
    
    $result = mysqli_query($connect, "SELECT * FROM User WHERE email ='$email' AND password='$pass'");

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        // Store user_id in the session
        $_SESSION['user_id'] = $row['user_id'];

        $cookieValue = $email . "," . $pass;
        setcookie($cookieName, $cookieValue, time() + 3600);

        header("Location: index.php");
        exit();
    } else {
        $errorMessage = 'Invalid email or password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="CSS/login.css" rel="stylesheet">
    <title>Login</title>
</head>
<body>
    <div id="login">
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <h1>Login</h1>

            <div class="form-text">
                <label for="email">Email:</label><br>
                <input type="email" name="email" id="email" required>
            </div>

            <div class="form-text">
                <label for="password">Password:</label><br>
                <input type="password" name="password" id="password" required>
            </div>

            <div class="submit_button">  
                <input type="submit" name="submit" value="Submit">
            </div>

            <div id="error-message" style="color: red;"><?php echo $errorMessage; ?></div>
            
            <p class="signup-link">Don't have an account? <a href="signup.php">Sign up</a></p>
            <p class="skip-link"><a href="index.php">Skip for now</a></p>
        </form>
    </div>
</body>
</html>
