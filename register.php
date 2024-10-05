<?php
session_start();
try {
    $db = new PDO('sqlite:base.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && isset($_POST['password'])) {
        $username = trim($_POST['username']);
        $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT); // Хэшируем пароль
        $sql = "INSERT INTO users (username, password) VALUES (:username, :password)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
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
    <link rel="stylesheet" href="styles.css">
    <title>Register</title>
    <style>
        .error {
            color: red;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Register</h2>
    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    <form action="register.php" method="post">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>
</body>
</html>