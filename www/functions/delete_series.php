<?php
/**
 * Author: Zeinab Barakat
 * Last modified on: 12.05.2024
 * Title: Delete Series Page
 * Summary: This page handels the deletion of a series from the user's collection.
 *          It receives the series ID via POST request and delete the corresponding entry from the database. 
 */
session_start();
include '../functions/db_connection.php';

if (isset($_POST['series_id'])) {
    $seriesId = $_POST['series_id'];

    try {
        $sql = "DELETE FROM my_series WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $seriesId);
        $stmt->execute();

        echo "Series deleted successfully.";
    } catch (Exception $e) {
        echo "Failed to delete series: " . $e->getMessage();
    }
    exit;
}
?>
