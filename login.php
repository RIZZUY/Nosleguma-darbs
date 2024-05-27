<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT id, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $hashed_password);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit();
    } else {
        $error_message = "Invalid username or password.";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
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
        <h2>Login</h2>
        <?php
        if (isset($error_message)) {
            echo '<div class="error-message">' . $error_message . '</div>';
        }
        ?>
        <form action="login.php" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>

</html>
