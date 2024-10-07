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
    $result = $db->query("PRAGMA table_info(rentals)")->fetchAll(PDO::FETCH_ASSOC);
    $columnExists = false;
    foreach ($result as $column) {
        if ($column['name'] === 'image') {
            $columnExists = true;
            break;
        }
    }
} catch (PDOException $e) {
    die("Database connection error: " . $e->getMessage());
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title'], $_POST['description'], $_POST['price'], $_POST['currency'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $currency = trim($_POST['currency']);
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }
    $sql = "INSERT INTO rentals (title, description, price, currency, image) VALUES (:title, :description, :price, :currency, :image)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':currency', $currency);
    $stmt->bindParam(':image', $image);
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
$sql = "SELECT * FROM users WHERE username = :username";
$stmt = $db->prepare($sql);
$stmt->bindParam(':username', $_SESSION['username']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
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
                <?php if ($rental['image']): ?>
                    <img src="<?php echo htmlspecialchars($rental['image']); ?>" alt="Rental Image" style="width: 100px; height: auto;">
                <?php endif; ?>
                <a href="?delete=<?php echo $rental['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
            </li>
        <?php endforeach; ?>
    </ul>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addRentalModal">
        Add Rental Property
    </button>
    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#editProfileModal">
        Edit Profile
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
                <form action="dashboard.php" method="post" enctype="multipart/form-data">
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
                            <label for="image">Image:</label>
                            <input type="file" id="image" name="image" class="form-control" accept="image/*">
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
    <div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="profile.php" method="post">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="fullname">Full Name:</label>
                            <input type="text" id="fullname" name="fullname" class="form-control" value="<?php echo htmlspecialchars($user['fullname'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <a href="?logout=true" class="btn btn-danger">Logout</a>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>