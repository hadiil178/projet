<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet";

$product = null; // Initialize the product variable
$available_sizes = []; // Initialize an array for available sizes

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the 'id' parameter exists in the URL
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $product_id = $_GET['id'];

        // Prepare and execute the SQL query to fetch the product details including stock and promotion info
        $stmt = $conn->prepare("SELECT * FROM produits WHERE id = :id");
        $stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch the product details as an associative array
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        // If no product is found with the given ID, handle this case
        if (!$product) {
            echo "Produit non trouvé.";
            exit();
        }

        // Assuming you have a column named 'tailles_disponibles' (or similar)
        // in your 'produits' table that stores available sizes as a comma-separated string
        if (isset($product['tailles_disponibles']) && !empty($product['tailles_disponibles'])) {
            $available_sizes = explode(',', trim($product['tailles_disponibles']));
            // Trim whitespace from each size
            $available_sizes = array_map('trim', $available_sizes);
        } else {
            // If the 'tailles_disponibles' column is not set or empty,
            // provide a default size or handle as needed.
            $available_sizes = ['200ml']; // Default size based on the image
        }

    } else {
        // If 'id' is not set or not a number, display an error
        echo "ID de produit invalide.";
        exit();
    }

} catch(PDOException $e) {
    echo "Erreur de connexion à la base de données: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['nom'] ?? 'Détails du Produit'); ?> - Elegance</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            margin: 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 95%; /* Adjusted width to accommodate the layout */
            max-width: 1200px; /* Increased max width */
        }

        .product-details-layout {
            display: grid;
            grid-template-columns: 1fr 1.2fr 1fr; /* Adjust the ratios as needed */
            gap: 30px;
            align-items: center; /* Vertically align the elements in the middle */
        }

        .product-info-left {
            text-align: left;
        }

        .brand-name {
            /* Removed brand name styling */
        }

        .product-name {
            font-family: 'Georgia', serif;
            font-size: 2.5em;
            color: #333;
            margin-top: 0;
            margin-bottom: 10px;
        }

        .product-image {
            text-align: center;
            position: relative;
        }

        .product-image img {
            max-width: 100%; /* Ensure image fits within its column */
            height: auto;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .new-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #000;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8em;
            font-weight: bold;
        }

        .product-info-right {
            text-align: right;
        }

        .price-container {
            margin-bottom: 20px;
        }

        .original-price {
            font-size: 1.6em;
            color: #777;
            text-decoration: line-through;
            margin-right: 10px;
        }

        .discounted-price {
            font-size: 2.2em;
            color: #8b4513;
            font-weight: bold;
        }

        .discount-percentage {
            color: green;
            font-weight: bold;
            font-size: 1em;
            margin-left: 10px;
        }

        .purchase-options {
            display: flex;
            flex-direction: column; /* Stack options vertically on the right */
            align-items: flex-end; /* Align items to the right */
            gap: 10px;
        }

        .purchase-options select {
            padding: 10px 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1em;
            min-width: 150px; /* Ensure elements don't collapse */
        }

        .purchase-options button {
            /* Removed button styling */
        }

        .stock-available {
            color: green;
            font-weight: bold;
            font-size: 1em;
        }

        .stock-out {
            color: red;
            font-weight: bold;
            font-size: 1em;
        }

        .product-code {
            color: #777;
            font-size: 0.9em;
        }

        .characteristics {
            grid-column: 1 / -1; /* Make characteristics span the full width */
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .characteristics h4 {
            font-family: 'Georgia', serif;
            color: #333;
            font-size: 1.6em;
            margin-bottom: 15px;
        }

        .characteristics p {
            line-height: 1.7;
            color: #555;
            font-size: 1em;
        }

        .back-link {
            display: block;
            margin-top: 30px;
            text-align: center;
            color: #555;
            text-decoration: none;
            grid-column: 1 / -1; /* Make back link span full width */
        }

        .back-link:hover {
            text-decoration: underline;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="product-details-layout">
            <div class="product-info-left">
                <h3 class="product-name"><?php echo htmlspecialchars($product['nom']); ?></h3>
            </div>
            <div class="product-image">
                <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['nom']); ?>">
                <?php if (isset($product['nouveau']) && $product['nouveau']): ?>
                    <div class="new-badge">NOUVEAU</div>
                <?php endif; ?>
            </div>
            <div class="product-info-right">
                <div class="price-container">
                    <?php if (isset($product['discounted_price']) && $product['discounted_price'] < $product['prix'] && $product['discount_percentage'] > 0): ?>
                        <span class="original-price"><?php echo htmlspecialchars($product['prix']); ?> DA</span>
                        <span class="discounted-price"><?php echo htmlspecialchars($product['discounted_price']); ?> DA</span>
                        <span class="discount-percentage">(<?php echo htmlspecialchars(round($product['discount_percentage'])); ?>% off)</span>
                    <?php else: ?>
                        <p class="price"><?php echo htmlspecialchars($product['prix']); ?> DA</p>
                    <?php endif; ?>
                </div>
                <div class="purchase-options">
                    <select>
                        <?php foreach ($available_sizes as $size): ?>
                            <option><?php echo htmlspecialchars($size); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($product['stock'] > 0): ?>
                        <p class="stock-available">En stock!</p>
                    <?php else: ?>
                        <p class="stock-out">Rupture de stock</p>
                    <?php endif; ?>
                    <p class="product-code">Code produit: <?php echo htmlspecialchars($product['id']); ?></p>
                </div>
            </div>
        </div>
        <div class="characteristics">
            <h4>Caractéristiques</h4>
            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
        </div>
        <a href="./index.php" class="back-link">Retour à la page d'accueil</a>
    </div>
</body>
</html>
<?php $conn = null; ?>