<?php
// process_delete_promotion.php

// Database connection details (ensure these are correct)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet";

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['promotion_ids_to_delete'])) {
    $promotion_ids_to_delete = $_POST['promotion_ids_to_delete'];

    if (!empty($promotion_ids_to_delete)) {
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->beginTransaction();

            // Prepare statement to delete from the promotion table
            $stmt_delete_promotion = $conn->prepare("DELETE FROM promotion WHERE prom_id = :prom_id");
            $stmt_delete_promotion->bindParam(':prom_id', $prom_id);

            // Prepare statement to get product IDs linked to the promotion
            $stmt_get_product_ids = $conn->prepare("SELECT product_id FROM produit_promotion WHERE promotion_id = :promotion_id");
            $stmt_get_product_ids->bindParam(':promotion_id', $prom_id);

            // Prepare statement to update product discount information
            $stmt_update_product = $conn->prepare("UPDATE produits SET discount_percentage = NULL, discounted_price = original_price WHERE id = :product_id");
            $stmt_update_product->bindParam(':product_id', $product_id);

            // Prepare statement to delete links from the produit_promotion table
            $stmt_delete_links = $conn->prepare("DELETE FROM produit_promotion WHERE promotion_id = :promotion_id");
            $stmt_delete_links->bindParam(':promotion_id', $prom_id);

            $deleted_count = 0;
            foreach ($promotion_ids_to_delete as $prom_id) {
                // 1. Delete the promotion
                $stmt_delete_promotion->execute();

                // 2. Get the IDs of products linked to this promotion
                $stmt_get_product_ids->bindValue(':promotion_id', $prom_id);
                $stmt_get_product_ids->execute();
                $linked_products = $stmt_get_product_ids->fetchAll(PDO::FETCH_COLUMN);

                // 3. Clear the discount information for the linked products
                foreach ($linked_products as $product_id) {
                    $stmt_update_product->bindValue(':product_id', $product_id);
                    $stmt_update_product->execute();
                }

                // 4. Delete the links in the produit_promotion table
                $stmt_delete_links->bindValue(':promotion_id', $prom_id);
                $stmt_delete_links->execute();

                $deleted_count++;
            }

            $conn->commit();
            $_SESSION['delete_promotion_message'] = "Successfully deleted " . $deleted_count . " promotion(s) and updated associated product discounts.";
            header("Location: delete_promotion.php");
            exit();

        } catch (PDOException $e) {
            $conn->rollBack();
            $_SESSION['delete_promotion_message'] = "<span style='color:red;'>Error deleting promotions: " . $e->getMessage() . "</span>";
            header("Location: delete_promotion.php");
            exit();
        }
    } else {
        $_SESSION['delete_promotion_message'] = "No promotions selected for deletion.";
        header("Location: delete_promotion.php");
        exit();
    }
} else {
    header("Location: delete_promotion.php");
    exit();
}
?>