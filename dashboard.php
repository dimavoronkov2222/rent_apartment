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
try {
    $db = new PDO('sqlite:base.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $error = "Database connection error: " . $e->getMessage();
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title'], $_POST['description'], $_POST['price'], $_POST['currency'], $_POST['contact'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $currency = trim($_POST['currency']);
    $contact = trim($_POST['contact']);
    $sql = "INSERT INTO rentals (title, description, price, currency, contact) VALUES (:title, :description, :price, :currency, :contact)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':currency', $currency);
    $stmt->bindParam(':contact', $contact);
    try {
        $stmt->execute();
        $success = "Rental property added successfully!";
    } catch (PDOException $e) {
        $error = "Error adding rental property: " . $e->getMessage();
    }
}
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM rentals WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id);
    try {
        $stmt->execute();
        $success = "Rental property deleted successfully!";
    } catch (PDOException $e) {
        $error = "Error deleting rental property: " . $e->getMessage();
    }
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
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    <p>This is your Dashboard.</p>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <h3>Rental Properties</h3>
    <ul class="list-group mb-4">
        <?php foreach ($rentals as $rental): ?>
            <li class="list-group-item">
                <strong><?php echo htmlspecialchars($rental['title']); ?></strong>
                <p><?php echo htmlspecialchars($rental['description']); ?></p>
                <p>Price: <?php echo htmlspecialchars($rental['price']) . ' ' . htmlspecialchars($rental['currency']); ?></p>
                <p>Contact: <?php echo htmlspecialchars($rental['contact']); ?></p>
                <a href="?delete=<?php echo $rental['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
            </li>
        <?php endforeach; ?>
    </ul>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addRentalModal">
        Add Rental Property
    </button>
    <div class="modal fade" id="addRentalModal" tabindex="-1" role="dialog" aria-labelledby="addRentalModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRentalModalLabel">Add Rental Property</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="dashboard.php" method="post">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="title">Title:</label>
                            <input type="text" id="title" name="title" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description:</label>
                            <textarea id="description" name="description" class="form-control" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="price">Price:</label>
                            <input type="number" id="price" name="price" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="currency">Currency:</label>
                            <select id="currency" name="currency" class="form-control" required>
                                <option value="USD">USD</option>
                                <option value="EUR">EUR</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="contact">Contact Information:</label>
                            <input type="text" id="contact" name="contact" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Property</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <a href="?logout=true" class="logout-button">Logout</a>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>