<?php
// Database connection details (as in your original script)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet";

try {
    $conn = new PDO('mysql:host=' . $servername . ';dbname=' . $dbname, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $new_category_name = isset($_POST['new_category_name']) ? trim($_POST['new_category_name']) : '';

        if (!empty($new_category_name)) {
            $stmt_check = $conn->prepare("SELECT COUNT(*) FROM categories WHERE nom = :nom");
            $stmt_check->bindParam(':nom', $new_category_name);
            $stmt_check->execute();
            $category_exists = $stmt_check->fetchColumn();

            if ($category_exists > 0) {
                $response = ['status' => 'error', 'message' => "La catégorie '" . htmlspecialchars($new_category_name) . "' existe déjà."];
            } else {
                $stmt_insert = $conn->prepare("INSERT INTO categories (nom) VALUES (:nom)");
                $stmt_insert->bindParam(':nom', $new_category_name);

                if ($stmt_insert->execute()) {
                    $response = ['status' => 'success', 'message' => "La catégorie '" . htmlspecialchars($new_category_name) . "' a été ajoutée avec succès.", 'new_category' => htmlspecialchars(ucfirst($new_category_name)), 'new_category_value' => htmlspecialchars($new_category_name)];
                } else {
                    $response = ['status' => 'error', 'message' => "Une erreur est survenue lors de l'ajout de la catégorie."];
                }
            }
        } else {
            $response = ['status' => 'error', 'message' => "Veuillez saisir un nom pour la nouvelle catégorie."];
        }

        // Send the response as JSON
        header('Content-Type: application/json');
        echo json_encode($response);
        exit(); // Important to stop further PHP execution
    }

} catch(PDOException $e) {
    $response = ['status' => 'error', 'message' => "Erreur de connexion à la base de données: " . $e->getMessage()];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

$conn = null;
?>