<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Dashboard</title>
    <style>
        .container {
            width: 80%;
            margin: auto;
            text-align: center;
        }
        .logout-button {
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    <p>This is your Dashboard.</p>
    <a href="?logout=true" class="logout-button">Logout</a>
</div>
</body>
</html>
