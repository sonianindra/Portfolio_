<?php
function recalculateTotalPoints($userId, $conn) {
    // Query to sum up all points for the user
    $query = "SELECT SUM(points) AS total_points FROM attendance WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $totalPoints = $row['total_points']; // Ensure this is handling decimals

    // Update the total points in the users table
    $updateQuery = "UPDATE users SET points = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("di", $totalPoints, $userId);
    $updateStmt->execute();
}
?>