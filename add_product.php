<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet";

$message = ""; // Variable to store success/error messages

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all categories from the database for the dropdown
    $stmt_categories = $conn->query("SELECT nom FROM categories ORDER BY nom");
    $categories = $stmt_categories->fetchAll(PDO::FETCH_COLUMN);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nom = $_POST["nom"];
        $description = $_POST["description"];
        $prix = $_POST["prix"];
        $category = $_POST["category"]; // Get the category from the form
        $stock = $_POST["stock"]; // Get the stock quantity from the form
        $tailles_disponibles = isset($_POST["tailles_disponibles"]) ? $_POST["tailles_disponibles"] : ""; // Get available sizes

        // Handle image upload
        $targetDir = "uploads/"; // Create this directory if it doesn't exist
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $targetFile = $targetDir . basename($_FILES["image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($targetFile,PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check === false) {
            $message = "<span style='color:red;'>Le fichier n'est pas une image valide.</span>";
            $uploadOk = 0;
        }

        // Check file size (you can adjust this limit)
        if ($_FILES["image"]["size"] > 500000) {
            $message = "<span style='color:red;'>L'image est trop volumineuse.</span>";
            $uploadOk = 0;
        }

        // Allow certain file formats
        $allowedFormats = array("jpg", "jpeg", "png", "gif");
        if(!in_array($imageFileType, $allowedFormats)) {
            $message = "<span style='color:red;'>Seuls les formats JPG, JPEG, PNG & GIF sont autorisés.</span>";
            $uploadOk = 0;
        }

        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                $image_path = $targetFile; // Save the path to the uploaded image

                // Basic validation for other fields
                if (empty($nom) || empty($prix) || empty($category) || empty($stock)) { // Added stock to required fields
                    $message = "<span style='color:red;'>Le nom, le prix, la catégorie et la quantité en stock sont obligatoires.</span>";
                } else {
                    // Prepare and execute the SQL query to insert the new product (INCLUDE STOCK AND TAILLES_DISPONIBLES)
                    $sql = "INSERT INTO produits (nom, description, prix, image_path, category, stock, tailles_disponibles) VALUES (:nom, :description, :prix, :image_path, :category, :stock, :tailles_disponibles)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':nom', $nom);
                    $stmt->bindParam(':description', $description);
                    $stmt->bindParam(':prix', $prix);
                    $stmt->bindParam(':image_path', $image_path);
                    $stmt->bindParam(':category', $category);
                    $stmt->bindParam(':stock', $stock); // Bind the stock value
                    $stmt->bindParam(':tailles_disponibles', $tailles_disponibles); // Bind available sizes

                    if ($stmt->execute()) {
                        $message = "<span style='color:green;'>Produit ajouté avec succès.</span>";

                        // --- Code to update the statistics table ---
                        try {
                            // Calculate the new total stock from the produits table
                            $sql_total_stock = "SELECT SUM(stock) AS total_stock FROM produits";
                            $stmt_total_stock = $conn->query($sql_total_stock);
                            $result_total_stock = $stmt_total_stock->fetch(PDO::FETCH_ASSOC);
                            $new_total_stock = $result_total_stock['total_stock'];

                            // Update the statistics table with the new total stock
                            $sql_update_stats = "UPDATE statistics SET stock = :total_stock WHERE stat_id = 1";
                            $stmt_update_stats = $conn->prepare($sql_update_stats);
                            $stmt_update_stats->bindParam(':total_stock', $new_total_stock);
                            $stmt_update_stats->execute();

                            // You might want to handle the case where there's no row in 'statistics' yet (e.g., insert one initially)

                        } catch (PDOException $e) {
                            $message .= "<br><span style='color:orange;'>Erreur lors de la mise à jour des statistiques de stock: " . $e->getMessage() . "</span>";
                        }
                        // --- End of statistics update ---

                    } else {
                        $message = "<span style='color:red;'>Erreur lors de l'ajout du produit.</span>";
                    }
                }
            } else {
                $message = "<span style='color:red;'>Erreur lors du téléchargement de l'image.</span>";
            }
        }
    }
} catch(PDOException $e) {
    $message = "<span style='color:red;'>Erreur de connexion à la base de données: " . $e->getMessage() . "</span>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Produit - Elegance</title>
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
            width: 80%;
            max-width: 600px;
        }

        h2 {
            font-family: 'Playfair Display', serif;
            color: #8C5356; /* Logo text color */
            text-align: center;
            margin-bottom: 25px;
            font-size: 2.5em;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555; /* Label color */
            font-weight: 500;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        input[type="file"],
        select, /* Added select */
        button {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd; /* Input border color */
            border-radius: 8px;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif; /* Consistent font */
            font-size: 1em;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        input[type="file"] {
            padding-top: 8px;
        }

        select {
            padding: 10px; /* Adjust padding for select */
        }

        button {
            background-color: #8C5356; /* Button background color */
            color: white;
            border: none;
            padding: 15px 25px;
            border-radius: 30px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 1.1em;
            font-family: 'Roboto', sans-serif; /* Consistent font */
            outline: none; /* Remove default focus outline */
        }

        button:hover {
            background-color: #734043; /* Darker accent for hover */
        }

        .error-message {
            color: red;
            margin-top: 10px;
        }

        .success-message {
            color: green;
            margin-top: 10px;
        }

        .back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #8C5356; /* Accent color */
            text-decoration: none;
            font-family: 'Roboto', sans-serif; /* Consistent font */
        }

        .back-link:hover {
            text-decoration: underline;
            color: #734043; /* Darker accent for hover */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Ajouter un Produit</h2>
        <?php if (!empty($message)): ?>
            <p class="<?php echo (strpos($message, 'succès') !== false) ? 'success-message' : 'error-message'; ?>"><?php echo $message; ?></p>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nom">Nom du Produit:</label>
                <input type="text" id="nom" name="nom" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description"></textarea>
            </div>

            <div class="form-group">
                <label for="prix">Prix (DA):</label>
                <input type="number" id="prix" name="prix" min="0" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="stock">Quantité en Stock:</label>
                <input type="number" id="stock" name="stock" min="0" value="0" required>
            </div>

            <div class="form-group">
                <label for="tailles_disponibles">Tailles Disponibles (séparées par des virgules):</label>
                <input type="text" id="tailles_disponibles" name="tailles_disponibles" placeholder="e.g., 100 ml, S, Rouge">
            </div>

            <div class="form-group">
                <label for="image">Image du Produit:</label>
                <input type="file" id="image" name="image" accept="image/*" required>
                <small>Formats acceptés: JPG, JPEG, PNG, GIF</small>
            </div>

            <div class="form-group">
                <label for="category">Catégorie:</label>
                <select id="category" name="category" required>
                    <option value="">Sélectionner une catégorie</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars(ucfirst($cat)); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="button" onclick="openAddCategoryPopup()">Ajouter une nouvelle catégorie</button>
            </div>

            <button type="submit">Ajouter le Produit</button>
        </form>
        <a href="./index.php" class="back-link">Retour à l'accueil</a>
    </div>

    <script>
        function openAddCategoryPopup() {
            window.open('ajoutercategory.html', 'AddCategory', 'width=600,height=400');
        }

        window.addEventListener('message', function(event) {
            if (event.data.type === 'newCategoryAdded') {
                const categorySelect = document.getElementById('category');
                const newOption = document.createElement('option');
                newOption.value = event.data.value;
                newOption.textContent = event.data.name;
                categorySelect.appendChild(newOption);
                categorySelect.value = event.data.value; // Optionally select the newly added category
            }
        });
    </script>
</body>
</html>