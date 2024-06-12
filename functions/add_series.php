<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/index.php");
    exit();
}

$title = $_POST['title'];
$year = $_POST['year'];
$seasons = $_POST['seasons'];
$genre = $_POST['genre'];
$platform = $_POST['platform'];
$picture = $_POST['picture'];
$rating = $_POST['rating'];

$data = [
    'title' => $title,
    'year' => $year,
    'seasons' => $seasons,
    'genre' => $genre,
    'platform' => $platform,
    'picture' => $picture,
    'rating' => $rating
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost:5000/api/series");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$response_data = json_decode($response, true);

if (isset($response_data['success'])) {
    header("Location: dashboard.php");
} else {
    echo "Fehler beim Hinzufügen der Serie";
}
?>
