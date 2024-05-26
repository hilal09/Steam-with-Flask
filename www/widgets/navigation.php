<!-- 
Author: Yudum Yılmaz
Last modified on: 12.05.2024
Title: Navigation Bar
Summary: This navigation bar provides links for various pages.
-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigation Bar</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
</head>
<body>
    <nav class="navbar">
        <ul>
            <li><a href="../pages/dashboard.php" <?php if(basename($_SERVER['PHP_SELF']) == 'dashboard.php') echo 'class="active"'; ?>><i class="fa-solid fa-house"></i>Home</a></li>
            <li><a href="../pages/search.php" <?php if(basename($_SERVER['PHP_SELF']) == 'search.php') echo 'class="active"'; ?>><i class="fa-solid fa-magnifying-glass"></i>Search</a></li>
            <li><a href="../pages/profile.php" <?php if(basename($_SERVER['PHP_SELF']) == 'profile.php') echo 'class="active"'; ?>><i class="fa-solid fa-user"></i>Profile</a></li>
        </ul>
    </nav>
</body>
</html>