<?php
session_start();

// Database Connection
require 'config.php';

$message = [];

if (isset($_POST['add_admin'])) {
    // Sanitize and validate
    $admin_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $admin_email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $admin_password = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);

    if (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
        $message[] = 'Invalid email address!';
    }
    // IMPORTANT CHANGE:  Only allow 'Admin' role here for sub-admins
    $allowed_roles = ['Admin'];
    if (!in_array($role, $allowed_roles)) {
        $message[] = 'Invalid role selected! Only Admin can be added here.';
    }

    if (empty($admin_name) || empty($admin_email) || empty($admin_password) || empty($cpassword) || empty($role)) {
        $message[] = 'All fields are required!';
    } else {
        try {
            if ($pdo) {
                $select = "SELECT * FROM admin WHERE name = :name OR email = :email";
                $stmt = $pdo->prepare($select);
                $stmt->bindParam(':name', $admin_name);
                $stmt->bindParam(':email', $admin_email);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row) {
                    $message[] = 'Admin name or email already exists!';
                } else {
                    if ($admin_password != $cpassword) {
                        $message[] = 'Confirm password not matched!';
                    } else {
                        // Store plain text password (INSECURE - ONLY FOR TESTING)
                        $insert = "INSERT INTO admin (name, email, role, password) VALUES(:name, :email, :role, :password)";
                        $stmt = $pdo->prepare($insert);
                        $stmt->bindParam(':name', $admin_name);
                        $stmt->bindParam(':email', $admin_email);
                        $stmt->bindParam(':role', $role);
                        $stmt->bindParam(':password', $admin_password); // Store plain text
                        $stmt->execute();
                        $message[] = 'New admin added successfully!';
                    }
                }
            } else {
                $message[] = 'Error: Database connection not established!';
            }
        } catch (PDOException $e) {
            $message[] = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Admin - Elegance Admin Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #FAF6F4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            color: #333;
            align-items: center; /* Center the content horizontally */
            justify-content: flex-start; /* Align content from the top */
            padding-top: 60px; /* Account for fixed header */
        }

        .header {
            background-color: #fff;
            padding: 15px 20px;
            text-align: left;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
            display: flex;
            justify-content: flex-start; /* Align items to the start to push back button left */
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 100;
        }

        .back-button {
            font-size: 1em;
            color: #555;
            text-decoration: none;
            transition: color 0.3s ease;
            display: flex;
            align-items: center;
        }

        .back-button i {
            margin-right: 5px;
        }

        .back-button:hover {
            color: #8C5356;
        }

        .admin-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
            width: 90%;
            max-width: 600px;
            margin-top: 20px; /* Add some space below the header */
            margin-bottom: 30px;
        }

        .admin-title {
            font-family: 'Playfair Display', serif;
            font-size: 2em;
            color: #8C5356; /* Changed color to match accent */
            text-align: center;
            margin-bottom: 20px;
        }

        .message-container {
            margin-bottom: 15px;
        }

        .message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #c3e6cb;
            margin-bottom: 8px;
            text-align: center;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #f5c6cb;
            margin-bottom: 8px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
            font-size: 0.9em;
        }

        .form-control {
            width: calc(100% - 22px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 0.9em;
        }

        .select-control {
            width: calc(100% - 22px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 0.9em;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="currentColor" height="24" viewBox="0 0 24 24" width="24"><path d="M7 10l5 5 5-5z"/></svg>');
            background-repeat: no-repeat;
            background-position-x: 95%;
            background-position-y: center;
        }

        .select-control::-ms-expand {
            display: none;
        }

        .submit-button {
            background-color: #8C5356;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        .submit-button:hover {
            background-color: #734043;
        }

        .footer {
            background-color: #fff;
            text-align: center;
            padding: 12px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.05);
            font-size: 0.75em;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="index.php" class="back-button"><i class="fas fa-arrow-left"></i> Back to Admin Panel</a>
        <div></div> </div>

    <div class="admin-container">
        <h2 class="admin-title">Add New Administrator</h2> <?php
        if (isset($message)) {
            foreach ($message as $msg) {
                echo '<div class="' . (strpos($msg, 'error') !== false ? 'error' : 'message') . '">' . $msg . '</div>';
            }
        }
        ?>
        <form action="" method="post">
            <div class="form-group">
                <label for="name">Admin Name:</label>
                <input type="text" name="name" class="form-control" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="email">Admin Email:</label>
                <input type="email" name="email" class="form-control" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="cpassword">Confirm Password:</label>
                <input type="password" name="cpassword" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="role">Role:</label>
                <select name="role" class="select-control" required>
                    <option value="" disabled selected>Select Role</option>
                    <option value="Admin" <?php echo (isset($_POST['role']) && $_POST['role'] == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            <input type="submit" value="Add Admin" name="add_admin" class="submit-button">
        </form>
    </div>

    <div class="footer">
        &copy; 2025 Elegance. All rights reserved.
    </div>
</body>
</html>