<?php
session_start();
require_once "database.php";

if (isset($_POST["submit"])) {
    $fullName = trim($_POST["fullname"]);
    $phonenumber = trim($_POST["phonenumber"]);
    $password = trim($_POST["password"]);
    $passwordRepeat = trim($_POST["repeat_password"]);
    $otp = mt_rand(100000, 999999);

    $errors = [];

    // Validate fields
    if (empty($fullName) || empty($phonenumber) || empty($password) || empty($passwordRepeat)) {
        $errors[] = "All fields are required.";
    }
    if (!preg_match('/^\d{10,15}$/', $phonenumber)) {
        $errors[] = "Invalid phone number format.";
    }
    if (strlen($password) < 4) {
        $errors[] = "Password must be at least 4 characters long.";
    }
    if ($password !== $passwordRepeat) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        // Check if phone number exists
        $sql = "SELECT * FROM users WHERE phonenumber = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $phonenumber);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $errors[] = "Phone number already exists.";
        } else {
            // Hash the password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $sql = "INSERT INTO users (full_name, phonenumber, password, otp) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssi", $fullName, $phonenumber, $passwordHash, $otp);

            if (mysqli_stmt_execute($stmt)) {
                echo "<div class='alert alert-success'>Account created successfully! OTP sent to your phone: $otp</div>";
            } else {
                $errors[] = "Error: " . mysqli_error($conn);
            }
        }
    }

    // Display errors
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
    <title>Register</title>
</head>
<body>
<form action="register.php" method="post">
    <input type="text" name="fullname" placeholder="Full Name">
    <input type="text" name="phonenumber" placeholder="Phone Number">
    <input type="password" name="password" placeholder="Password">
    <input type="password" name="repeat_password" placeholder="Repeat Password">
    <button type="submit" name="submit">Register</button>
</form>
</body>
</html>