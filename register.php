<?php
session_start();
try {
    $db = new PDO('sqlite:base.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'], $_POST['password'], $_POST['full_name'])) {
        $username = trim($_POST['username']);
        $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
        $full_name = trim($_POST['full_name']);
        $photo = null;
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == UPLOAD_ERR_OK) {
            $photo = 'uploads/' . basename($_FILES['profile_photo']['name']);
            move_uploaded_file($_FILES['profile_photo']['tmp_name'], $photo);
        }
        $sql = "INSERT INTO users (username, password, full_name, profile_photo) VALUES (:username, :password, :full_name, :profile_photo)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':profile_photo', $photo);
        try {
            $stmt->execute();
            $_SESSION['username'] = $username;
            header("Location: dashboard.php");
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Username already exists.";
            } else {
                $error = "Registration error: " . $e->getMessage();
            }
        }
    }
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
    <title>Register</title>
</head>
<body>
<div class="container">
    <h2>Register</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form action="register.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="profile_photo">Profile Photo:</label>
            <input type="file" id="profile_photo" name="profile_photo" class="form-control" accept="image/*" required>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>