<?php
$servername = "localhost:3306";
$username = "ywoeddmy_x";
$password = "Ginger2010!";
$dbname = "ywoeddmy_attendance"; // replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>