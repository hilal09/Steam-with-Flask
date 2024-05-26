<?php
/**
 * Author: Hilal Cubukcu
 * Last modified on: 12.05.2024
 * Title: Login Page
 * Summary: This page allows users to sign in to their Steam account. If the user is already logged in, they are redirected to the dashboard page.
 */

session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: ../pages/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login to Steam</title>
    <link rel="stylesheet" href="../style/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

</head>
    <body class="register_login">
        <h1>Sign In to Steam<sup class="tm">TM</sup></h1>
        <form  action="../functions/login.php" method="POST">
            <?php if (isset($_GET['error'])) { ?>
                <p class="error"><?php echo $_GET['error']; ?></p>
            <?php } ?>
            <label>E-Mail Address </label>
            <input type="text" name="email" placeholder="E-Mail Address">
            <label>Password</label>
            <input type="password" name="password" placeholder="Password">

            <p class="register-link">Didn't sign up yet? <a href="registration.php">Click here to register.</a></p>

            <button type="submit">Sign In</button>
        </form>
    </body>
</html>
