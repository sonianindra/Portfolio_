<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="path/to/css/styles.css">
    <title>User Registration</title>
    <style>
        body {
            background-color: lightyellow;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        form {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label, input, p {
            display: block;
            margin-bottom: 10px;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
            box-sizing: border-box;
        }

        input[type="submit"], .login-btn {
            width: calc(100% - 20px); /* Adjust width to account for padding */
            padding: 10px;
            background-color: gold;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: block;
            margin: 10px auto; /* Center the button and add margin on top and bottom */
            box-sizing: border-box; /* Include padding and border in the width */
}

        input[type="submit"]:hover, .login-btn:hover {
            background-color: darkkhaki;
        }

        .note {
            font-style: italic;
            color: red;
        }
    </style>
</head>
<body>
    <form action="register.php" method="post">
        <h2>Register</h2>

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <p class="note">.</p>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <input type="submit" value="Register">
        
        <!-- Login button -->
        <a href="login.php" class="login-btn">Already registered? Login here</a>
    </form>
</body>
</html>