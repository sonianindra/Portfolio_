<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if (isset($_POST['username']) && isset($_POST['password'])) {
    // Include the database connection here, so it's only loaded when needed
    require('db.php');
    
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM `users` WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_logged_in'] = true;
            header("Location: event.php");
            exit();
        } else {
            $error = "Username/password is incorrect.";
        }
    } else {
        $error = "Username/password is incorrect.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            background-color: lavender; /* Light purple background */
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%; /* Full width on small screens */
            max-width: 400px; /* Maximum width on larger screens */
        }

        h1.login-title {
            color: rebeccapurple;
            margin-bottom: 20px;
        }

        input.login-input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
            box-sizing: border-box;
        }

        input.login-button {
            width: 100%;
            padding: 10px;
            background-color: rebeccapurple;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input.login-button:hover {
            background-color: darkslateblue;
        }

        .link a {
            color: rebeccapurple;
        }

        .link a:hover {
            text-decoration: underline;
        }
        .admin-button {
            position: fixed;
            bottom: 10px;
            right: 10px;
            background-color: darkgray;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
        }

        .admin-button:hover {
            background-color: gray;
        }
        @media (max-width: 600px) {
    .form {
        margin: 10px;
        width: calc(100% - 20px); /* Slightly smaller than the screen width */
        }
     }
    </style>
</head>
<body>
    <?php if (isset($error)): ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>

    <form class="form" method="post" name="login">
        <h1 class="login-title">Login</h1>
        <input type="text" class="login-input" name="username" placeholder="Username" required autofocus />
        <input type="password" class="login-input" name="password" placeholder="Password" required />
        <input type="submit" value="Login" name="submit" class="login-button" />
        <p class="link"><a href="registration.php">New Registration</a></p>
        <a href="admin_login.php" class="admin-button">Admin Login</a>
    </form>
</body>
</html>