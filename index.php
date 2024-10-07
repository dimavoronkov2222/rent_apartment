<?php
try {
    $db = new PDO('sqlite:base.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT * FROM rentals";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database connection error: " . $e->getMessage();
}
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
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Rental App</a>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="login.php">Login</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="register.php">Register</a>
            </li>
        </ul>
    </div>
</nav>
<div class="container">
    <h2>Rental Listings</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <div class="row">
        <?php foreach ($rentals as $rental): ?>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($rental['title']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($rental['description']); ?></p>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#rentalModal<?php echo $rental['id']; ?>">
                            View Details
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="rentalModal<?php echo $rental['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="rentalModalLabel<?php echo $rental['id']; ?>" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="rentalModalLabel<?php echo $rental['id']; ?>"><?php echo htmlspecialchars($rental['title']); ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($rental['description']); ?></p>
                            <p><strong>Price:</strong> <?php echo htmlspecialchars($rental['price']) . ' ' . htmlspecialchars($rental['currency']); ?></p>
                            <p><strong>Contact:</strong> <?php echo htmlspecialchars($rental['contact']); ?></p>
                        </div>
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