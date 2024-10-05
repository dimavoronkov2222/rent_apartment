<?php
session_start();
try {
    $db = new PDO('sqlite:base.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $error = "Database connection error: " . $e->getMessage();
}
$sql = "SELECT * FROM rentals";
$stmt = $db->prepare($sql);
$stmt->execute();
$rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>Rental Listings</title>
    <style>
        .container {
            margin-top: 20px;
        }
        .card {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Rental Catalog</a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav ml-auto">
            <?php if (isset($_SESSION['username'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="?logout=true">Logout</a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="register.php">Register</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
<div class="container">
    <h2>Available Rental Properties</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <div class="row">
        <?php foreach ($rentals as $rental): ?>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($rental['title']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($rental['description']); ?></p>
                        <p class="card-text">Price: <?php echo htmlspecialchars($rental['price']) . ' ' . htmlspecialchars($rental['currency']); ?></p>
                        <p class="card-text">Contact: <?php echo htmlspecialchars($rental['contact']); ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>