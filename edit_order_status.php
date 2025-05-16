<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if an order ID is provided in the URL
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $order_id = $_GET['id'];

        // Fetch the current order status
        $stmt = $conn->prepare("SELECT id, status FROM orders WHERE id = :id");
        $stmt->bindParam(':id', $order_id, PDO::PARAM_INT);
        $stmt->execute();
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            $error_message = "Order not found.";
        }

        // Handle form submission
        if (isset($_POST['new_status'])) {
            $new_status = $_POST['new_status'];

            $update_stmt = $conn->prepare("UPDATE orders SET status = :status WHERE id = :id");
            $update_stmt->bindParam(':status', $new_status, PDO::PARAM_STR);
            $update_stmt->bindParam(':id', $order_id, PDO::PARAM_INT);
            $update_stmt->execute();

            // Redirect back to the orders page after successful update
            header("Location: orders.php?update=success");
            exit();
        }

    } else {
        $error_message = "Invalid order ID.";
    }

} catch(PDOException $e) {
    $error_message = "Database error: " . $e->getMessage();
}

// Define possible status options
$status_options = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Edit Order Status</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif; /* Consistent font */
            background-color: #FAF6F4; /* Main background color */
            margin: 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            color: #333; /* Default text color */
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 500px;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            color: #8C5356; /* Logo text color */
            text-align: center;
            margin-bottom: 25px;
            font-size: 2.5em;
        }

        .status-form {
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555; /* Label color */
            font-weight: 500;
            font-size: 1.1em;
        }

        .form-group input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
            font-size: 1em;
            transition: border-color 0.3s ease;
        }

        .form-group input[type="text"]:focus {
            border-color: #8C5356;
            outline: none;
            box-shadow: 0 0 8px rgba(140, 83, 86, 0.2);
        }

        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: 'Roboto', sans-serif;
            font-size: 1em;
        }

        .form-group button {
            background-color: #8C5356; /* Button background color */
            color: #fff;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'Roboto', sans-serif;
            font-size: 1.2em;
            transition: background-color 0.3s ease;
            display: block;
            margin-top: 20px;
            width: fit-content;
            outline: none;
        }

        .form-group button:hover {
            background-color: #734043; /* Darker accent for hover */
        }

        .error-message {
            color: red;
            margin-top: 10px;
            text-align: center;
        }

        .back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #8C5356; /* Accent color */
            text-decoration: none;
            transition: color 0.3s ease;
            font-weight: bold;
            font-size: 1.1em;
        }

        .back-link:hover {
            color: #734043; /* Darker accent for hover */
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Order Status</h1>

        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php elseif ($order): ?>
            <form method="post" class="status-form">
                <div class="form-group">
                    <label for="current_status">Current Status:</label>
                    <input type="text" id="current_status" value="<?php echo htmlspecialchars($order['status']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="new_status">Select New Status:</label>
                    <select name="new_status" id="new_status">
                        <?php foreach ($status_options as $option): ?>
                            <option value="<?php echo htmlspecialchars($option); ?>" <?php if ($order['status'] == $option) echo 'selected'; ?>><?php echo htmlspecialchars($option); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit">Update Status</button>
                </div>
            </form>
        <?php else: ?>
            <p>Order details not found.</p>
        <?php endif; ?>

        <a href="orders.php" class="back-link">Back to Orders</a>
    </div>
</body>
</html>
<?php $conn = null; ?>