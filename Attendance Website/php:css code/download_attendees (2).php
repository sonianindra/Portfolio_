<?php
session_start();

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirect to login page if not logged in as admin
    header('Location: admin_login.php');
    exit();
}

require('db.php'); // Ensure this path is correct

// Check if the event ID is set
if (isset($_GET['event_id'])) {
    $eventId = intval($_GET['event_id']);

    // Prepare SQL query to fetch usernames of attendees
    $query = "SELECT u.username FROM users u
              JOIN attendance a ON u.id = a.user_id
              WHERE a.event_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Set headers to download the file rather than display it
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="event_attendees.csv"');

    // Open the file in write mode
    $output = fopen('php://output', 'w');

    // Add column headers
    fputcsv($output, array('Username'));

    // Fetch and write the data to the CSV
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit();
} else {
    echo "No event ID specified.";
}
?>