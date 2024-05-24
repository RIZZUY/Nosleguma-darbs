<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

$date = isset($_GET['date']) ? $_GET['date'] : '';

$sql = "DELETE FROM diary_entries WHERE date = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $date, $_SESSION['user_id']);
$result = $stmt->execute();

if ($result) {
    $_SESSION['message'] = 'Diary entry deleted successfully!';
} else {
    $_SESSION['message'] = 'Error deleting diary entry: ' . $stmt->error;
}

$stmt->close();
$conn->close();

header("Location: index.php");
exit();
?>
