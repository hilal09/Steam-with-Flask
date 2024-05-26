<?php
/**
 * Author: Hilal Cubukcu
 * Last modified on: 12.05.2024
 * Title: Sign Up Page
 * Summary: This page allows users to sign up for a new account on Steam. If there's any error during registration, it will be displayed to the user.
 */

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up to Steam</title>
    <link rel="stylesheet" href="../style/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

</head>
<body class="register_login">
    <h1>Sign up to Steam<sup class="tm">TM</sup></h1>

    <form action="../functions/register.php" method="POST">
        <?php if (isset($_GET['error'])) { ?>
            <p class="error"><?php echo $_GET['error']; ?></p>
        <?php } ?>
        <label>Name</label>
        <input type="text" name="name" placeholder="Name">
        <label>E-Mail Address</label>
        <input type="text" name="email" placeholder="E-Mail Address">
        <label>Password</label>
        <input type="password" name="password" placeholder="Password">
        <p class="register-link">Already Signed Up? <a href="index.php">Click here to Log In.</a></p>

        <button type="submit">Sign Up</button>
    </form>
</body>
</html>
