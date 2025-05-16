<?php
// Database connection (as in index.php)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if ID is provided in the URL
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        // Fetch the product data based on the ID
        $stmt = $conn->prepare("SELECT * FROM produits WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            // If product not found, redirect to manage products page
            header("Location: manage_products.php");
            exit;
        }
    } else {
        // If no ID is provided, redirect to manage products page
        header("Location: manage_products.php");
        exit;
    }
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données: " . $e->getMessage();
    // Consider redirecting to an error page instead of manage_products.php
    exit; // Stop execution after the error message
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Edit Product</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
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
            max-width: 800px; /* Adjusted max-width */
        }

        h1 {
            font-family: 'Playfair Display', serif;
            color: #8C5356; /* Logo text color */
            text-align: center;
            margin-bottom: 25px;
            font-size: 2.5em;
        }

        form {
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555; /* Label color */
            font-weight: 500;
            font-size: 1.1em;
        }

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd; /* Input border color */
            border-radius: 5px;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif; /* Consistent font */
            font-size: 1em;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus {
            border-color: #8C5356; /* Focus border color */
            outline: none;
            box-shadow: 0 0 8px rgba(140, 83, 86, 0.2); /* Focus shadow */
        }

        .btn-primary {
            background-color: #8C5356; /* Button background color */
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'Roboto', sans-serif; /* Consistent font */
            font-size: 1.2em;
            transition: background-color 0.3s ease;
            display: block; /* Make button a block element */
            margin: 0 auto; /* Center the button */
            width: fit-content; /* Adjust width to content */
            outline: none; /* Remove default focus outline */
        }

        .btn-primary:hover {
            background-color: #734043; /* Darker accent for hover */
        }

        .btn-secondary {
            background-color: #e0e0e0;
            color: #333;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'Roboto', sans-serif; /* Consistent font */
            font-size: 1em;
            transition: background-color 0.3s ease;
            margin-top: 10px; /* Add space between buttons */
            display: block; /* Make button a block element */
            margin: 0 auto;
            width: fit-content;
            text-decoration: none;
            text-align: center;
        }

        .btn-secondary:hover {
            background-color: #f0f0f0;
        }


        .error-message {
            color: red;
            margin-top: 10px;
            font-size: 0.9em;
        }

        @media (max-width: 768px) {
            .container {
                width: 100%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Product</h1>
        <form method="post" action="update_product.php">
            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['nom']); ?>" required>
            </div>
            <div class="form-group">
                <label for="tailles_disponibles">Available Sizes (comma-separated):</label>
                <input type="text" id="tailles_disponibles" name="tailles_disponibles" value="<?php echo htmlspecialchars($product['tailles_disponibles']); ?>">
                <small>Enter available sizes separated by commas (e.g., 50 ml, 100 ml, 200 ml).</small>
            </div>
            <div class="form-group">
                <label for="price">Price (DA):</label>
                <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($product['prix']); ?>" required min="0" step="0.01">
            </div>
            <div class="form-group">
                <label for="category">Category:</label>
                <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($product['category']); ?>" required>
            </div>
            <div class="form-group">
                <label for="stock">Stock Quantity:</label>
                <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" required min="0">
            </div>
            <div class="form-group">
                <label for="image_path">Image Path:</label>
                <input type="text" id="image_path" name="image_path" value="<?php echo htmlspecialchars($product['image_path']); ?>" required>
            </div>
            <button type="submit" class="btn-primary">Update Product</button>
            <a href="manage_products.php" class="btn-secondary">Cancel</a>
        </form>
    </div>
    <script>
    // You can add javascript here
    </script>
</body>
</html>