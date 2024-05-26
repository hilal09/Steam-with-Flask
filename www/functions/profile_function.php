<?php
/**
 * Author: Melisa Rosic Emira
 * Last modified on: 12.05.2024
 * Title: Profile Page Functionality
 * Summary: This script interacts with the database to handle account deletion and update user profile details, such as name, email, avatar, and password.  It maintains data integrity and redirects users as needed.
 */

session_start();
require 'db_connection.php'; 

$userId = $_POST['userId']; 

if (isset($_POST['delete-account'])) {
    $conn->begin_transaction();

    try {
       $sql = "DELETE FROM my_series WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        $sql = "DELETE FROM user_accounts WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        session_destroy();

        $conn->commit();
        header('Location: ../pages/index.php'); 
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        echo "Failed to delete account: " . $e->getMessage();
    }
}


$name = $_POST['full_name'];
$email = $_POST['email'];
$avatar = '';

if (isset($_POST['cat1'])) {
    $avatar = "cat1.jpg";
} elseif (isset($_POST['cat2'])) {
    $avatar = "cat2.jpg";
} elseif (isset($_POST['cat3'])) {
    $avatar = "cat3.jpg";
} elseif (isset($_POST['cat4'])) {
    $avatar = "cat4.jpg";
} else {
    if (isset($_SESSION['avatar'])) {
        $avatar = $_SESSION['avatar'];
    }
}

$password = $_POST['password'];
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql = "UPDATE user_accounts SET avatar = ?, name = ?, email = ?, password = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $avatar, $name, $email, $hashed_password, $userId);

if ($stmt->execute()) {
    header('Location: ../pages/profile.php');
    exit;
} else {
    echo "Update failed: " . $conn->error;
}

?>
