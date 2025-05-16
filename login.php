<?php
// --- Configuration ---
session_start();
require 'config.php';

// --- Error Handling ---
$error_message = "";

// --- Form Submission Handling ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve user input
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Database Query
    $query = "SELECT admin_id, name FROM admin WHERE email = :email AND password = :password";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);

    try {
        $stmt->execute();

        // Check if a matching user was found
        if ($stmt->rowCount() > 0) {
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            // Set session variables
            $_SESSION["id"] = $data['admin_id'];
            $_SESSION["nom"] = $data['name'];

            // Redirect to the dashboard
            header("Location: index.php");
            exit();
        } else {
            // Authentication failed
            $error_message = "Your email or password is incorrect";
        }
    } catch (PDOException $e) {
        // Handle database errors
        $error_message = "Database error: " . $e->getMessage();
        // Consider logging this error for debugging
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elegance - Login</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        /* Base Styles */
        body {
            font-family: 'Playfair Display', serif;
            background-color: #FAF6F4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
            color: #333;
        }

        .login-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
            text-align: center;
            width: 90%;
            max-width: 450px;
            opacity: 0; /* Initially hidden */
            animation: fadeIn 0.5s ease-out forwards 0.2s; /* Fade in animation */
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Logo */
        .logo {
            font-family: 'Great Vibes', cursive;
            font-size: 3em;
            font-weight: bold;
            color: #8C5356;
            margin-bottom: 20px;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 15px;
            text-align: left;
            position: relative; /* For positioning the eye icon */
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
            font-size: 0.9em;
        }

        .password-input-wrapper {
            position: relative; /* To contain the input and the eye */
        }

        input[type="text"],
        input[type="password"] {
            width: 100%; /* Take full width of the wrapper */
            padding: 10px 30px 10px 10px; /* Add padding to the right for the eye */
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
            font-size: 0.9em;
            transition: border-color 0.3s ease, box-shadow 0.3s ease; /* Transition for focus animation */
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #8C5356;
            box-shadow: 0 0 8px rgba(140, 83, 86, 0.3); /* Subtle shadow on focus */
        }

        /* Error Messages */
        .error-message {
            color: #dc3545;
            margin-top: 8px;
            font-size: 0.8em;
            display: block; /* Ensure error messages take up full width */
        }

        /* Buttons */
        .button-group {
            margin-top: 25px; /* Increased margin to create space */
            text-align: center; /* Center the buttons */
        }

        button {
            background-color: #8C5356;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease; /* Transition for hover animation */
            font-size: 0.9em;
            font-family: 'Roboto', sans-serif;
            margin: 0 5px;
            outline: none;
        }

        button:hover {
            background-color: #734043;
            transform: scale(1.05); /* Slight scale-up on hover */
        }

        button[type="reset"] {
            background-color: #a9a9a9;
        }

        button[type="reset"]:hover {
            background-color: #808080;
            transform: scale(1.05); /* Slight scale-up on hover */
        }

        /* Password Toggle Icon */
        .password-toggle {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 1.1em;
            color: #555;
            transition: color 0.3s ease; /* Subtle color change on hover */
        }

        .password-toggle:hover {
            color: #8C5356;
        }

        /* Forgot Password Link */
        .forgot-password {
            display: block;
            margin-top: 20px; /* Adjust top margin for spacing */
            font-size: 0.95em;
            color: #808080; /* Set the color to grey */
            text-decoration: none;
            transition: color 0.3s ease; /* Subtle color change on hover */
            text-align: center; /* Center the link */
        }

        .forgot-password:hover {
            text-decoration: underline;
            color: #555; /* Darker grey on hover */
        }
    </style>

    <script>
        function Validation_login() {
            var email = document.getElementById('email');
            var passwordInput = document.getElementById('password');
            var msg_email = document.getElementById('msg_email');
            var msg_password = document.getElementById('msg_password');
            var E = email.value.trim();
            var P = passwordInput.value.trim();
            var isValid = true;

            // Reset previous error messages
            msg_email.textContent = "";
            msg_password.textContent = "";

            if (E === "") {
                msg_email.textContent = "Please enter your email!";
                isValid = false;
            }

            if (P === "") {
                msg_password.textContent = "Please enter your password!";
                isValid = false;
            }

            return isValid;
        }

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            // You could also toggle a class on the eye icon to change its appearance
        }
    </script>
</head>
<body>
    <div class="login-container">
        <h1 class="logo">Elegance</h1>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" onsubmit="return Validation_login();">
            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="text" id="email" name="email">
                <span id="msg_email" class="error-message"></span>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <div class="password-input-wrapper">
                    <input type="password" id="password" name="password">
                    <i class="fas fa-eye password-toggle" onclick="togglePasswordVisibility()"></i>
                </div>
                <span id="msg_password" class="error-message"></span>
            </div>
            <a href="forgot_password.php" class="forgot-password">Forgot Password?</a>
            <?php if (!empty($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <div class="button-group">
                <button type="submit">Log In</button>
                <button type="reset">Clear</button>
            </div>
        </form>
    </div>
</body>
</html>