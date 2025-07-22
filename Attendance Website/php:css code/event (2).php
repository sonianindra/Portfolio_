<?php
    session_start();

    // Prevent caching
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    
    // Check if the user is logged in
    if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
        header('Location: login.php'); // Redirect to login page
        exit();
    }
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include('db.php');
    $query = "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC";
    $result = $conn->query($query);

?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="path/to/css/styles.css">
<title>Event Listing</title>
    <style>
        body {
            background-color: lightyellow; /* Light yellow background */
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .button-link {
            background-color: rebeccapurple;
            color: yellow;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 3px;
            margin-right: 8px; /* Space between buttons */
            transition: background-color 0.3s;
        }

        .button-link:hover {
            background-color: darkslateblue;
        }

        .top-nav {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f0e68c;
        }

        tr:nth-child(even) {
            background-color: #f5f5f5;
        }

        tr:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <div class="top-nav">
        <a href="dashboard.php" class="button-link">Dashboard</a>
        <a href="leaderboard.php" class="button-link">Leaderboard</a>
        <a href="info.php" class="button-link">Rules</a>
        <a href="announcements.php" class="button-link">Links</a>
        <a href="logout.php" class="button-link">Logout</a>
    </div>

    <h1>Upcoming Events</h1>
    <?php
    if ($result->num_rows > 0) {
        echo "<table>";
    // Added Category in the table header
        echo "<tr><th>Event Name</th><th>Date</th><th>Points</th><th>Category</th><th>Check In</th></tr>";
     while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['event_date']) . "</td>";
        echo "<td>" . htmlspecialchars($row['points']) . "</td>";
        // Displaying the event category
        echo "<td>" . htmlspecialchars($row['event_category']) . "</td>";
        echo "<td>";

        // Show check-in button only if it's the day of the event
        if (date("Y-m-d") == $row["event_date"]) {
            echo "<form method='post' action='checkin.php'>
                    <input type='hidden' name='event_id' value='" . $row['event_id'] . "'>
                    <input type='submit' value='Check In'>
                  </form>";
        } else {
            echo "Check-in available on event day";
        }

        echo "</td></tr>";
    }
    echo "</table>";
    } else {
    echo "<p>No upcoming events.</p>";
    }
    ?>

</body>
</html>