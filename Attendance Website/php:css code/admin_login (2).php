<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="path/to/css/styles.css">
    <title>Admin Login</title>
    <!-- Add CSS if needed -->
</head>
<body>
    <h2>Admin Login</h2>
    <form action="check_admin_login.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <input type="submit" value="Login">
    </form>
</body>
</html>