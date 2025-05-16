<?php
// forgot_password.php

// Include your database configuration
require 'config.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the submitted email address
    $email = $_POST['email'];

    // 1. Check if the email exists in the database
    $query = "SELECT admin_id FROM admin WHERE email = :email";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Email found, proceed with password reset

        // 2. Generate a unique reset token
        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", time() + (60 * 60)); // Token expires in 1 hour

        // 3. Store the token and expiry in the database
        $updateQuery = "UPDATE admin SET reset_token = :token, reset_expiry = :expiry WHERE email = :email";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->bindParam(':token', $token);
        $updateStmt->bindParam(':expiry', $expiry);
        $updateStmt->bindParam(':email', $email);

        if ($updateStmt->execute()) {
            // 4. Send an email to the user with the reset link
            $resetLink = "http://yourdomain.com/reset_password.php?token=" . $token;
            $subject = "Password Reset Request";
            $body = "Please click the following link to reset your password: " . $resetLink;
            $headers = "From: webmaster@yourdomain.com"; // Replace with your email

            // In a real application, you would use a more robust email sending library
            // For local development, you might need to configure your PHP environment
            // to send emails (e.g., using an SMTP server). The previous error
            // indicated a problem with this.

            // This line will still likely cause an error if your email setup isn't correct.
            // Please refer to the previous explanation on how to configure email sending
            // in your local development environment (e.g., using MailHog).
            if (mail($email, $subject, $body, $headers)) {
                $message = "A password reset link has been sent to your email address.";
            } else {
                $message = "Failed to send the password reset link. Please try again later.";
            }
        } else {
            $message = "Error updating reset token.";
        }
    } else {
        $message = "The provided email address was not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .container { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); text-align: center; }
        h2 { color: #333; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; color: #555; }
        input[type="email"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #8C5356; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; }
        .message { margin-top: 20px; color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Forgot Your Password?</h2>
        <?php if (!empty($message)): ?>
            <p class="<?php echo (strpos($message, 'Error') !== false) ? 'error' : 'message'; ?>"><?php echo $message; ?></p>
        <?php else: ?>
            <p>Enter your email address below and we'll send you a link to reset your password.</p>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email Address:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <button type="submit">Send Reset Link</button>
            </form>
        <?php endif; ?>
        <p style="margin-top: 20px;"><a href="login.php">Back to Login</a></p>
    </div>
</body>
</html>