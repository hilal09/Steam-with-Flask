<?php
/**
 * Author: Melisa Rosic Emira
 * Last modified on: 12.05.2024
 * Title: Profile Page
 * Summary: This script fetches user data from the database for the profile page. Users can update their information (name, email and password) and choose an avatar, with an option to delete their account.
 */

session_start();
require '../functions/db_connection.php';
$user_ID = $_SESSION['user_id'];
$sql = "SELECT * FROM user_accounts WHERE id = $user_ID";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $user_ID = $row['id'];
        $name = $row['name'];
        $email = $row['email'];
        $password = $row['password'];
        $avatar = $row['avatar'];
        $_SESSION['avatar'] = $row['avatar'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="../style/style.css?v<?php echo time(); ?>"> 
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

</head>
<body>
    <?php include "../widgets/navigation.php"; ?>
    <button class="sidebar-toggle">&#9776;</button>
    <div class="profile-container">
        <aside class="profile-sidebar">
            <div class="profile-user-card">
                <div class="profile-user-avatar">
                    <img src="../uploads/avatars/<?php echo $avatar ?>"  alt="User Avatar">
                </div>
                <div class="profile-user-info">
                    <h2><?php echo $name; ?></h2>
                </div>
            </div>
            <nav class="profile-navigation">
                <ul>
                    <li><a href="#profile-section" class="nav-link">My Account</a></li>
                </ul>
                <ul>
                    <li><a href="../functions/logout.php" class="nav-link"> Log out</a></li>
                </ul>
            </nav>
        </aside>
        <main class="profile-main">
            <section id="profile-section" class="profile-content active">
                <form class="account-form" action="../functions/profile_function.php" method="POST" enctype="multipart/form-data">
                    <h1>Account Settings</h1>
                    <input type="hidden" name="userId" value="<?php echo $user_ID ?>">
                    <div class="form-group">
                        <label for="full-name">Name</label>
                        <input type="text" id="full-name" name="full_name" value="<?php echo $name ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo $email ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" value="<?php echo $password ?>">
                    </div>
                    <h1>Avatar</h1>
                    <div class="form-group">
                        <ul>
                        <li><input type="radio" name="cat1" id="cb1" />
                            <label class = "avatar-image-label" for="cb1"><img src="../uploads/avatars/cat1.jpg" /></label>
                        </li>
                        <li><input type="radio" name="cat2" id="cb2" />
                            <label class = "avatar-image-label" for="cb2"><img src="../uploads/avatars/cat2.jpg" /></label>
                        </li>
                        <li><input type="radio" name="cat3" id="cb3" />
                            <label class = "avatar-image-label" for="cb3"><img src="../uploads/avatars/cat3.jpg" /></label>
                        </li>
                        <li><input type="radio" name="cat4" id="cb4" />
                            <label class = "avatar-image-label" for="cb4"><img src="../uploads/avatars/cat4.jpg" /></label>
                        </li>
                        </ul>
                    </div>
                    <button type="submit" class="save-changes-button">Save Changes</button>
                </form>
                <form class="account-form" action="../functions/profile_function.php" method="POST">
                    <input type="hidden" name="userId" value="<?php echo $user_ID ?>">
                    <div class="form-group">
                        <h2>Account Removal</h2>
                        <button type="submit" value="1" name="delete-account" class="btn-delete-account">Delete my account</button>
                    </div>
                </form>
            </section>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toggle = document.querySelector('.sidebar-toggle');
            var sidebar = document.querySelector('.profile-sidebar');

            toggle.addEventListener('click', function () {
                sidebar.classList.toggle('active');
            });
        });

    </script>
</body>
</html>


