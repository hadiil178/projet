<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if customer ID is provided in the URL
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $customer_id = $_GET['id'];

        // Fetch the customer details from the database
        $stmt = $conn->prepare("SELECT * FROM customers WHERE id = :id");
        $stmt->bindParam(':id', $customer_id, PDO::PARAM_INT);
        $stmt->execute();
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$customer) {
            // Customer not found
            $error_message = "Customer not found.";
        }
    } else {
        // Invalid customer ID
        $error_message = "Invalid customer ID.";
    }
} catch (PDOException $e) {
    $error_message = "Database error: " . $e->getMessage();
}

// Function to safely display HTML.
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Customer Profile</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #333;
        }

        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            text-align: center;
        }

        h1 {
            color: #8C5356;
            margin-bottom: 20px;
            font-size: 2em;
        }

        p {
            margin-bottom: 15px;
            font-size: 1.1em;
            line-height: 1.6;
        }

        .profile-info {
            text-align: left;
            margin-bottom: 20px;
        }

        .profile-info strong {
            color: #8C5356;
        }

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #8C5356;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            transition: background-color 0.3s ease;
            font-size: 1.1em;
        }

        .back-button:hover {
            background-color: #734043;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Customer Profile</h1>
        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php elseif (isset($customer)): ?>
            <div class="profile-info">
                <p><strong>ID:</strong> <?php echo h($customer['id']); ?></p>
                <p><strong>First Name:</strong> <?php echo h($customer['first_name']); ?></p>
                <p><strong>Last Name:</strong> <?php echo h($customer['last_name']); ?></p>
                <p><strong>Email:</strong> <?php echo h($customer['email']); ?></p>
                <p><strong>Registration Date:</strong> <?php echo h($customer['registration_date']); ?></p>
                </div>
            <a href="customer.php" class="back-button">Back to Customers</a>
        <?php else: ?>
            <p>No customer data found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
<?php $conn = null; ?>
