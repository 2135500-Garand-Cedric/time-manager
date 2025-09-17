<?php
session_start();
require_once __DIR__ . '/db_conn.php';

// Check authentication
if (!isset($_COOKIE['tm_auth']) || $_COOKIE['tm_auth'] !== getenv('APP_SECRET')) {
    header("Location: index.php");
    exit;
}

// Get all activities
$activities = [];
$res = $mysqli->query("SELECT id, name FROM activities ORDER BY name ASC");
while ($row = $res->fetch_assoc()) {
    $activities[] = $row;
}

// Get current active activity
$currentActivity = null;
$recordRes = $mysqli->query("SELECT r.id, r.activity_id, r.start_time, a.name 
                             FROM records r 
                             JOIN activities a ON r.activity_id = a.id 
                             WHERE r.end_time IS NULL 
                             ORDER BY r.start_time DESC 
                             LIMIT 1");
if ($recordRes->num_rows > 0) {
    $currentActivity = $recordRes->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Time Manager</title>
<link rel="stylesheet" href="styles.css">
</head>
<body class="app-body">
    <!-- Top-right button -->
    <div class="top-right-button">
        <a href="edit_records">
            <button type="button">Edit Past Activities</button>
        </a>
    </div>
    <div class="app-container">

        <?php if ($currentActivity): ?>
            <p class="current-activity-name"><?php echo htmlspecialchars($currentActivity['name']); ?></p>
            <p class="elapsed-time" id="elapsed-time" data-start="<?php echo $currentActivity['start_time']; ?>"></p>
        <?php else: ?>
            <p class="no-activity">No activity running</p>
        <?php endif; ?>

        <form method="POST" action="change_activity.php" class="activity-form">
            <select name="activity" id="activity" required>
                <option value="">-- Choose activity --</option>
                <?php foreach ($activities as $act): ?>
                    <?php if ($currentActivity && $act['id'] == $currentActivity['activity_id']) continue; ?>
                    <option value="<?php echo $act['id']; ?>"><?php echo htmlspecialchars($act['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Change</button>
        </form>


    </div>
    <script src="timer.js"></script>
</body>
</html>
