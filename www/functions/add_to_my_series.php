<?php
/*
* Author: Yudum Yilmaz
* Last modified on: 12.05.2024
* Title: Add to My Series Handler
* Summary: This script handles the addition of default series to the user's personal collection ("My Series").
* It retrieves the information of the selected default series from the database and inserts it into the user's personal collection.
* Note: Some functionalities are not fully implemented:
* - The script does not prevent adding the same series multiple times without error notification.
* - Images from the database are not currently loaded.
* - Instead of a popup, the user is redirected to a separate page, which is not the intended behavior.
*/

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/index.php");
    exit();
}

include "db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $default_series_id = $_POST['default_series_id'];
    $user_id = $_SESSION['user_id'];

    $default_series_sql = "SELECT * FROM default_series WHERE id = ?";
    $stmt = mysqli_prepare($conn, $default_series_sql);
    mysqli_stmt_bind_param($stmt, "i", $default_series_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {

        $insert_sql = "INSERT INTO my_series (user_id, title, year, seasons, genre, platform, picture) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_sql);
        mysqli_stmt_bind_param($stmt, "isissbs", $user_id, $row['title'], $row['year'], $row['seasons'], $row['genre'], $row['platform'], $row['picture_url']);
        mysqli_stmt_execute($stmt);
        

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo "Default series added to My Series successfully.";
        } else {
            echo "Error adding default series to My Series.";
        }
    } else {
        echo "Default series not found.";
    }
}

mysqli_close($conn);
?>