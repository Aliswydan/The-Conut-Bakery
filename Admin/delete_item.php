<?php
    session_start();

    include 'connect_to_server_and_database.php';

    $itemName = $_GET['itemName'];
    $tableName = $_GET['tableName'];
    $nameAttribute = $_GET['nameAttributeInDb'];

    $deleteQuery = "DELETE FROM $tableName WHERE $nameAttribute = '$itemName'";

    mysqli_query($connect,$deleteQuery);

    $_SESSION['submenuPage'] = $_GET['pageSource'];

    header('Location: view_items.php');

    exit();
?>