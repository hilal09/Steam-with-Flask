<?php
/**
 * Author: Hilal Cubukcu
 * Last modified on: 12.05.2024
 * Title: Database Connection
 * Summary: This script establishes a connection to the MySQL database.
 */

$servername = "localhost";
$name = "root"; 
$password = ""; 
$database = "steam"; 

//Create connection
$conn = mysqli_connect($servername, $name, $password, $database);

// Check connection
if (!$conn) {
    echo "Connection failed!";
}
?>
