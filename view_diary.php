<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

$user_id = $_SESSION['user_id'];
$date = isset($_GET['date']) ? $_GET['date'] : '';
$entry = '';

if (!empty($date)) {
    $sql = "SELECT entry FROM diary_entries WHERE user_id = ? AND date = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $date);
    $stmt->execute();
    $stmt->bind_result($entry);
    $stmt->fetch();
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Diary Entry</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="content">
    <div class="diary-container">
        <?php if (!empty($entry)): ?>
            <div class="diary-entry-box">
                <h2>Diary Entry for <?php echo $date; ?></h2>
                <p><?php echo nl2br($entry); ?></p>
                <div class="button-container">
                    <a href="edit.php?date=<?php echo $date; ?>" class="action-button edit-button">Edit</a>
                    <a href="delete.php?date=<?php echo $date; ?>" class="action-button delete-button" onclick="return confirm('Are you sure you want to delete this entry?')">Delete</a>
                </div>
            </div>
        <?php else: ?>
            <div class="diary-entry-box">
                <h2>No Diary Entry for <?php echo $date; ?></h2>
                <p>Sorry, there is no diary entry available for this date.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
