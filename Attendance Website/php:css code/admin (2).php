<?php
    session_start();
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: admin_login.php');
        exit();
    }
    require('db.php'); // Make sure this path is correct

    // SQL query to fetch user data and their total points directly from the users table
    $query = "SELECT id, username, points FROM users";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>Username</th><th>Total Points</th><th>Edit Points</th></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
            echo "<td>" . htmlspecialchars($row['points']) . "</td>";
            echo "<td><a href='edit_points.php?user_id=" . $row['id'] . "'>Edit Points</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No users found.";
    }
    // Fetch events for downloading attendee lists
    $eventQuery = "SELECT event_id, name FROM events";
    $eventsResult = $conn->query($eventQuery);
    echo "<h2>Download Attendee List</h2>";
    echo "<form action='download_attendees.php' method='get'>";
    echo "<select name='event_id'>";
    while ($event = $eventsResult->fetch_assoc()) {
        echo "<option value='" . $event['event_id'] . "'>" . htmlspecialchars($event['name']) . "</option>";
    }
    echo "</select>";
    echo "<input type='submit' value='Download'>";
    echo "</form>";
    mysqli_data_seek($eventsResult, 0);
    echo "<h2>Download Non-Attendee List</h2>";
    echo "<form action='download_nonattendees.php' method='get'>";
    echo "<select name='event_id'>";
    while ($event = $eventsResult->fetch_assoc()) {
        echo "<option value='" . $event['event_id'] . "'>" . htmlspecialchars($event['name']) . "</option>";
    }
    echo "</select>";
    echo "<input type='submit' value='Download Non-Attendees'>";
    echo "</form>";

    $conn->close();
    ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="path/to/css/styles.css">
    <title>Admin Dashboard</title>
    
</head>
<body>

    <!-- Logout Link -->
    <p><a href="admin_logout.php">Logout</a></p>
    
</body>
</html>