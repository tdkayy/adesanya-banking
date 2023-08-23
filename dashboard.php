<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: homepage2.html'); // Redirect to login if not authenticated
    exit();
}

$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo $username; ?>!</h1>
    <p><a href="logout.php">Logout</a></p>
</body>
</html>
