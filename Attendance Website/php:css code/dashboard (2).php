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
        ?>
    <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="path/to/css/styles.css">
    <title>User Dashboard</title>
    <style>
        body {
            background-color: thistle; /* Light purple background */
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        h2 {
            color: darkslateblue;
        }

        .event-list {
            margin-top: 20px;
        }

        .event-item {
            background-color: lavender;
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .event-item h3 {
            margin-top: 0;
        }

        .event-item p {
            margin: 5px 0;
        }
        .back-button {
            padding: 10px 20px;
            background-color: lightblue;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block; /* To style it like a button */
            margin-bottom: 20px; /* Spacing */
        }

        .back-button:hover {
            background-color: deepskyblue;
        }
    </style>
</head>
<body>
    <a href="event.php" class="back-button">Back to Events</a> 
    <a href="fake_dashboard.php" class="back-button">Plan Your Points</a>

    <?php
    require('db.php');
    
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    
        // Query for total points
        $pointsQuery = "SELECT points FROM users WHERE id = ?";
        $pointsStmt = $conn->prepare($pointsQuery);
        $pointsStmt->bind_param("i", $user_id);
        $pointsStmt->execute();
        $pointsResult = $pointsStmt->get_result();
        $userPoints = $pointsResult->fetch_assoc()['points'];
    
        // Display total points
        echo "<h2>Total Points: " . $userPoints . "</h2>";
        // New: Display points by category
        $categories = ['DEI', 'PHIL', 'SOC', 'PROF', 'GENERAL'];
        foreach ($categories as $category) {
            $catPointsQuery = "SELECT SUM(a.points) as total_cat_points 
                               FROM attendance a
                               JOIN events e ON a.event_id = e.event_id 
                               WHERE a.user_id = ? AND e.event_category = ?";
            $catPointsStmt = $conn->prepare($catPointsQuery);
            $catPointsStmt->bind_param("is", $user_id, $category);
            $catPointsStmt->execute();
            $catPointsResult = $catPointsStmt->get_result();
            $catRow = $catPointsResult->fetch_assoc();
            $totalCatPoints = $catRow['total_cat_points'] ?? 0;
            echo "<h3>" . $category . " Points: " . $totalCatPoints . "</h3>";
        }
    
    
        // Query for attended events
        $eventsQuery = "SELECT e.name, e.event_date, e.points, e.event_category FROM events e 
                        JOIN attendance a ON e.event_id = a.event_id 
                        WHERE a.user_id = ? ORDER BY e.event_date DESC";
        $eventsStmt = $conn->prepare($eventsQuery);
        $eventsStmt->bind_param("i", $user_id);
        $eventsStmt->execute();
        $eventsResult = $eventsStmt->get_result();
    
        // Display attended events
        echo "<div class='event-list'>";
        while ($row = $eventsResult->fetch_assoc()) {
            echo "<div class='event-item'>";
            echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
            echo "<p>Category: " . htmlspecialchars($row['event_category']) . "</p>";
            echo "<p>Date: " . htmlspecialchars($row['event_date']) . "</p>";
            echo "<p>Points: " . htmlspecialchars($row['points']) . "</p>";
            echo "</div>";
        }
        echo "</div>";
    } else {
        echo "<p>User not logged in.</p>";
    }
?>
</body>
</html>
