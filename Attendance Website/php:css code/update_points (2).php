<?php
require('db.php');
require_once 'functions.php';

if (isset($_POST['user_id'], $_POST['event_id'], $_POST['new_points'])) {
    $user_id = $_POST['user_id'];
    $event_id = $_POST['event_id'];
    $new_points = $_POST['new_points'];

    // Update points for the specific event attendance
    $query = "UPDATE attendance SET points = ? WHERE user_id = ? AND event_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sii", $new_points, $user_id, $event_id);
    $stmt->execute();

    echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Update Status</title>";
    echo "<style>body { font-family: Arial, sans-serif; padding: 20px; text-align: center; }";
    echo ".message { margin-bottom: 20px; font-size: 20px; }";
    echo ".button { background-color: lightblue; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; margin: 5px; }";
    echo ".button:hover { background-color: deepskyblue; }</style></head><body>";

    if ($stmt->affected_rows > 0) {
        echo "Points for the event updated successfully.";
        // Recalculate total points after updating
        recalculateTotalPoints($user_id, $conn);
        echo "<p class='message'>Points updated successfully.</p>";
    } else {
        echo "<p class='message'>Error updating points: " . $conn->error . "</p>";
    }

    // Back to Edit Points button
    echo "<a href='edit_points.php?user_id=" . $user_id . "' class='button'>Back to Edit Points</a>";
    // Back to Admin Dashboard button
    echo "<a href='admin.php' class='button'>Back to Admin Dashboard</a>";

    echo "</body></html>";
} else {
    echo "Invalid request.";
}
?>