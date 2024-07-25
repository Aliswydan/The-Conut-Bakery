<?php
require_once 'connect_to_server_and_database.php';
session_start();

$cookieName = 'loginInformation';
setcookie($cookieName,'',time() - 3600);
unset($_SESSION['user_id']);

$cookieName = 'loginInformation';
$cookieValue = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['username'];
    $phone = $_POST['phone_number'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $address = $_POST['address'];

    // Check if the email already exists in the database
    $checkEmailQuery = "SELECT * FROM User WHERE email = '$email'";
    $checkEmailResult = mysqli_query($connect, $checkEmailQuery);

    if (mysqli_num_rows($checkEmailResult) > 0) {
        // Email already exists, set an error message
        $errorMessage = 'Email already exists. Please use a different email.';
    
    } else {
        // Email does not exist, proceed with registration
        $query = "INSERT INTO User (user_name, phone_number, email, password, address) VALUES ('$name', '$phone', '$email', '$pass', '$address')";

        // Execute the query
        $result = mysqli_query($connect, $query);

        if ($result !== FALSE && mysqli_affected_rows($connect) > 0) {
            // Fetch the user_id from the newly inserted record
            $userResult = mysqli_query($connect, "SELECT user_id FROM User WHERE email ='$email' AND password='$pass'");
            $user = mysqli_fetch_assoc($userResult);

            // Store user_id in the session
            $_SESSION['user_id'] = $user['user_id'];

            $cookieValue = $email . "," . $pass;
            setcookie($cookieName, $cookieValue, time() + 3600);

            header("Location: index.php");
            exit();
        } else {
            // Registration failed
            $errorMessage = 'Registration failed. Please try again later.';
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="CSS/login.css" rel="stylesheet">
    <title>Sign up</title>
</head>
<body>
    <div id="login">
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <h1>Sign up</h1>

            <div class="form-text">
                <label for="username">Username:</label><br>
                <input type="text" name="username" id="username" required>
            </div>

            <div class="form-text">
                <label for="phone_number">Phone number:</label><br>
                <input type="tel" name="phone_number" id="phone_number" required>
            </div>

            <div class="form-text">
                <label for="email">Email:</label><br>
                <input type="email" name="email" id="email" required>
            </div>

            <div class="form-text">
                <label for="password">Password:</label><br>
                <input type="password" name="password" id="password" required>
            </div>

            <div class="form-text">
                <label for="address">Address:</label><br>
                <input type="text" name="address" id="address" required>
            </div>
            <div class="submit_button">
                <input type="submit" name="submit" value="Submit">
            </div>
            <?php
             if (!empty($errorMessage)) {
             echo "<div id=\"error-message\"> $errorMessage </div>";
             }
            ?>  

            

            
            <p class="signup-link">Have an account? <a href="login.php">Login</a></p>
            <p class="skip-link"><a href="index.php">Skip for now</a></p>
        </form>
    </div>

</body>
</html>
