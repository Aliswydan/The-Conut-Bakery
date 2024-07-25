<?php
require_once "connect_to_server_and_database.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
<script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script> 

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Conut Bakery | Feedback</title>
    <link rel="icon" href="img/logo.png" type="image/ico">

    <link rel="stylesheet" href="CSS/Feedback.css">
    <link rel="stylesheet" href="CSS/nav_bar.css">
    <link rel="stylesheet" href="CSS/footer.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans+Condensed:wght@500&display=swap" rel="stylesheet">

</head>

<body>
    <!-- Nav -->
    <nav><?php include 'nav_bar.php'; ?></nav>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $text = mysqli_real_escape_string($connect, $_POST['text']);
    $id = $_SESSION['user_id'];
    $errorMessage = '';

    $sql = "INSERT INTO Feedback (user_id, text) VALUES ('$id', '$text')";
    $result = mysqli_query($connect, $sql);

    if (!$result) {
        $errorMessage = "Error: " . mysqli_error($connect);
    } else {
        $errorMessage = "Feedback submitted successfully!";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $feedbackIdToDelete = $_POST['feedback_id'];
    $deleteQuery = "DELETE FROM Feedback WHERE feedback_id = $feedbackIdToDelete";
    $deleteResult = mysqli_query($connect, $deleteQuery);

    if (!$deleteResult) {
        $errorMessage = "Error deleting feedback: " . mysqli_error($connect);
    }
}
?>
    <!-- Main content -->
    <main>
        <div class="feedback">
            <h1>Give Us Your Feedback</h1>

            <!-- Feedback form -->
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <label>Your Feedback:</label>
                <textarea name="text" rows="3" cols="20">Customer's Feedback</textarea><br>
                
                <?php if (!isset($errorMessage)) { ?>
                    <button id="btnF" type="submit" name="submit">Submit your Feedback</button>
                <?php } else { ?>
                    <div id="error-message" style="color: red;"><?php echo $errorMessage; ?></div>
                <?php } ?>
            </form>
        </div>

        <div class="feedback-container">
            <?php
            $query = "SELECT text, date, user_id, feedback_id FROM Feedback;";
            $result2 = @mysqli_query($connect, $query);

            if ($result2) {
                while (($column = @mysqli_fetch_array($result2)) !== null) {
                    echo '<div class="feedback-card">';
                    echo '<h6>' . $column[0] . '</h6>';
                    echo '<p>Date: ' . $column[1] . '</p>';

                    if ($_SESSION['user_id'] == $column[2]) {
                        echo '<form method="POST">';
                        echo '<input type="hidden" name="feedback_id" value="' . $column[3] . '">';
                        echo '<button class="feedback-button" type="submit" name="delete">DELETE</button>';
                        echo '</form>';
                    }

                    echo '</div>';
                }
            }

            ?>
        </div>
    </main>

    <!-- Your existing code for Footer -->
    <footer>
    <?php include 'Footer.php'; ?>
</footer>
    </body>
    </html>
    <!----------------------------------------------------------- End ----------------------------------------------------------->