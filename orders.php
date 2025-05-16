<?php
// Database connection (as in index.php)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all orders (assuming you have an 'orders' table)
    $stmt = $conn->prepare("SELECT * FROM orders ORDER BY order_date DESC");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    echo "Erreur de connexion à la base de données: " . $e->getMessage();
    $orders = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Orders</title>
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
            max-width: 1200px; /* Increased max-width to accommodate more columns */
        }

        h1 {
            font-family: 'Playfair Display', serif;
            color: #8C5356; /* Logo text color */
            text-align: center;
            margin-bottom: 25px;
            font-size: 2.5em;
        }

        table {
            width: 100%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden; /* for rounded corners of thead/tbody */
        }

        thead {
            background-color: #f5f5f0;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            color: #555; /* Label color */
            font-weight: 500;
            font-size: 1.1em;
        }

        td {
            color: #333;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .actions a {
            margin-right: 10px; /* Reduced margin */
            text-decoration: none;
            color: #8C5356; /* Accent color */
            font-weight: bold;
            transition: color 0.3s ease;
            font-size: 0.95em; /* Slightly smaller font for actions */
        }

        .actions a:hover {
            color: #734043; /* Darker accent for hover */
            text-decoration: underline;
        }

        .actions a:last-child {
            margin-right: 0;
        }

        .back-to-index {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #8C5356; /* Accent color */
            text-decoration: none;
            transition: color 0.3s ease;
            font-weight: bold;
            font-size: 1.1em;
        }

        .back-to-index:hover {
            color: #734043; /* Darker accent for hover */
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Orders</h1>
        <?php if (!empty($orders)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer ID</th>
                        <th>Order Date</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo $order['id']; ?></td>
                            <td><?php echo htmlspecialchars($order['customer_id'] ?? 'Guest'); ?></td>
                            <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                            <td><?php echo htmlspecialchars($order['total_amount']); ?> DA</td>
                            <td><?php echo htmlspecialchars($order['status']); ?></td>
                            <td class="actions">
                                <a href="view_order.php?id=<?php echo $order['id']; ?>">View</a>
                                <a href="edit_order_status.php?id=<?php echo $order['id']; ?>">Edit Status</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No orders found.</p>
        <?php endif; ?>
        <a href="index.php" class="back-to-index">Back to Home</a>
    </div>
</body>
</html>
<?php $conn = null; ?>