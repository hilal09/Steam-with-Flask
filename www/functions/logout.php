<?php
/**
 * Author: Hilal Cubukcu
 * Last modified on: 12.05.2024
 * Title: Logout Script
 * Summary: This script handles the logout process for Steam. It destroys the session and redirects the user to the login page.
 */

session_start();

session_unset();

session_destroy();

header("Location: ../pages/index.php");
?>
