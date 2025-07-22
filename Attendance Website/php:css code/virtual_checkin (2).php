<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from the AJAX request
    $eventId = intval($_POST['event_id']);
    $points = intval($_POST['points']);
    $action = $_POST['action'];

    // Initialize session variables if not set
    if (!isset($_SESSION['virtual_checkins'])) {
        $_SESSION['virtual_checkins'] = [];
        $_SESSION['total_points'] = 0;
    }

    // Add or remove the event from the virtual check-in list
    if ($action === 'add' && !in_array($eventId, $_SESSION['virtual_checkins'])) {
        $_SESSION['virtual_checkins'][] = $eventId;
        $_SESSION['total_points'] += $points;
    } elseif ($action === 'remove' && in_array($eventId, $_SESSION['virtual_checkins'])) {
        $_SESSION['virtual_checkins'] = array_diff($_SESSION['virtual_checkins'], [$eventId]);
        $_SESSION['total_points'] -= $points;
    }

    // Calculate category-wise points
    require('db.php');
    $user_id = $_SESSION['user_id'];
    $categoryPoints = [];

    // Fetch existing category points
    $categories = ['DEI', 'PHIL', 'SOC', 'PROF', 'GENERAL'];
    foreach ($categories as $category) {
        $catPointsQuery = "SELECT SUM(a.points) AS total_cat_points 
                           FROM attendance a
                           JOIN events e ON a.event_id = e.event_id 
                           WHERE a.user_id = ? AND e.event_category = ?";
        $catPointsStmt = $conn->prepare($catPointsQuery);
        $catPointsStmt->bind_param("is", $user_id, $category);
        $catPointsStmt->execute();
        $catPointsResult = $catPointsStmt->get_result();
        $existingPoints = $catPointsResult->fetch_assoc()['total_cat_points'] ?? 0;

        $categoryPoints[$category] = $existingPoints;
    }

    // Add virtual check-in points to category points
    if (!empty($_SESSION['virtual_checkins'])) {
        $eventIds = implode(',', array_map('intval', $_SESSION['virtual_checkins']));
        $query = "SELECT event_category, SUM(points) AS category_points 
                  FROM events 
                  WHERE event_id IN ($eventIds) 
                  GROUP BY event_category";
        $result = $conn->query($query);

        while ($row = $result->fetch_assoc()) {
            $category = $row['event_category'];
            $virtualPoints = $row['category_points'];
            $categoryPoints[$category] += $virtualPoints;
        }
    }

    // Fetch the list of events the user has virtually checked into
    $virtualEvents = [];
    if (!empty($_SESSION['virtual_checkins'])) {
        $eventIds = implode(',', array_map('intval', $_SESSION['virtual_checkins']));
        $eventsQuery = "SELECT name, event_date, points, event_category 
                        FROM events 
                        WHERE event_id IN ($eventIds)";
        $eventsResult = $conn->query($eventsQuery);

        while ($row = $eventsResult->fetch_assoc()) {
            $virtualEvents[] = [
                'name' => $row['name'],
                'date' => $row['event_date'],
                'points' => $row['points'],
                'category' => $row['event_category']
            ];
        }
    }

    // Return the updated total points, category points, and virtual events
    echo json_encode([
        'totalPoints' => $_SESSION['total_points'],
        'categoryPoints' => $categoryPoints,
        'virtualEvents' => $virtualEvents
    ]);
}
?>
