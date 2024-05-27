<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Your Diary</title>
    <style>
        .message-box {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
            color: #333;
            text-align: center;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="content">
    <?php
    if (isset($_SESSION['message'])) {
        echo '<div class="message-box" id="message-box">' . $_SESSION['message'] . '</div>';
        unset($_SESSION['message']);
    }
    ?>
    <div class="calendar-container" id="calendar-container">
        <?php include 'calendar.php'; ?>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var messageBox = document.getElementById("message-box");
        if (messageBox) {
            setTimeout(function() {
                messageBox.style.display = "none";
            }, 5000);
        }

        function loadCalendar(url) {
            fetch(url)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('calendar-container').innerHTML = html;
                    bindNavigationLinks();
                })
                .catch(error => console.warn(error));
        }

        function bindNavigationLinks() {
            document.querySelectorAll('.calendar .navigation a').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    loadCalendar(this.href);
                });
            });
        }

        bindNavigationLinks();
    });
</script>

</body>
</html>
