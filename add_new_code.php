<?php
// add_new_code.php

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet";

$message = ""; // To store success/error messages

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['new_promotion_code'], $_POST['start_date'], $_POST['end_date'], $_POST['discount_percentage']) &&
            !empty($_POST['new_promotion_code']) && !empty($_POST['start_date']) && !empty($_POST['end_date']) && isset($_POST['discount_percentage'])) {

            $new_code = htmlspecialchars($_POST['new_promotion_code']);
            $start_date = htmlspecialchars($_POST['start_date']);
            $end_date = htmlspecialchars($_POST['end_date']);
            $discount_percentage = intval($_POST['discount_percentage']);

            // Basic validation for discount percentage
            if ($discount_percentage < 0 || $discount_percentage > 100) {
                $message = "<span style='color:red;'>Discount percentage must be between 0 and 100.</span>";
            } else {
                $stmt = $conn->prepare("INSERT INTO promotion (code, start_date, end_date, discount_percentage, is_active) VALUES (:code, :start_date, :end_date, :discount_percentage, 1)");
                $stmt->bindParam(':code', $new_code);
                $stmt->bindParam(':start_date', $start_date);
                $stmt->bindParam(':end_date', $end_date);
                $stmt->bindParam(':discount_percentage', $discount_percentage);

                if ($stmt->execute()) {
                    session_start();
                    $_SESSION['apply_promotion_message'] = "Promotion code '$new_code' added successfully!";
                    header("Location: apply_promotion.php");
                    exit();
                } else {
                    $message = "<span style='color:red;'>Error adding promotion code.</span>";
                }
            }
        } else {
            $message = "<span style='color:red;'>Please fill in all the required fields.</span>";
        }
    }
} catch (PDOException $e) {
    $message = "<span style='color:red;'>Database error: " . $e->getMessage() . "</span>";
}

session_start();
if (isset($_SESSION['add_promotion_message'])) {
    $message = $_SESSION['add_promotion_message'];
    unset($_SESSION['add_promotion_message']);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Promotion Code</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@400;500&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #FAF6F4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            text-align: center;
        }

        .container {
            background-color: #FFFFFF;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 500px;
            box-sizing: border-box;
        }

        h2 {
            color: #8C5356;
            margin-bottom: 30px;
            text-align: center;
            font-size: 2.2em;
            font-family: 'Playfair Display', serif;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 25px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
            font-size: 1.1em;
        }

        input[type="text"],
        input[type="date"],
        input[type="number"] {
            width: calc(100% - 20px);
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin: 0;
            font-size: 1.1em;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        input[type="number"]:focus {
            outline: none;
            border-color: #8C5356;
            box-shadow: 0 0 5px rgba(140, 83, 86, 0.5);
        }

        button[type="submit"] {
            background-color: #8C5356;
            color: white;
            padding: 15px 25px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-size: 1.2em;
            transition: background-color 0.3s ease;
            margin-top: 10px;
            width: 100%;
            font-family: 'Roboto', sans-serif;
            outline: none;
        }

        button[type="submit"]:hover {
            background-color: #734043;
        }

        .message {
            margin-top: 20px;
            padding: 12px;
            border-radius: 6px;
            font-size: 1.1em;
            font-weight: 500;
        }

        .message.success {
            background-color: #E6F4EA;
            color: #388E3C;
            border: 1px solid #C8E6C9;
        }

        .message.error {
            background-color: #FBE9E7;
            color: #D32F2F;
            border: 1px solid #FFCDD2;
        }

        .back-link {
            display: block;
            margin-top: 30px;
            color: #3498db;
            text-decoration: none;
            font-size: 1.1em;
            transition: color 0.3s ease;
            text-align: center;
            font-family: 'Roboto', sans-serif;
        }

        .back-link:hover {
            color: #217dbb;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
                width: 95%;
            }

            input[type="text"],
            input[type="date"] {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Add New Promotion Code</h2>

        <?php if (!empty($message)): ?>
            <p class="message <?php echo (strpos($message, 'success') !== false) ? 'success' : 'error'; ?>">
                <?php echo $message; ?></p>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="new_promotion_code">Promotion Code:</label>
                <input type="text" id="new_promotion_code" name="new_promotion_code" required>
            </div>
            <div class="form-group">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" required>
            </div>
            <div class="form-group">
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" required>
            </div>
            <div class="form-group">
                <label for="discount_percentage">Discount Percentage:</label>
                <input type="number" id="discount_percentage" name="discount_percentage" required min="0" max="100">
            </div>
            <button type="submit">Add New Code</button>
        </form>

        <a href="apply_promotion.php" class="back-link">Back to apply promotion</a>
        <a href="index.php" class="back-link">Back to Dashboard</a>
    </div>
</body>

</html>