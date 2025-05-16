<?php
// Database connection (as in index.php)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all products
    $stmt = $conn->prepare("SELECT * FROM produits");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    echo "Erreur de connexion à la base de données: " . $e->getMessage();
    $products = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Manage Products</title>
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
            max-width: 1000px;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            color: #8C5356; /* Logo text color */
            text-align: center;
            margin-bottom: 25px;
            font-size: 2.5em;
        }

        .add-product-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #8C5356; /* Accent color */
            text-decoration: none;
            transition: color 0.3s ease;
            font-weight: bold;
        }

        .add-product-link:hover {
            color: #734043; /* Darker accent for hover */
            text-decoration: underline;
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
            margin-right: 15px;
            text-decoration: none;
            color: #8C5356; /* Accent color */
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .actions a:hover {
            color: #734043; /* Darker accent for hover */
            text-decoration: underline;
        }

        .actions a:last-child {
            margin-right: 0;
        }

        .delete-link {
            color: red;
        }

        .delete-link:hover {
            color: darkred;
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
        <h1>Manage Products</h1>
        <a href="add_product.php" class="add-product-link">Add New Product</a>
        <?php if (!empty($products)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Image Path</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td><?php echo htmlspecialchars($product['nom']); ?></td>
                            <td><?php echo htmlspecialchars($product['prix']); ?> DA</td>
                            <td><?php echo htmlspecialchars($product['category']); ?></td>
                            <td><?php echo htmlspecialchars($product['image_path']); ?></td>
                            <td class="actions">
                                <a href="edit_product.php?id=<?php echo $product['id']; ?>">Edit</a>
                                <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="delete-link" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No products found.</p>
        <?php endif; ?>
        <a href="index.php" class="back-to-index">Back to Home</a>
    </div>
</body>
</html>
<?php $conn = null; ?>