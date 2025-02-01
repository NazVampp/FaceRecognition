<!-- index.php -->
<!DOCTYPE html>
<html lang="en" class="login-page">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Face Recognition</title>
    <link href="images/PBantuan.png" rel="icon" type="image/x-icon">
    <link rel="stylesheet" href="style.css" type="text/css" charset="utf-8">
</head>
<body class="loginPg">
    <?php
        // Set $showButtons to false to hide buttons
        $showButtons = false;
        include 'navbar.php';
    ?>

    <div class="login-container">
        <h2>Auxiliary Police Login</h2>
        <form id="loginForm">
            <div class="input-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" id="loginButton">Login</button>
        </form>
    </div>

    <script src="script.js"></script>
</body>
</html>
