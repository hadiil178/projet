<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $newCategoryName = $_POST["new_category_name"];

        // Prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO categories (nom) VALUES (:nom)"); // Changed 'nom_categorie' to 'nom'
        $stmt->bindParam(':nom', $newCategoryName);

        if ($stmt->execute()) {
            $response = ['status' => 'success', 'message' => "La catégorie '$newCategoryName' a été ajoutée avec succès.", 'new_category' => htmlspecialchars(ucfirst($newCategoryName)), 'new_category_value' => htmlspecialchars($newCategoryName)];
        } else {
            $response = ['status' => 'error', 'message' => "Erreur lors de l'ajout de la catégorie."];
        }
    } else {
        $response = ['status' => 'error', 'message' => "Méthode de requête invalide."];
    }

} catch(PDOException $e) {
    $response = ['status' => 'error', 'message' => "Erreur de connexion à la base de données: " . $e->getMessage()];
}

// Send the JSON response
header('Content-Type: application/json');
echo json_encode($response);

// Close the database connection
$conn = null;
?>