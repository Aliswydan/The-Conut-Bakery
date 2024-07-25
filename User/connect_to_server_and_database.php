<?php
    // Database connection parameters
    $host = 'localhost';
    $username = 'root';
    $password = '';

    // Create a connection to the MySQL server
    $connect = @mysqli_connect($host, $username, $password);
    if(!$connect) {
        echo "<script type=\"text/javascript\"> alert(\"Failed to connect to database\"); </script>"; 
    }


    $databaseName = "Bakery";
    if(!(@mysqli_select_db($connect,$databaseName))) {
        echo "<script type=\"text/javascript\"> alert(\"Failed to select database\"); </script>";
    }


?>