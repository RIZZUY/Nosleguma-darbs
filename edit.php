<?php
include 'db.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$date = isset($_GET['date']) ? $_GET['date'] : '';
$errorMessage = $successMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $newEntry = htmlspecialchars($_POST['new_entry']);
    $newEntry = mysqli_real_escape_string($conn, $newEntry);

    if (empty($newEntry)) {
        $errorMessage = 'Error: Diary entry cannot be empty.';
    } else {

        $stmt = $conn->prepare("UPDATE diary_entries SET entry = ? WHERE date = ?");
        $stmt->bind_param("ss", $newEntry, $date);

        if ($stmt->execute()) {
            $successMessage = 'Diary entry updated successfully!';
        } else {
            $errorMessage = 'Error updating diary entry: ' . $conn->error;
        }

        $stmt->close();
    }
}

$sql = "SELECT entry FROM diary_entries WHERE date = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $date, $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($currentEntry);

if ($stmt->fetch()) {
} else {
    $currentEntry = '';
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Edit Diary Entry</title>
    <style>
        .content {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .edit-diary-container {
            background-color: #f9f9f9;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .message-box {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .error-message {
            background-color: #ffebee;
            color: #b71c1c;
        }

        .success-message {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        textarea {
            width: calc(100% - 40px);
            height: 200px;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: calc(100% - 40px);
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        body {
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="content edit-diary-container">
    <h2>Edit Diary Entry for <?php echo $date; ?></h2>

    <?php if (!empty($errorMessage)): ?>
        <div class="message-box error-message">
            <?php echo $errorMessage; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($successMessage)): ?>
        <div class="message-box success-message">
            <?php echo $successMessage; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <textarea name="new_entry"><?php echo $currentEntry; ?></textarea><br>
        <input type="submit" value="Save Changes">
    </form>
</div>

</body>
</html>
