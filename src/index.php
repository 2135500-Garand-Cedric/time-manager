<?php
session_start();

// If the user is already logged in, redirect to app
if (isset($_COOKIE['tm_auth']) && $_COOKIE['tm_auth'] === getenv('APP_SECRET')) {
    header("Location: app.php");
    exit;
}

// Handle login form submission
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputCode = trim($_POST['code'] ?? '');

    // Check against APP_SECRET from .env
    if ($inputCode === getenv('APP_SECRET')) {
        // Set a cookie valid for 1 year
        setcookie('tm_auth', $inputCode, time() + (365 * 24 * 60 * 60), "/", "", false, true);

        // Redirect to app
        header("Location: app.php");
        exit;
    } else {
        $error = "Invalid code. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Manager - Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="login-body">
    <div class="login-container">
        <h1>Time Manager</h1>
        <form method="POST" class="login-form">
            <input type="password" name="code" placeholder="Enter access code" required>
            <button type="submit">Login</button>
            <?php if ($error): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
