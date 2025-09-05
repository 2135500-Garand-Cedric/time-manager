<?php
date_default_timezone_set('America/Toronto');
session_start();
require_once __DIR__ . '/db_conn.php';

// Check authentication
if (!isset($_COOKIE['tm_auth']) || $_COOKIE['tm_auth'] !== getenv('APP_SECRET')) {
    header("Location: index.php");
    exit;
}

// Get the new activity ID from POST
$newActivityId = intval($_POST['activity'] ?? 0);

if ($newActivityId > 0) {
    $mysqli->begin_transaction();

    try {
        // End the current activity (if any)
        $currentRes = $mysqli->query("SELECT id, start_time FROM records WHERE end_time IS NULL ORDER BY start_time DESC LIMIT 1");
        if ($currentRes && $currentRes->num_rows > 0) {
            $current = $currentRes->fetch_assoc();

            // Calculate duration in seconds
            $startTime = strtotime($current['start_time']);
            $endTime = time();
            $duration = $endTime - $startTime;

            // Update the record with end_time and duration
            $stmt = $mysqli->prepare("UPDATE records SET end_time = NOW(), duration_seconds = ? WHERE id = ?");
            $stmt->bind_param("ii", $duration, $current['id']);
            $stmt->execute();
            $stmt->close();
        }

        // Start the new activity
        $stmt = $mysqli->prepare("INSERT INTO records (activity_id, start_time) VALUES (?, NOW())");
        $stmt->bind_param("i", $newActivityId);
        $stmt->execute();
        $stmt->close();

        $mysqli->commit();
    } catch (Exception $e) {
        $mysqli->rollback();
        die("Failed to change activity: " . $e->getMessage());
    }
}

// Redirect back to the main app page
header("Location: app.php");
exit;
