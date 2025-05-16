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

        // Fetch the specific order details
        $stmt = $conn->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->bindParam(':id', $order_id, PDO::PARAM_INT);
        $stmt->execute();
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($order) {
            // Optionally fetch customer details if customer_id is available
            $customer = null;
            if ($order['customer_id']) {
                $stmt_customer = $conn->prepare("SELECT first_name, last_name, email FROM customers WHERE id = :customer_id");
                $stmt_customer->bindParam(':customer_id', $order['customer_id'], PDO::PARAM_INT);
                $stmt_customer->execute();
                $customer = $stmt_customer->fetch(PDO::FETCH_ASSOC);
            }

            // You might also want to fetch the individual items in this order
            // (This assumes you have an 'order_items' table)
            // $stmt_items = $conn->prepare("SELECT oi.*, p.nom AS product_name, p.prix AS product_price FROM order_items oi JOIN produits p ON oi.product_id = p.id WHERE oi.order_id = :order_id");
            // $stmt_items->bindParam(':order_id', $order_id, PDO::PARAM_INT);
            // $stmt_items->execute();
            // $order_items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

        } else {
            $error_message = "Order not found.";
        }

    } else {
        $error_message = "Invalid order ID.";
    }

} catch(PDOException $e) {
    $error_message = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Order Details</title>
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
            max-width: 700px;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            color: #8C5356; /* Logo text color */
            text-align: center;
            margin-bottom: 25px;
            font-size: 2.5em;
        }

        .order-details {
            margin-top: 20px;
            line-height: 1.7;
            color: #333;
        }

        .order-details p {
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #eee;
        }

        .order-details p:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .order-details strong {
            font-weight: bold;
            color: #555; /* Label color */
            margin-right: 5px;
        }

        .back-link {
            display: block;
            margin-top: 30px;
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
        <h1>Order Details</h1>

        <?php if (isset($error_message)): ?>
            <p style="color: red; text-align: center;"><?php echo $error_message; ?></p>
        <?php elseif ($order): ?>
            <div class="order-details">
                <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['id']); ?></p>
                <?php if ($customer): ?>
                    <p><strong>Customer:</strong> <?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?> (ID: <?php echo htmlspecialchars($order['customer_id']); ?>)</p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['email']); ?></p>
                <?php else: ?>
                    <p><strong>Customer:</strong> Guest</p>
                <?php endif; ?>
                <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
                <p><strong>Total Amount:</strong> <?php echo htmlspecialchars($order['total_amount']); ?> DA</p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
                </div>
        <?php else: ?>
            <p style="text-align: center;">No order details found.</p>
        <?php endif; ?>

        <a href="orders.php" class="back-link">Back to Orders</a>
    </div>
</body>
</html>
<?php $conn = null; ?>