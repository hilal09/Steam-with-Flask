<?php
/**
 * Author: Melisa Rosic Emira
 * Last modified on: 12.05.2024
 * Title: Title: User Account Functions
 * Summary: This script retrieves user account information from the database and provides functions to get user details such as ID, name, email, and avatar.
 */
require 'db_connection.php';

$sql = "SELECT * FROM user_accounts WHERE id = 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    
    while($row = $result->fetch_assoc()) {
        $user_ID = $row['id'];
        $name = $row['name'];
        $email = $row['email'];
        $avatar = $row['avatar'];
        
    }
}

function getUserID (){
    echo $user_ID;
}

function getUserName (){
    echo $name;
}

function getUserEmail (){
    echo $email;
}

function getUserAvatar (){
    echo $avatar;
}

?>