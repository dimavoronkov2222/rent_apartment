<?php
session_start();

$valid_username = "user";
$valid_password = "password";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Проверка логина и пароля
    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION['username'] = $username;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google-signin-client_id" content="YOUR_CLIENT_ID.apps.googleusercontent.com"> <!-- Замените на ваш Client ID -->
    <link rel="stylesheet" href="styles.css">
    <title>Login</title>
    <script src="https://apis.google.com/js/platform.js" async defer></script>
    <style>
        .error {
            color: red;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Login</h2>
    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="login.php" method="post">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit">Login</button>
    </form>

    <div>
        <button class="google-login-button" id="googleLoginBtn">
            <img src="https://upload.wikimedia.org/wikipedia/commons/5/53/Google_%22G%22_Logo.svg" alt="Google" class="google-icon" width="20" height="20">
            Sign in with Google
        </button>
    </div>

    <p>Don't have an account? <a href="register.php">Register</a></p>
</div>

<script>
    document.getElementById('googleLoginBtn').onclick = function() {
        gapi.load('auth2', function() {
            gapi.auth2.init({
                client_id: 'YOUR_CLIENT_ID.apps.googleusercontent.com' // Замените на ваш Client ID
            }).then(function(auth2) {
                auth2.signIn().then(function(googleUser) {
                    var id_token = googleUser.getAuthResponse().id_token;

                    // Отправка id_token на сервер
                    fetch('callback.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id_token: id_token })
                    })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Success:', data);
                            // Здесь вы можете перенаправить пользователя или обновить интерфейс
                            if (data.status === 'success') {
                                window.location.href = 'dashboard.php'; // Перенаправление на панель
                            }
                        })
                        .catch((error) => {
                            console.error('Error:', error);
                        });
                });
            });
        });
    };
</script>
</body>
</html>