<?php
$servername = "adesanya_banking";
$username = "client_username";
$password = "client_password";
$dbname = "user_registration_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
 