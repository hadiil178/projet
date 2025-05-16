<?php
// Database connection (adjust these values based on your setup)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if all required fields are set
        if (isset($_POST['id'], $_POST['name'], $_POST['price'], $_POST['category'], $_POST['image_path'])) {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $price = $_POST['price'];
            $category = $_POST['category'];
            $image_path = $_POST['image_path'];

            // Prepare the SQL statement to update the product
            $stmt = $conn->prepare("UPDATE produits SET nom = :name, prix = :price, category = :category, image_path = :image_path WHERE id = :id");

            // Bind the parameters
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':category', $category);
            $stmt->bindParam(':image_path', $image_path);

            // Execute the statement
            if ($stmt->execute()) {
                // Product updated successfully, redirect to the manage products page
                header("Location: manage_products.php?update=success");
                exit;
            } else {
                // Error during update
                echo "Erreur lors de la mise à jour du produit.";
                // You might want to redirect to an error page or display a more user-friendly message
            }
        } else {
            echo "Tous les champs du formulaire doivent être remplis.";
            // Consider redirecting back to the edit form with an error message
        }
    } else {
        // If the page is accessed directly (not via POST), redirect to manage products
        header("Location: manage_products.php");
        exit;
    }
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données: " . $e->getMessage();
    // Consider redirecting to an error page
}
?>