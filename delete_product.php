<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $product_id = $_GET['id'];

        // Prepare the SQL statement to delete the product
        $stmt = $conn->prepare("DELETE FROM produits WHERE id = :id");
        $stmt->bindParam(':id', $product_id);

        // Execute the deletion
        if ($stmt->execute()) {
            // Deletion successful, redirect with a success message
            header("Location: manage_products.php?delete=success");
            exit;
        } else {
            // Error during deletion, redirect with an error message
            header("Location: manage_products.php?delete=error");
            exit;
        }
    } else {
        // If no ID is provided or it's not numeric, redirect with an error
        header("Location: manage_products.php?delete=invalid_id");
        exit;
    }

} catch (PDOException $e) {
    // Database connection error
    header("Location: manage_products.php?delete=db_error");
    exit;
}
?>