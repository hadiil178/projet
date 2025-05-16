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

        // Fetch the category data based on the ID
        $stmt = $conn->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$category) {
            // If category not found, redirect to manage categories page
            header("Location: manage_categories.php");
            exit;
        }
    } else {
        // If no ID is provided, redirect to manage categories page
        header("Location: manage_categories.php");
        exit;
    }

    // Handle form submission for updating the category
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nom_categorie = $_POST['nom_categorie'];

        if (empty($nom_categorie)) {
            $error_message = "Le nom de la catégorie est obligatoire.";
        } else {
            $update_stmt = $conn->prepare("UPDATE categories SET nom_categorie = :nom_categorie WHERE id = :id");
            $update_stmt->bindParam(':nom_categorie', $nom_categorie);
            $update_stmt->bindParam(':id', $id);

            if ($update_stmt->execute()) {
                $success_message = "Catégorie mise à jour avec succès!";
                // Optionally redirect back to manage categories page
                header("Location: manage_categories.php?update=success");
                exit;
            } else {
                $error_message = "Erreur lors de la mise à jour de la catégorie.";
            }
        }
    }

} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données: " . $e->getMessage();
    // Consider redirecting to an error page
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Edit Category</title>
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
            max-width: 500px;
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

        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd; /* Input border color */
            border-radius: 5px;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif; /* Consistent font */
            font-size: 1em;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus {
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
            margin: 20px auto; /* Center the button with some top margin */
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
            display: block; /* Make button a block element */
            margin: 10px auto;
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
            text-align: center;
        }

        .success-message {
            color: green;
            margin-top: 10px;
            text-align: center;
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
        <h1>Edit Category</h1>
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <?php if (isset($success_message)): ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <form method="post">
            <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
            <div class="form-group">
                <label for="nom_categorie">Category Name:</label>
                <input type="text" id="nom_categorie" name="nom_categorie" value="<?php echo htmlspecialchars($category['nom_categorie']); ?>" required>
            </div>
            <button type="submit" class="btn-primary">Update Category</button>
            <a href="manage_categories.php" class="btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>