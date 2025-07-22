<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="path/to/css/styles.css">
    <title>Leaderboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: thistle; /* Light purple background */
            padding: 20px;
            text-align: center;
        }

        h1 {
            color: rebeccapurple;
        }

        a {
            color: darkblue;
            text-decoration: none;
            background-color: lavender;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        a:hover {
            background-color: darkslateblue;
            color: white;
        }

        table {
            margin-top: 20px;
            border-collapse: collapse;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: rebeccapurple;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <h1>Brotherhood Leaderboard</h1>
    <a href="event.php">Back to Events</a>

    <?php
    require('db.php');

    // List of usernames to exclude from ranking
    $excluded_users = array('helainad', 'KaylaM','aleezak','nury','nazy','denisk','annac','allisons','ethanz','anikaa','erikc','laurenz','ethanh','wille','anthonyk','LaurenS','CJO','devinb','jaxm','marissab','luzmaryh','samuels','bellab','zainabr','mikaels','kevinj','elvinm','veerag','alexanderd','adamw','ezekielg','ashleys'); // replace with actual usernames to exclude

    // Main leaderboard query
    $query = "SELECT username, points FROM users ORDER BY points DESC";
    $result = $conn->query($query);

    // Main leaderboard table
    if ($result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>Rank</th><th>Username</th><th>Points</th></tr>";

        $rank = 1;
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";

            // Check if the user is in the excluded list
            if (in_array($row['username'], $excluded_users)) {
                echo "<td>chair/exec</td>"; // Display "chair" for excluded users
            } else {
                echo "<td>" . $rank . "</td>";
                $rank++;
            }

            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
            echo "<td>" . htmlspecialchars($row['points']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No users found.";
    }

    // Separate leaderboard for excluded users
    echo "<h2>Chair/Exec Leaderboard</h2>";

    $excluded_query = "SELECT username, points FROM users WHERE username IN ('" . implode("','", $excluded_users) . "') ORDER BY points DESC";
    $excluded_result = $conn->query($excluded_query);

    // Excluded users leaderboard table
    if ($excluded_result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>Username</th><th>Points</th></tr>";

        while ($row = $excluded_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
            echo "<td>" . htmlspecialchars($row['points']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No excluded users found.";
    }
    ?>
</body>
</html>