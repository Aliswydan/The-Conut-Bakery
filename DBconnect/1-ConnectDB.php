<?php

// Database connection parameters
$host = 'localhost';
$username = 'root';
$password = '';

// Create a connection to the MySQL server
$connect = mysqli_connect($host, $username, $password);

// Check the connection
if (!$connect) {
    die("Failed to connect: " . mysqli_connect_error());
}






?>




