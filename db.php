<?php

$servername = "fdb34.awardspace.net";
$username = "3931241_vanags";
$password = "R18DJ!G21DZ5";
$db = "3931241_vanags";

$conn = new mysqli($servername, $username, $password, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
