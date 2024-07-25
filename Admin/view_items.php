<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Items </title>
    
    <link rel="stylesheet" type="text/css" href="CSS/menu.css">
    <link rel="stylesheet" type="text/css" href="CSS/submenu.css">
    <link rel="stylesheet" type="text/css" href="CSS/submenu_items.css">

</head>

<body>

    <?php
        include 'menu_bar.php'
    ?>

    <?php
        include 'submenu.php';

        if(isset($_GET['submenuPage'])) {
            include $_GET['submenuPage'];
        } elseif(isset($_POST['submenuPage'])) {
            include $_POST['submenuPage'];
        } elseif(isset($_SESSION['submenuPage'])) {
            include $_SESSION['submenuPage'];

            unset($_SESSION['submenuPage']);
        } elseif(isset($_GET['submenuPageFromEdit'])) {
            include $_GET['submenuPageFromEdit'];
        }
    ?>
    
    

    
</body>

</html>