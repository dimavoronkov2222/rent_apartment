<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
try {
    $db = new PDO('sqlite:base.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $username = $_SESSION['username'];
    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['update_profile'])) {
            $new_username = trim($_POST['username']);
            $full_name = trim($_POST['full_name']);
            $contact = trim($_POST['contact']);
            $sql = "UPDATE users SET username = :username, full_name = :full_name, contact = :contact WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':username', $new_username);
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':contact', $contact);
            $stmt->bindParam(':id', $user['id']);
            $stmt->execute();
            $_SESSION['username'] = $new_username;
            $success = "Profile updated successfully!";
        }
        if (isset($_POST['change_password'])) {
            $current_password = trim($_POST['current_password']);
            $new_password = trim($_POST['new_password']);
            if (password_verify($current_password, $user['password'])) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET password = :password WHERE id = :id";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':password', $hashed_password);
                $stmt->bindParam(':id', $user['id']);
                $stmt->execute();

                $success = "Password changed successfully!";
            } else {
                $error = "Current password is incorrect.";
            }
        }
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['profile_picture']['tmp_name'];
            $file_name = basename($_FILES['profile_picture']['name']);
            $file_path = "uploads/" . $file_name;
            if (move_uploaded_file($file_tmp, $file_path)) {
                $sql = "UPDATE users SET profile_picture = :profile_picture WHERE id = :id";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':profile_picture', $file_path);
                $stmt->bindParam(':id', $user['id']);
                $stmt->execute();
                $success = "Profile picture updated successfully!";
            } else {
                $error = "Failed to upload profile picture.";
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
    <title>Profile</title>
</head>
<body>
<div class="container">
    <h2>Profile</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <h4>Update Profile</h4>
    <form action="profile.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>
        <div class="form-group">
            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="contact">Contact Information:</label>
            <input type="text" id="contact" name="contact" class="form-control" value="<?php echo htmlspecialchars($user['contact'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="profile_picture">Profile Picture:</label>
            <input type="file" id="profile_picture" name="profile_picture" class="form-control">
        </div>
        <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
    </form>
    <h4>Change Password</h4>
    <form action="profile.php" method="post">
        <div class="form-group">
            <label for="current_password">Current Password:</label>
            <input type="password" id="current_password" name="current_password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" class="form-control" required>
        </div>
        <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
    </form>
    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>