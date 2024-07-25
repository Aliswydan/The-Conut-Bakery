<?php
include_once 'connect_to_server_and_database.php';
session_start();

if (isset($_POST['conut_name'])) {
    $selectedConut = $_POST['conut_name'];

    // Insert 'conut_name' into ConutAdditive table
    $queryInsertConutAdditive = "INSERT INTO ConutAdditive (conut_name) VALUES (?)";
    $stmtInsert = mysqli_prepare($connect, $queryInsertConutAdditive);
    mysqli_stmt_bind_param($stmtInsert, 's', $selectedConut);
    $successInsert = mysqli_stmt_execute($stmtInsert);

    if ($successInsert) {
        // Insertion was successful, retrieve the ID
        $lastInsertId = mysqli_insert_id($connect);
        
        $_SESSION['conut_additive_id'] = $lastInsertId;
        $_SESSION['card_div'] = true;
        $_SESSION['conut_name'] = $_POST['conut_name'];

        header("Location: Conuts.php");
        exit();
    } 
}

if (isset($_POST['drink_name'])) {
    $selectedDrink = $_POST['drink_name'];

    // Insert 'drink_name' into DrinkAdditive table
    $queryInsertDrinkAdditive = "INSERT INTO DrinkAdditive (drink_name) VALUES (?)";
    $stmtInsert = mysqli_prepare($connect, $queryInsertDrinkAdditive);
    mysqli_stmt_bind_param($stmtInsert, 's', $selectedDrink);
    $successInsert = mysqli_stmt_execute($stmtInsert);

    if ($successInsert) {
        // Insertion was successful, retrieve the ID
        $lastInsertId = mysqli_insert_id($connect);
        
        $_SESSION['drink_additive_id'] = $lastInsertId;
        $_SESSION['card_div'] = true;
        $_SESSION['drink_name'] = $_POST['drink_name'];

        header("Location: Drinks.php");
        exit();
    } 
}


if (isset($_POST['chimney_name'])) {
    $selectedChimney = $_POST['chimney_name'];

    // Insert 'chimney_name' into ChimneyAdditive table
    $queryInsertChimneyAdditive = "INSERT INTO ChimneyAdditive (chimney_name) VALUES (?)";
    $stmtInsert = mysqli_prepare($connect, $queryInsertChimneyAdditive);
    mysqli_stmt_bind_param($stmtInsert, 's', $selectedChimney);
    $successInsert = mysqli_stmt_execute($stmtInsert);

    if ($successInsert) {
        // Insertion was successful, retrieve the ID
        $lastInsertId = mysqli_insert_id($connect);
        
        $_SESSION['chimney_additive_id'] = $lastInsertId;
        $_SESSION['card_div'] = true;
        $_SESSION['chimney_name'] = $_POST['chimney_name'];

        header("Location: Chimneys.php");
        exit();
    } 
}





// when cancel is clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['conut_additive_id_delete'])) {
    $SELECTED = $_POST['conut_additive_id_delete'];

    $sql1 = "DELETE FROM spreadadditivecount WHERE conut_additive_id = '$SELECTED';";
    $sqldelete1 = mysqli_query($connect, $sql1);


    $sql1 = "DELETE FROM toppingadditivecount WHERE conut_additive_id = '$SELECTED';";
    $sqldelete1 = mysqli_query($connect, $sql1);

    $sql2 = "DELETE FROM conutadditive WHERE conut_additive_id = '$SELECTED';";
    $sqldelete2 = mysqli_query($connect, $sql2);

    if (!$sqldelete1 || !$sqldelete2) {
        echo "Error: " . mysqli_error($connect);
    } else {
        unset($_SESSION['conut_additive_id']);
        header("Location: Conuts.php");
        exit();
    }
    
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['drink_additive_id_delete'])) {
    $SELECTED = $_POST['drink_additive_id_delete'];


    $sql1 = "DELETE FROM toppingadditivedrink WHERE drink_additive_id = '$SELECTED';";
    $sqldelete1 = mysqli_query($connect, $sql1);

    $sql2 = "DELETE FROM drinkadditive WHERE drink_additive_id = '$SELECTED';";
    $sqldelete2 = mysqli_query($connect, $sql2);

    if (!$sqldelete1 || !$sqldelete2) {
        echo "Error: " . mysqli_error($connect);
    } else {
        unset($_SESSION['drink_additive_id']);
        header("Location: Drinks.php");
        exit();
    }
    
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['chimney_additive_id_delete'])) {
    $SELECTED = $_POST['chimney_additive_id_delete'];

    $sql1 = "DELETE FROM spreadadditivechimney WHERE chimney_additive_id = '$SELECTED';";
    $sqldelete1 = mysqli_query($connect, $sql1);


    $sql1 = "DELETE FROM toppingadditivechimney WHERE chimney_additive_id = '$SELECTED';";
    $sqldelete1 = mysqli_query($connect, $sql1);

    $sql2 = "DELETE FROM chimneyadditive WHERE chimney_additive_id = '$SELECTED';";
    $sqldelete2 = mysqli_query($connect, $sql2);

    if (!$sqldelete1 || !$sqldelete2) {
        echo "Error: " . mysqli_error($connect);
    } else {
        unset($_SESSION['chimney_additive_id']);
        header("Location: Chimneys.php");
        exit();
    }
    
}

?>

