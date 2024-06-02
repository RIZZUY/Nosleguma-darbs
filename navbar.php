<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION['username']) && file_exists('profile_pictures/' . $_SESSION['username'] . '.png')) {
    $profile_picture = 'profile_pictures/' . $_SESSION['username'] . '.png';
} else {
    $profile_picture = 'pfp.jpg';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Navbar</title>
</head>
<body>
    <div class="navbar-toggle" id="navbarToggle">☰</div>
    <div class="navbar" id="navbar">
        <a href="write_diary.php" <?php if(basename($_SERVER['PHP_SELF']) == 'write_diary.php') echo 'class="active"'; ?>>Write Diary</a>
        <a href="index.php" <?php if(basename($_SERVER['PHP_SELF']) == 'index.php') echo 'class="active"'; ?>>View Calendar</a>
        <a href="edit_profile.php" class="profile-picture-link"><img src="<?php echo $profile_picture; ?>" alt="Profile Picture" class="profile-picture"></a>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var navbarToggle = document.getElementById('navbarToggle');
            var navbar = document.getElementById('navbar');

            navbarToggle.addEventListener('click', function() {
                navbar.classList.toggle('active');
            });

            function checkWidth() {
                if (window.innerWidth > 768) {
                    navbar.classList.add('desktop');
                    navbar.classList.remove('active');
                    navbarToggle.style.display = 'none';
                } else {
                    navbar.classList.remove('desktop');
                    navbarToggle.style.display = 'block';
                }
            }

            checkWidth();
            window.addEventListener('resize', checkWidth);
        });
    </script>
</body>
</html>
