<?php
session_start();
require_once "database.php";

if (isset($_POST["login"])) {
    $phonenumber = trim($_POST["phonenumber"]);
    $password = trim($_POST["password"]);

    $errors = [];

    if (empty($phonenumber) || empty($password)) {
        $errors[] = "Phone number and password are required.";
    } else {
        $sql = "SELECT * FROM users WHERE phonenumber = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $phonenumber);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user["password"])) {
            $_SESSION["user"] = $user["full_name"];
            header("Location: bet.php");
            exit();
        } else {
            $errors[] = "Invalid phone number or password.";
        }
    }

    foreach ($errors as $error) {
        echo "<div class='alert alert-danger'>$error</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
<form action="login.php" method="post">
    <input type="text" name="phonenumber" placeholder="Phone Number">
    <input type="password" name="password" placeholder="Password">
    <button type="submit" name="login">Login</button>
</form>
</body>
</html>