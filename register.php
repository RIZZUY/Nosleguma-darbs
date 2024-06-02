<?php
include 'db.php';
session_start();

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username)) {
        $error_message = "Username is required.";
    } elseif (strpos($username, ' ') !== false) {
        $error_message = "Username cannot contain spaces.";
    } elseif (strlen($password) < 4) {
        $error_message = "Password must be at least 4 characters long.";
    } elseif (strpos($password, ' ') !== false) {
        $error_message = "Password cannot contain spaces.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        $check_sql = "SELECT id FROM diary_users WHERE username = ?";
        if ($stmt = $conn->prepare($check_sql)) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error_message = "Username already exists. Please choose another.";
            } else {
                $stmt->close();
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $default_picture = '/vanags.atwebpages.com/diary/profile_pictures/defaultpicture.png';

                $sql = "INSERT INTO diary_users (username, password, profile_picture) VALUES (?, ?, ?)";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("sss", $username, $hashed_password, $default_picture);
                    if ($stmt->execute()) {
                        header("Location: login.php");
                        exit();
                    } else {
                        $error_message = "Error: " . $sql . "<br>" . $conn->error;
                    }
                    $stmt->close();
                } else {
                    $error_message = "Error preparing SQL statement: " . $conn->error;
                }
            }
            $stmt->close();
        } else {
            $error_message = "Error preparing SQL statement: " . $conn->error;
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Register</title>
    <script>
        setTimeout(function() {
            var errorMsg = document.querySelector('.error-message');
            if (errorMsg) {
                errorMsg.style.display = 'none';
            }
        }, 5000);
    </script>
</head>
<body>
    <div class="form-container">
        <h2>Register</h2>
        <?php
        if (!empty($error_message)) {
            echo '<div class="error-message">' . $error_message . '</div>';
        }
        ?>
        <form action="register.php" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
