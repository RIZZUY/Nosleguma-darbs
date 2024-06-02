<?php
include 'db.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$profile_message = '';
$password_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_picture'])) {
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['profile_picture']['tmp_name'];
        $file_name = basename($_FILES['profile_picture']['name']);
        $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if ($file_type === 'jpg' || $file_type === 'jpeg' || $file_type === 'png') {
            $upload_dir = 'profile_pictures/';
            $new_file_name = $_SESSION['username'] . '.' . $file_type;
            $upload_path = $upload_dir . $new_file_name;
            
            if (move_uploaded_file($file_tmp, $upload_path)) {
                $sql = "UPDATE diary_users SET profile_picture = ? WHERE username = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $new_file_name, $_SESSION['username']);
                $stmt->execute();
                $stmt->close();

                $_SESSION['profile_picture'] = $new_file_name;
                $profile_message = '<div class="success-message">Profile picture updated successfully.</div>';
            } else {
                $profile_message = '<div class="error-message">Failed to upload the profile picture.</div>';
            }
        } else {
            $profile_message = '<div class="error-message">Invalid file type. Only JPG, JPEG, and PNG are allowed.</div>';
        }
    } else {
        $profile_message = '<div class="error-message">Failed to upload the profile picture. Please try again.</div>';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (strlen($new_password) < 4) {
        $password_message = '<div class="error-message">Password must be at least 4 characters long.</div>';
    } elseif (strpos($new_password, ' ') !== false) {
        $password_message = '<div class="error-message">Password cannot contain spaces.</div>';
    } elseif ($new_password !== $confirm_password) {
        $password_message = '<div class="error-message">New password and confirm password do not match.</div>';
    } else {
        $sql = "SELECT password FROM diary_users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $_SESSION['username']);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($hashed_password);
            $stmt->fetch();
            if (password_verify($old_password, $hashed_password)) {
                $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_sql = "UPDATE diary_users SET password = ? WHERE username = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("ss", $hashed_new_password, $_SESSION['username']);
                $update_stmt->execute();
                $update_stmt->close();
                $password_message = '<div class="success-message">Password updated successfully.</div>';
            } else {
                $password_message = '<div class="error-message">Old password is incorrect.</div>';
            }
        } else {
            $password_message = '<div class="error-message">User not found.</div>';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .box {
            width: 48%;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .box h2 {
            margin-bottom: 20px;
        }

        input[type="file"],
        input[type="password"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: inline-block;
            width: 100%;
            padding: 10px;
            margin-top: 10px;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .error-message {
            color: #f00;
            margin-bottom: 10px;
        }

        .success-message {
            color: #008000;
            margin-bottom: 10px;
        }

        img.profile-picture {
            max-width: 200px;
            border-radius: 50%;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
    <div class="box">
        <h2>Update Profile Picture</h2>
        <?php
        if ($profile_message) {
            echo $profile_message;
        }

        $profile_picture = 'profile_pictures/' . $_SESSION['profile_picture'];
        if (file_exists($profile_picture)) {
            echo '<img src="' . $profile_picture . '" alt="Profile Picture" class="profile-picture">';
        } else {
            echo '<p>No profile picture found</p>';
        }
        ?>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="file" name="profile_picture" accept="image/png, image/jpeg">
            <input type="submit" name="update_picture" value="Update Profile Picture">
        </form>
    </div>

    <div class="box">
        <h2>Change Password</h2>
        <?php
        if ($password_message) {
            echo $password_message;
        }
        ?>
        <form action="" method="post">
            <input type="password" name="old_password" placeholder="Old Password" required>
            <input type="password" name="new_password" placeholder="New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <input type="submit" name="update_password" value="Change Password">
        </form>
    </div>
</div>

</body>
</html>
