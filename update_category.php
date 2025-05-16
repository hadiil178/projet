<?php
// Database connection (as in index.php)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['id']) && isset($_POST['nom_categorie'])) {
            $id = $_POST['id'];
            $nom_categorie = $_POST['nom_categorie'];

            if (empty($nom_categorie)) {
                header("Location: edit_category.php?id=$id&error=Le nom de la catégorie est obligatoire.");
                exit;
            }

            $update_stmt = $conn->prepare("UPDATE categories SET nom_categorie = :nom_categorie WHERE id = :id");
            $update_stmt->bindParam(':nom_categorie', $nom_categorie);
            $update_stmt->bindParam(':id', $id);

            if ($update_stmt->execute()) {
                header("Location: manage_categories.php?update=success");
                exit;
            } else {
                header("Location: edit_category.php?id=$id&error=Erreur lors de la mise à jour de la catégorie.");
                exit;
            }
        } else {
            // If ID or category name is missing
            header("Location: manage_categories.php?error=Données de mise à jour manquantes.");
            exit;
        }
    } else {
        // If accessed directly without POST request
        header("Location: manage_categories.php");
        exit;
    }

} catch (PDOException $e) {
    // Log the error for debugging purposes
    error_log("Database Error in update_category.php: " . $e->getMessage());
    header("Location: manage_categories.php?error=Erreur de base de données.");
    exit;
}

// Close the database connection (optional, PDO handles this on script end)
$conn = null;
?>