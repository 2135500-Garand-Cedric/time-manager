<?php
session_start();
require_once __DIR__ . '/db_conn.php';

// Check authentication
if (!isset($_COOKIE['tm_auth']) || $_COOKIE['tm_auth'] !== getenv('APP_SECRET')) {
    header("Location: index.php");
    exit;
}
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $record_id = intval($_POST['record_id']);
    $new_end_time = $_POST['end_time'];

    // Fetch current record
    $res = $mysqli->query("SELECT * FROM records WHERE id = $record_id");
    if ($res->num_rows > 0) {
        $record = $res->fetch_assoc();

        $start_ts = strtotime($record['start_time']);
        $end_ts = strtotime($new_end_time);

        if ($start_ts < $end_ts) {
            $duration_seconds = $end_ts - $start_ts;

            // Update current record's end_time and duration_seconds
            $stmt = $mysqli->prepare("UPDATE records SET end_time = ?, duration_seconds = ? WHERE id = ?");
            $stmt->bind_param("sii", $new_end_time, $duration_seconds, $record_id);
            $stmt->execute();
            $stmt->close();

            // Update next activity's start_time
            $nextRes = $mysqli->query("SELECT id FROM records WHERE start_time > '{$record['start_time']}' ORDER BY start_time ASC LIMIT 1");
            if ($nextRes->num_rows > 0) {
                $nextRecord = $nextRes->fetch_assoc();
                $stmt2 = $mysqli->prepare("UPDATE records SET start_time = ? WHERE id = ?");
                $stmt2->bind_param("si", $new_end_time, $nextRecord['id']);
                $stmt2->execute();
                $stmt2->close();
            }

            $message = "End time and duration updated successfully!";
        } else {
            $error = "End time must be after the start time.";
        }
    }
}

// Fetch last 50 past activities
$records = [];
$res = $mysqli->query("SELECT r.id, a.name, r.start_time, r.end_time 
                       FROM records r 
                       JOIN activities a ON r.activity_id = a.id
                       WHERE r.end_time IS NOT NULL
                       ORDER BY r.start_time DESC
                       LIMIT 50");
while ($row = $res->fetch_assoc()) {
    $records[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Past Activities</title>
<link rel="stylesheet" href="styles.css">
</head>
<body class="app-body">
<!-- Top-right button -->
<div class="top-right-button">
    <a href="app">
        <button type="button">Main Page</button>
    </a>
</div>
<div class="app-container">
    <h1>Edit Past Activities</h1>

    <?php if (isset($message)) echo "<p class='success'>$message</p>"; ?>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

    <table>
        <tbody>
            <?php foreach ($records as $rec): ?>
            <tr>
                <form method="POST">
                    <td><?php echo htmlspecialchars($rec['name']); ?></td>
                    <td>
                        <input type="datetime-local" name="end_time" 
                               value="<?php echo date('Y-m-d\TH:i', strtotime($rec['end_time'])); ?>" required>
                    </td>
                    <td>
                        <input type="hidden" name="record_id" value="<?php echo $rec['id']; ?>">
                        <button type="submit">OK</button>
                    </td>
                </form>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
