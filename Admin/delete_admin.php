<?php
    include 'connect_to_server_and_database.php';

    $adminName = $_GET['adminName']; 
    $deleteQuery = "DELETE FROM admin WHERE admin_name = '$adminName'";

    mysqli_query($connect,$deleteQuery);

    header("Location: admins.php");

    exit();
?>