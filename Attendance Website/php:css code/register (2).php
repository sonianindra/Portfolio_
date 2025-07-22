<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require('db.php');

if (isset($_POST['username'], $_POST['password'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $initial_points = 0.00;

    // Insert the new user into the database
    $query = "INSERT INTO users (username, password, points) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssd", $username, $hashed_password, $initial_points);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $message = "Registration successful. <a href='login.php'>Login here</a>";
    } else {
        $message = "Error: " . htmlspecialchars($conn->error);
    }
} else {
    // Redirect back to registration form if accessed directly
    header("Location: registration.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="path/to/css/styles.css">
    <title>User Registration</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: lavenderblush; /* Light and playful background */
            color: rebeccapurple; /* Fun and vibrant text color */
            text-align: center;
            padding: 50px;
        }

        .container {
            background-color: white;
            width: 50%;
            margin: auto;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        h1 {
            color: mediumvioletred;
        }

        a {
            color: mediumslateblue;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        .message {
            margin-top: 20px;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Registration Status</h1>
        <div class="message">
            <?php echo $message; ?>
        </div>
    </div>
</body>
</html>
