<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/index.php");
    exit();
}

$series_id = $_POST['series_id'];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost:5000/api/series/$series_id");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$response_data = json_decode($response, true);

if (isset($response_data['success'])) {
    header("Location: dashboard.php");
} else {
    echo "Fehler beim Löschen der Serie";
}
?>
