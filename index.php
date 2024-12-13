<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>
<header>
    <h1>Welcome to B£Tn£R</h1>
    <nav>
        <?php if (isset($_SESSION["user"])): ?>
            <a href="bet.php">Bet Page</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>
<main>
    <p><?php echo isset($_SESSION["user"]) ? "Hello, " . htmlspecialchars($_SESSION["user"]) . "!" : "Welcome to the best betting platform!"; ?></p>
</main>
</body>
</html>