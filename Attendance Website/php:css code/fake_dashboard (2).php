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

require('db.php');

// Fetch user's actual points from the database
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch actual points
    $pointsQuery = "SELECT points FROM users WHERE id = ?";
    $pointsStmt = $conn->prepare($pointsQuery);
    $pointsStmt->bind_param("i", $user_id);
    $pointsStmt->execute();
    $pointsResult = $pointsStmt->get_result();
    $actualPoints = $pointsResult->fetch_assoc()['points'] ?? 0;

    // Initialize virtual check-ins if not set
    if (!isset($_SESSION['virtual_checkins'])) {
        $_SESSION['virtual_checkins'] = [];
    }

    // Recalculate total points (real + virtual)
    $_SESSION['total_points'] = $actualPoints;

    if (!empty($_SESSION['virtual_checkins'])) {
        $eventIds = implode(',', array_map('intval', $_SESSION['virtual_checkins']));
        $virtualPointsQuery = "SELECT SUM(points) AS virtual_points FROM events WHERE event_id IN ($eventIds)";
        $virtualPointsResult = $conn->query($virtualPointsQuery);
        $virtualPoints = $virtualPointsResult->fetch_assoc()['virtual_points'] ?? 0;
        $_SESSION['total_points'] += $virtualPoints;
    }

    // Fetch category-specific points
    $categories = ['DEI', 'PHIL', 'SOC', 'PROF', 'GENERAL'];
    $categoryPoints = [];
    foreach ($categories as $category) {
        $catPointsQuery = "SELECT SUM(a.points) AS total_cat_points 
                           FROM attendance a
                           JOIN events e ON a.event_id = e.event_id 
                           WHERE a.user_id = ? AND e.event_category = ?";
        $catPointsStmt = $conn->prepare($catPointsQuery);
        $catPointsStmt->bind_param("is", $user_id, $category);
        $catPointsStmt->execute();
        $catPointsResult = $catPointsStmt->get_result();
        $categoryPoints[$category] = $catPointsResult->fetch_assoc()['total_cat_points'] ?? 0;
    }

    // Add virtual category points
    if (!empty($_SESSION['virtual_checkins'])) {
        $virtualCategoriesQuery = "SELECT event_category, SUM(points) AS category_points 
                                   FROM events 
                                   WHERE event_id IN ($eventIds) 
                                   GROUP BY event_category";
        $virtualCategoriesResult = $conn->query($virtualCategoriesQuery);

        while ($row = $virtualCategoriesResult->fetch_assoc()) {
            $category = $row['event_category'];
            $categoryPoints[$category] += $row['category_points'];
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="path/to/css/styles.css"> <!-- Link to your existing stylesheet -->
    <title>Fake Dashboard with Check-Ins</title>
    <style>
        body {
            background-color: thistle; /* Match the main dashboard */
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        h2, h3 {
            color: darkslateblue;
        }

        .event-list, .virtual-event-list {
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
            display: inline-block;
            margin-bottom: 20px;
        }

        .back-button:hover {
            background-color: deepskyblue;
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
            background-color: lavender;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
    <script>
        function updateVirtualCheckin(eventId, points, action) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "virtual_checkin.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);

                    // Update total points
                    document.getElementById('totalPoints').innerText = response.totalPoints;

                    // Update category points
                    for (const [category, points] of Object.entries(response.categoryPoints)) {
                        const categoryElement = document.getElementById(`category-${category}`);
                        if (categoryElement) {
                            categoryElement.innerText = points;
                        }
                    }

                    // Update the fake checked-in events list
                    const virtualEventList = document.getElementById('virtualEventList');
                    virtualEventList.innerHTML = ''; // Clear the list
                    response.virtualEvents.forEach(event => {
                        const eventItem = document.createElement('div');
                        eventItem.classList.add('event-item');
                        eventItem.innerHTML = `
                            <h3>${event.name}</h3>
                            <p>Category: ${event.category}</p>
                            <p>Date: ${event.date}</p>
                            <p>Points: ${event.points}</p>
                        `;
                        virtualEventList.appendChild(eventItem);
                    });
                }
            };
            xhr.send(`event_id=${eventId}&points=${points}&action=${action}`);
        }
    </script>
</head>
<body>
    <a href="dashboard.php" class="back-button">Back to Dashboard</a>

    <h2>Virtual Dashboard</h2>
    <p>Total Points: <span id="totalPoints"><?php echo $_SESSION['total_points'] ?? 0; ?></span></p>

    <h3>Category Points (Existing + Virtual):</h3>
    <?php
    foreach ($categories as $category) {
        $totalCategoryPoints = $categoryPoints[$category] ?? 0;
        echo "<p>$category: <span id='category-$category'>$totalCategoryPoints</span> points</p>";
    }
    ?>

    <h3>Upcoming Events</h3>
    <?php
    $eventsQuery = "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC";
    $eventsResult = $conn->query($eventsQuery);

    if ($eventsResult->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>Event Name</th><th>Date</th><th>Points</th><th>Category</th><th>Action</th></tr>";
        while ($event = $eventsResult->fetch_assoc()) {
            $eventId = htmlspecialchars($event['event_id']);
            $points = htmlspecialchars($event['points']);
            $category = htmlspecialchars($event['event_category']);
            echo "<tr>";
            echo "<td>" . htmlspecialchars($event['name']) . "</td>";
            echo "<td>" . htmlspecialchars($event['event_date']) . "</td>";
            echo "<td>" . htmlspecialchars($event['points']) . "</td>";
            echo "<td>" . htmlspecialchars($event['event_category']) . "</td>";
            echo "<td>";
            echo "<button onclick=\"updateVirtualCheckin('$eventId', $points, 'add')\">Check In</button>";
            echo "<button onclick=\"updateVirtualCheckin('$eventId', $points, 'remove')\">Check Out</button>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No upcoming events to display.</p>";
    }
    ?>

    <h3>Fake Checked-In Events</h3>
    <div id="virtualEventList" class="virtual-event-list">
        <?php
        if (!empty($_SESSION['virtual_checkins'])) {
            $eventIds = implode(',', array_map('intval', $_SESSION['virtual_checkins']));
            $virtualEventsQuery = "SELECT name, event_date, points, event_category FROM events WHERE event_id IN ($eventIds)";
            $virtualEventsResult = $conn->query($virtualEventsQuery);

            while ($row = $virtualEventsResult->fetch_assoc()) {
                echo "<div class='event-item'>";
                echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
                echo "<p>Category: " . htmlspecialchars($row['event_category']) . "</p>";
                echo "<p>Date: " . htmlspecialchars($row['event_date']) . "</p>";
                echo "<p>Points: " . htmlspecialchars($row['points']) . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p>No events checked in virtually.</p>";
        }
        ?>
    </div>
</body>
</html>
