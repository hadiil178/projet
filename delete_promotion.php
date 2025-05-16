<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet";

$message = "";
$promotions = [];

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all promotions
    $stmt_promotions = $conn->query("SELECT prom_id, code FROM promotion");
    $promotions = $stmt_promotions->fetchAll(PDO::FETCH_ASSOC);

    session_start();
    if (isset($_SESSION['delete_promotion_message'])) {
        $message = $_SESSION['delete_promotion_message'];
        unset($_SESSION['delete_promotion_message']);
    }

} catch (PDOException $e) {
    $message = "<span style='color:red;'>Database error: " . $e->getMessage() . "</span>";
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Promotion Codes</title>
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
            max-width: 600px;
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

        form {
            text-align: left;
        }

        .promotion-list {
            margin-bottom: 20px;
            border: 1px solid #D3CEDF;
            border-radius: 6px;
            padding: 15px;
        }

        .promotion-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .promotion-item input[type="checkbox"] {
            margin-right: 10px;
        }

        .promotion-item label {
            font-size: 1.1em;
            color: #444;
        }

        button[type="submit"] {
            background-color: #e74c3c;
            /* Red button for delete */
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1.2em;
            transition: background-color 0.3s ease;
            margin-top: 20px;
            width: 100%;
            font-family: 'Roboto', sans-serif;
            /* Consistent font */
            outline: none;
            /* Remove default focus outline */
        }

        button[type="submit"]:hover {
            background-color: #c0392b;
            /* Darker red on hover */
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
            /* Consistent font */
        }

        .back-link:hover {
            color: #217dbb;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
                width: 95%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Delete Promotion Codes</h2>

        <?php if (!empty($message)): ?>
            <p class="message <?php echo (strpos($message, 'success') !== false) ? 'success' : 'error'; ?>">
                <?php echo $message; ?></p>
        <?php endif; ?>

        <form method="post" action="process_delete_promotion.php">
            <div class="promotion-list">
                <?php if (empty($promotions)): ?>
                    <p>No promotion codes found.</p>
                <?php else: ?>
                    <?php foreach ($promotions as $promotion): ?>
                        <div class="promotion-item">
                            <input type="checkbox" name="promotion_ids_to_delete[]"
                                value="<?php echo $promotion['prom_id']; ?>" id="promotion_<?php echo $promotion['prom_id']; ?>">
                            <label for="promotion_<?php echo $promotion['prom_id']; ?>">
                                <?php echo htmlspecialchars($promotion['code']); ?></label>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <button type="submit"
                onclick="return confirm('Are you sure you want to delete the selected promotion codes?')">Delete Selected
                Promotions</button>
        </form>
        <a href="apply_promotion.php" class="back-link">Back to Apply Promotion</a>
        <a href="index.php" class="back-link">Back to Dashboard</a>
        
    </div>
</body>

</html>