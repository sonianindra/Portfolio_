<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require('db.php');
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

// Fetch user's total points by category
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

// Fetch attended events of the user for editing points
$eventsQuery = "SELECT e.event_id, e.name FROM events e 
                JOIN attendance a ON e.event_id = a.event_id 
                WHERE a.user_id = ?";
$eventsStmt = $conn->prepare($eventsQuery);
$eventsStmt->bind_param("i", $user_id);
$eventsStmt->execute();
$eventsResult = $eventsStmt->get_result();


?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Edit User Event Points</title>
    <!-- Add any additional CSS here -->
</head>
<body>
   

    <form action="update_points.php" method="post">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

        <label for="event_id">Select Event:</label>
        <select name="event_id" id="event_id" required>
            <?php
            while ($row = $eventsResult->fetch_assoc()) {
                echo "<option value='" . $row['event_id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
            }
            ?>
        </select><br>

        <label for="new_points">New Points:</label>
        <input type="number" id="new_points" name="new_points" step="0.01" required><br>

        <input type="submit" value="Update Points">
    </form>
</body>
</html>