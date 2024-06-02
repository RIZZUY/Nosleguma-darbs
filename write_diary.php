<?php
session_start();

include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$entry_date = isset($_POST['entry_date']) ? $_POST['entry_date'] : date('Y-m-d');
$entry_content = isset($_POST['entry_content']) ? $_POST['entry_content'] : '';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($entry_date) || empty($entry_content)) {
        $error = 'Date and entry content cannot be empty.';
    } else {
        $sql = "INSERT INTO diary_entries (user_id, date, entry) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE entry=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $user_id, $entry_date, $entry_content, $entry_content);

        if ($stmt->execute()) {
            $success = 'Diary entry saved successfully.';
        } else {
            $error = 'Error saving diary entry: ' . $stmt->error;
        }

        $stmt->close();
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
    <title>Write Diary Entry</title>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="content">
    <div class="diary-container">
        <h2>Write Diary Entry</h2>
        <?php if ($error): ?>
            <div class="error-container">
                <p class="error-message"><?php echo $error; ?></p>
            </div>
        <?php elseif ($success): ?>
            <div class="success-container">
                <p class="success-message"><?php echo $success; ?></p>
            </div>
        <?php endif; ?>
        <form action="write_diary.php" method="post">
            <label for="entry_date">Date:</label>
            <input type="date" id="entry_date" name="entry_date" value="<?php echo $entry_date; ?>" required>
            <textarea id="entry_content" name="entry_content" rows="10" placeholder="Write your diary entry here..." required><?php echo htmlspecialchars($entry_content); ?></textarea>
            <button type="submit">Save Entry</button>
        </form>
    </div>
</div>

</body>
</html>
