<?php
session_start();
require('db.php');

if (isset($_POST['event_id']) && isset($_SESSION['user_id'])) {
    $event_id = $_POST['event_id'];
    $user_id = $_SESSION['user_id'];

    // Check if the user has already checked into this event
    $checkAttendanceQuery = "SELECT * FROM attendance WHERE user_id = ? AND event_id = ?";
    $checkAttendanceStmt = $conn->prepare($checkAttendanceQuery);
    $checkAttendanceStmt->bind_param("ii", $user_id, $event_id);
    $checkAttendanceStmt->execute();
    $checkAttendanceResult = $checkAttendanceStmt->get_result();

    echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Check-in Status</title>";
    echo "<style>body { background-color: lavender; color: purple; font-family: Arial, sans-serif; text-align: center; padding-top: 50px; }";
    echo ".message { margin-bottom: 20px; font-size: 20px; }";
    echo ".dashboard-link, .events-link { display: inline-block; background-color: purple; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; margin-top: 20px; }";
    echo ".dashboard-link:hover, .events-link:hover { background-color: deepskyblue; }</style></head><body>";

    if ($checkAttendanceResult->num_rows > 0) {
        echo "<div class='message'>You have already checked into this event.</div>";
        echo "<a href='event.php' class='events-link'>Back to Events</a>";
    } else {
        $eventDateQuery = "SELECT event_date FROM events WHERE event_id = ?";
        $eventDateStmt = $conn->prepare($eventDateQuery);
        $eventDateStmt->bind_param("i", $event_id);
        $eventDateStmt->execute();
        $eventDateResult = $eventDateStmt->get_result();
        $eventDateRow = $eventDateResult->fetch_assoc();

        if (date("Y-m-d") == $eventDateRow['event_date']) {
            $conn->begin_transaction();
            try {
                $pointsQuery = "SELECT points FROM events WHERE event_id = ?";
                $pointsStmt = $conn->prepare($pointsQuery);
                $pointsStmt->bind_param("i", $event_id);
                $pointsStmt->execute();
                $pointsResult = $pointsStmt->get_result();
                $eventPoints = $pointsResult->fetch_assoc()['points'];

                $attendanceQuery = "INSERT INTO attendance (user_id, event_id, attended_date, points) VALUES (?, ?, CURDATE(), ?)";
                $attendanceStmt = $conn->prepare($attendanceQuery);
                $attendanceStmt->bind_param("iid", $user_id, $event_id, $eventPoints);
                $attendanceStmt->execute();

                $updatePointsQuery = "UPDATE users SET points = points + ? WHERE id = ?";
                $updatePointsStmt = $conn->prepare($updatePointsQuery);
                $updatePointsStmt->bind_param("di", $eventPoints, $user_id);
                $updatePointsStmt->execute();

                $conn->commit();
                echo "<div class='message'>Check-in successful. Points updated.</div>";
                echo "<a href='dashboard.php' class='dashboard-link'>View My Dashboard</a>";
            } catch (mysqli_sql_exception $exception) {
                $conn->rollback();
                echo "<div class='message'>Error: " . $exception->getMessage() . "</div>";
                echo "<a href='event.php' class='events-link'>Back to Events</a>";
            }
        } else {
            echo "<div class='message'>Check-in is only available on the day of the event.</div>";
            echo "<a href='event.php' class='events-link'>Back to Events</a>";
}
} echo "</body></html>";
} else {
// Redirect to login page if not logged in or event_id is not set
header("Location: login.php");
exit();
}
?>