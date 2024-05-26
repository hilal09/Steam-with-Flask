<?php
/**
 * Author: Hilal Cubukcu & Zeinab Barakat
 * Last modified on: 12.05.2024
 * Title: Add Series Page
 * Summary: This page allows logged-in users to add a new series to their collection. It processes the form data and inserts the series into the database.
 */

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/index.php");
    exit();
}

include "db_connection.php";

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $year = $_POST['year'];
    $seasons = $_POST['seasons'];
    $genre = $_POST['genre'];
    $platform = $_POST['platform'];

    $picture = null;
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
        $picture = file_get_contents($_FILES['picture']['tmp_name']);
    }

    $stmt = $conn->prepare("INSERT INTO my_series (user_id, title, year, seasons, genre, platform, picture) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ississs", $user_id, $title, $year, $seasons, $genre, $platform, $picture);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    header("Location: ../pages/dashboard.php");
    exit();
}
?>
