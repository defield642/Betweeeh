<?php
session_start();
require_once "database.php"; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["send_otp"])) {
        $phonenumber = $_POST["phonenumber"];
        $errors = [];

        // Validate phone number
        if (empty($phonenumber) || !preg_match('/^\d{10,15}$/', $phonenumber)) {
            $errors[] = "Please enter a valid phone number.";
        } else {
            // Check if the phone number exists in the database
            $sql = "SELECT * FROM users WHERE phonenumber = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $phonenumber);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) === 0) {
                $errors[] = "Phone number not found in our records.";
            } else {
                // Generate OTP
                $otp = random_int(100000, 999999);

                // Store OTP in the database (assuming 'otp' field exists in the users table)
                $updateSql = "UPDATE users SET otp = ? WHERE phonenumber = ?";
                $updateStmt = mysqli_prepare($conn, $updateSql);
                mysqli_stmt_bind_param($updateStmt, "is", $otp, $phonenumber);
                mysqli_stmt_execute($updateStmt);

                // Simulate sending OTP (you need to manually send the OTP via SMS or email)
                $_SESSION["otp"] = $otp;
                $_SESSION["phonenumber"] = $phonenumber;

                echo "<div class='alert alert-success'>OTP sent to $phonenumber. Please check your messages.</div>";
            }
        }

        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    }

    if (isset($_POST["verify_otp"])) {
        $otp = $_POST["otp"];
        $phonenumber = $_SESSION["phonenumber"];
        $errors = [];

        // Verify the OTP entered by the user
        if ($otp != $_SESSION["otp"]) {
            $errors[] = "Invalid OTP. Please try again.";
        } else {
            $_SESSION["otp_verified"] = true;
            echo "<div class='alert alert-success'>OTP verified successfully. You can now reset your password.</div>";
        }

        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    }

    if (isset($_POST["reset_password"])) {
        if (!isset($_SESSION["otp_verified"]) || $_SESSION["otp_verified"] !== true) {
            echo "<div class='alert alert-danger'>Unauthorized access. Please verify OTP first.</div>";
            exit;
        }

        $newPassword = $_POST["new_password"];
        $repeatPassword = $_POST["repeat_password"];
        $errors = [];

        // Validate passwords
        if (empty($newPassword) || strlen($newPassword) < 4) {
            $errors[] = "Password must be at least 4 characters long.";
        }
        if ($newPassword !== $repeatPassword) {
            $errors[] = "Passwords do not match.";
        }

        if (empty($errors)) {
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $phonenumber = $_SESSION["phonenumber"];

            // Update the password in the database
            $sql = "UPDATE users SET password = ?, otp = NULL WHERE phonenumber = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ss", $passwordHash, $phonenumber);
            mysqli_stmt_execute($stmt);

            echo "<div class='alert alert-success'>Password reset successfully. You can now log in.</div>";
            session_destroy();
        } else {
            foreach ($errors as $error) {
                echo "<div class='alert alert-danger'>$error</div>";
            }
        }
    }
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
</head>
<body>
    <h2>Forgot Password</h2>
    <?php if (!isset($_SESSION["otp_verified"])): ?>
        <form method="post" action="">
            <input type="text" name="phonenumber" placeholder="Enter your phone number" required>
            <button type="submit" name="send_otp">Send OTP</button>
        </form>

        <form method="post" action="">
            <input type="text" name="otp" placeholder="Enter OTP" required>
            <button type="submit" name="verify_otp">Verify OTP</button>
        </form>
    <?php else: ?>
        <form method="post" action="">
            <input type="password" name="new_password" placeholder="Enter new password" required>
            <input type="password" name="repeat_password" placeholder="Repeat new password" required>
            <button type="submit" name="reset_password">Reset Password</button>
        </form>
    <?php endif; ?>
</body>
</html>