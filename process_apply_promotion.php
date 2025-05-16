<?php
// process_apply_promotion.php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet";

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($_POST['action'] == 'apply_existing') {
            if (isset($_POST['product_ids']) && is_array($_POST['product_ids']) && isset($_POST['promotion_codes']) && is_array($_POST['promotion_codes'])) {
                $product_ids = $_POST['product_ids'];
                $promotion_ids = $_POST['promotion_codes'];

                // Fetch promotion details (assuming your 'promotion' table has a 'discount_percentage' column)
                $stmt_promotions = $conn->prepare("SELECT prom_id, discount_percentage FROM promotion WHERE prom_id IN (" . implode(',', array_fill(0, count($promotion_ids), '?')) . ")");
                $stmt_promotions->execute($promotion_ids);
                $promotions_data = $stmt_promotions->fetchAll(PDO::FETCH_ASSOC);

                if (!empty($promotions_data)) {
                    $success_count = 0;
                    foreach ($product_ids as $product_id) {
                        // Fetch the current product price
                        $stmt_price = $conn->prepare("SELECT prix FROM produits WHERE id = :id");
                        $stmt_price->bindParam(':id', $product_id);
                        $stmt_price->execute();
                        $product_data = $stmt_price->fetch(PDO::FETCH_ASSOC);

                        if ($product_data) {
                            $original_price = $product_data['prix'];
                            $discounted_price = $original_price;
                            $discount_percentage = 0;

                            // Apply the first selected promotion (you might want to handle multiple promotions differently)
                            $promotion = $promotions_data[0]; // Applying the first selected promotion

                            if (isset($promotion['discount_percentage']) && $promotion['discount_percentage'] > 0) {
                                $discount_percentage = $promotion['discount_percentage'];
                                $discount_amount = $original_price * ($discount_percentage / 100);
                                $discounted_price = $original_price - $discount_amount;

                                // Update the products table with promotion details
                                $stmt_update = $conn->prepare("UPDATE produits SET original_price = :original_price, discounted_price = :discounted_price, discount_percentage = :discount_percentage WHERE id = :id");
                                $stmt_update->bindParam(':original_price', $original_price);
                                $stmt_update->bindParam(':discounted_price', $discounted_price);
                                $stmt_update->bindParam(':discount_percentage', $discount_percentage);
                                $stmt_update->bindParam(':id', $product_id);

                                if ($stmt_update->execute()) {
                                    $success_count++;
                                }
                            }
                        }
                    }
                    $_SESSION['apply_promotion_message'] = "Successfully applied promotion to " . $success_count . " products.";
                } else {
                    $_SESSION['apply_promotion_message'] = "Error: No valid promotion codes selected.";
                }
            } else {
                $_SESSION['apply_promotion_message'] = "Error: Please select products and promotion codes.";
            }
        }

    } catch (PDOException $e) {
        $_SESSION['apply_promotion_message'] = "Database Error: " . $e->getMessage();
    }

    header("Location: apply_promotion.php");
    exit();
}
?>