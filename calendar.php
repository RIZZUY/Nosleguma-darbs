<?php
include 'db.php';

function getDiaryEntry($date, $user_id, $conn) {
    $sql = "SELECT entry FROM diary_entries WHERE date = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $date, $user_id);
    $stmt->execute();
    $stmt->bind_result($entry);
    $stmt->fetch();
    $stmt->close();

    return isset($entry) ? $entry : null;
}

function getFirstDayOfMonth($year, $month) {
    return date('N', strtotime("$year-$month-01"));
}

$currentYear = isset($_GET['year']) ? $_GET['year'] : date('Y');
$currentMonth = isset($_GET['month']) ? $_GET['month'] : date('n');
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);
$firstDayOfMonth = getFirstDayOfMonth($currentYear, $currentMonth);

$prevMonthYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;
$prevMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;

$nextMonthYear = $currentMonth == 12 ? $currentYear + 1 : $currentYear;
$nextMonth = $currentMonth == 12 ? 1 : $currentMonth + 1;

$calendarURL = 'calendar.php';
?>

<div class="calendar">
    <div class="month"><?php echo date('F Y', strtotime("$currentYear-$currentMonth-01")); ?></div>
    <div class="navigation">
        <a href="<?php echo $calendarURL . '?year=' . $prevMonthYear . '&month=' . $prevMonth; ?>">&lt; Prev</a>
        <a href="<?php echo $calendarURL . '?year=' . date('Y') . '&month=' . date('n'); ?>">Current Month</a>
        <a href="<?php echo $calendarURL . '?year=' . $nextMonthYear . '&month=' . $nextMonth; ?>">Next &gt;</a>
    </div>
    <div class="days">
        <?php
        $daysOfWeek = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
        foreach ($daysOfWeek as $day) {
            echo '<div class="day-name">' . $day . '</div>';
        }

        $firstDayPosition = $firstDayOfMonth - 1;
        for ($i = 0; $i < $firstDayPosition; $i++) {
            echo '<div class="day"></div>';
        }

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = sprintf('%d-%02d-%02d', $currentYear, $currentMonth, $i);
            $entry = getDiaryEntry($date, $_SESSION['user_id'], $conn);
            $class = $date == date('Y-m-d') ? 'current-date' : '';
            echo '<div class="day ' . $class . '"><a href="view_diary.php?date=' . $date . '">' . $i . '</a></div>';
        }
        ?>
    </div>
</div>

<link rel="stylesheet" href="style.css">
