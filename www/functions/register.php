<?php
/**
 * Author: Hilal Cubukcu
 * Last modified on: 12.05.2024
 * Title: Registration Script
 * Summary: This script handles user registration for Steam. It validates user inputs, checks if the email is already registered, hashes the password, inserts the user into the database, and redirects to the dashboard upon successful registration.
 */

include 'db_connection.php';

$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];

if (empty($name) || empty($email) || empty($password)) {
    header("Location: ../pages/registration.php?error=Please fill in the fields.");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../pages/registration.php?error=Invalid e-mail.");
    exit();
}

$sql = "SELECT id FROM user_accounts WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    header("Location: ../pages/registration.php?error=E-mail taken.");
    exit();
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO user_accounts (name, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $name, $email, $hashed_password);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $user_id = $stmt->insert_id;

    header("Location: ../pages/dashboard.php");
    exit();
} else {
    header("Location: ../pages/registration.php?error=registrationfailed");
    exit();
}

$stmt->close();
$conn->close();
?>
