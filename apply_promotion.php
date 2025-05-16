<?php
// apply_promotion.php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet";

$message = ""; // To store success/error messages
$products = [];
$promotions = [];

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all products
    $stmt_products = $conn->query("SELECT id AS product_id, nom AS name FROM produits");
    $products = $stmt_products->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all promotions
    $stmt_promotions = $conn->query("SELECT prom_id, code FROM promotion");
    $promotions = $stmt_promotions->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // It's generally better to log errors or display a user-friendly message
    // instead of echoing the raw error, especially in a production environment.
    error_log("Database Error: " . $e->getMessage());
    $message = "Error: Could not connect to the database or fetch data.";
    // Optionally, you could set an HTTP error code: http_response_code(500);
}

session_start();
if (isset($_SESSION['apply_promotion_message'])) {
    $message = $_SESSION['apply_promotion_message'];
    unset($_SESSION['apply_promotion_message']);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply Promotion - Elegance</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #FAF6F4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            color: #333; /* Consistent text color */
        }

        
        

        .container {
            max-width: 800px;
            margin: 100px auto 40px; /* Adjusted top margin for fixed header, increased bottom margin */
            background-color: #FFFFFF;
            padding: 35px; /* Increased padding */
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); /* Slightly enhanced shadow */
            position: relative;
        }

        .page-title { /* Renamed from h2 for the main content title */
            font-family: 'Playfair Display', serif;
            color: #8C5356;
            text-align: center;
            margin-bottom: 30px; /* Increased bottom margin */
            font-weight: 700;
            font-size: 2.3em;
        }

        form {
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label { /* More specific selector for form labels */
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
            font-size: 0.95em;
        }

        .checkbox-group {
            display: flex;
            flex-direction: column;
            margin-top: 10px;
            border: 1px solid #E0DDEB; /* Lighter border color */
            border-radius: 6px;
            padding: 15px; /* Increased padding */
            max-height: 220px; /* Slightly increased max height */
            overflow-y: auto;
            position: relative;
            background-color: #FDFCFE; /* Very light background for contrast */
        }

        .checkbox-group::-webkit-scrollbar {
            width: 8px;
        }

        .checkbox-group::-webkit-scrollbar-thumb {
            background-color: #D3CEDF;
            border-radius: 4px;
        }

        .checkbox-group::-webkit-scrollbar-track {
            background-color: #F9F9F9;
        }


        .checkbox-group::before,
        .checkbox-group::after {
            content: '';
            position: absolute;
            left: 0;
            width: 100%;
            height: 15px; /* Reduced height */
            pointer-events: none;
            z-index: 1;
        }

        .checkbox-group::before {
            top: 0;
            background: linear-gradient(to bottom, rgba(253, 252, 254, 1) 0%, rgba(253, 252, 254, 0) 100%); /* Match background */
        }

        .checkbox-group::after {
            bottom: 0;
            background: linear-gradient(to top, rgba(253, 252, 254, 1) 0%, rgba(253, 252, 254, 0) 100%); /* Match background */
        }

        /* Hide fades if content isn't scrollable (approx. 7 items) */
        .checkbox-group:not(:has(.checkbox-item:nth-child(6))) ::before, /* Adjust count based on item height */
        .checkbox-group:not(:has(.checkbox-item:nth-child(6))) ::after {
            display: none;
        }

        .checkbox-item {
            margin-bottom: 12px;
            display: flex;
            align-items: center;
        }
        .checkbox-item:last-child {
            margin-bottom: 0;
        }

        .checkbox-item input[type="checkbox"] {
            margin-right: 12px;
            /* Consider custom checkbox styling for a more polished look if desired */
        }

        .checkbox-group label { /* For labels within the checkbox group */
            margin-bottom: 0;
            font-weight: 400; /* Normal weight */
            font-size: 1em;
            color: #333;
        }

        .select-all-container { /* Renamed for clarity */
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            padding: 8px 0; /* Add some padding */
        }

        .select-all-container input[type="checkbox"] {
            margin-right: 10px;
        }
        .select-all-container label {
            font-weight: 500;
            color: #555;
            font-size: 0.95em;
            margin-bottom: 0; /* Override general label margin */
        }


        button[type="submit"],
        .link-button {
            background-color: #8C5356;
            color: white;
            border: none;
            padding: 14px 25px; /* Slightly adjusted padding */
            border-radius: 30px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.1s ease;
            font-size: 1.05em; /* Slightly adjusted font size */
            font-family: 'Roboto', sans-serif;
            font-weight: 500;
            outline: none;
            width: 100%;
            box-sizing: border-box;
            text-align: center;
            text-decoration: none;
            display: block;
            margin-top: 20px;
        }
        button[type="submit"]:first-of-type {
             margin-top: 10px; /* Reduced margin for the first button after form groups */
        }


        button[type="submit"]:hover,
        .link-button:hover {
            background-color: #734043;
        }
        button[type="submit"]:active,
        .link-button:active {
            transform: translateY(1px); /* Subtle press effect */
        }

        /* Specific style for the "Delete Promotion Codes" button */
        .link-button.delete-button { /* Using a class is more robust */
            background-color: #8C5356 !important; /* Keep consistent with theme */
        }

        .link-button.delete-button:hover {
            background-color: #734043 !important;
        }
        /* If you want a different color for delete, define it here: */
        /*
        .link-button.delete-button {
            background-color: #c0392b !important;
        }
        .link-button.delete-button:hover {
            background-color: #a93226 !important;
        }
        */


        .message {
            margin-top: 25px; /* Spacing from elements above */
            margin-bottom: 20px; /* Spacing from elements below */
            padding: 15px 20px; /* Increased padding */
            border-radius: 6px;
            font-size: 1em;
            font-weight: 500;
            display: flex; /* For icon alignment */
            align-items: center;
        }
        .message i {
            margin-right: 10px;
            font-size: 1.2em;
        }

        .message.success {
            background-color: #E6F4EA;
            color: #388E3C;
            border: 1px solid #C8E6C9;
        }

        .message.error {
            background-color: #FBE9E7;
            color: #D32F2F;
            border: 1px solid #FFCDD2;
        }

        @media (max-width: 768px) {
            .container {
                margin-left: 20px;
                margin-right: 20px;
                padding: 25px;
            }
        }

        @media (max-width: 600px) {
            .header {
                padding: 12px 20px;
            }
            .header-title {
                font-size: 1.6em;
            }
            .container {
                margin-top: 80px; /* Adjust top margin for smaller screens */
                padding: 20px;
            }
            .page-title {
                font-size: 2em;
            }

            button[type="submit"],
            .link-button {
                font-size: 1em;
                padding: 12px 20px;
                margin-top: 15px;
            }
            .checkbox-group {
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    

    <div class="container">
        <h2 class="page-title">Apply Promotion to Products</h2>

        <?php if (!empty($message)): ?>
            <div class="message <?php echo (strpos(strtolower($message), 'success') !== false || strpos(strtolower($message), 'applied') !== false) ? 'success' : 'error'; ?>">
                 <?php if (strpos(strtolower($message), 'success') !== false || strpos(strtolower($message), 'applied') !== false): ?>
                    <i class="fas fa-check-circle"></i>
                <?php else: ?>
                    <i class="fas fa-exclamation-circle"></i>
                <?php endif; ?>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="process_apply_promotion.php">
            <input type="hidden" name="action" value="apply_existing">

            <div class="form-group">
                <label for="product_ids_label">Select Products:</label> <div class="select-all-container">
                    <input type="checkbox" id="select_all_products">
                    <label for="select_all_products">Select All Products</label>
                </div>
                <div class="checkbox-group" id="product_checkboxes" aria-labelledby="product_ids_label">
                    <?php if (empty($products)): ?>
                        <p>No products available.</p>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <div class="checkbox-item">
                                <input type="checkbox" id="product_<?php echo htmlspecialchars($product['product_id']); ?>"
                                       name="product_ids[]" value="<?php echo htmlspecialchars($product['product_id']); ?>">
                                <label for="product_<?php echo htmlspecialchars($product['product_id']); ?>">
                                    <?php echo htmlspecialchars($product['name']); ?></label>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="promotion_codes_label">Select Promotion Codes:</label> <div class="checkbox-group" id="promotion_checkboxes" aria-labelledby="promotion_codes_label">
                     <?php if (empty($promotions)): ?>
                        <p>No promotions available.</p>
                    <?php else: ?>
                        <?php foreach ($promotions as $promotion): ?>
                            <div class="checkbox-item">
                                <input type="checkbox" id="promotion_code_<?php echo htmlspecialchars($promotion['prom_id']); ?>"
                                       name="promotion_codes[]" value="<?php echo htmlspecialchars($promotion['prom_id']); ?>">
                                <label for="promotion_code_<?php echo htmlspecialchars($promotion['prom_id']); ?>">
                                    <?php echo htmlspecialchars($promotion['code']); ?></label>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit">Apply Selected Promotions</button>
        </form>

        <a href="add_new_code.php" class="link-button">Add New Promotion Code</a>
        <a href="index.php" class="link-button">Back to Dashboard</a>
        <a href="delete_promotion.php" class="link-button delete-button">Delete Promotion Codes</a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllProductsCheckbox = document.getElementById('select_all_products');
            const productCheckboxesContainer = document.getElementById('product_checkboxes');

            if (selectAllProductsCheckbox && productCheckboxesContainer) {
                const productCheckboxes = productCheckboxesContainer.querySelectorAll('input[type="checkbox"]');

                selectAllProductsCheckbox.addEventListener('change', function() {
                    productCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });

                productCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        if (!this.checked && selectAllProductsCheckbox.checked) {
                            selectAllProductsCheckbox.checked = false;
                        } else {
                            // Check if all product checkboxes are checked
                            let allChecked = true;
                            productCheckboxes.forEach(cb => {
                                if (!cb.checked) {
                                    allChecked = false;
                                }
                            });
                            selectAllProductsCheckbox.checked = allChecked;
                        }
                    });
                });

                // Initialize "Select All" checkbox state on page load
                if (productCheckboxes.length > 0) {
                    let allInitiallyChecked = true;
                    productCheckboxes.forEach(cb => {
                        if (!cb.checked) {
                            allInitiallyChecked = false;
                        }
                    });
                    selectAllProductsCheckbox.checked = allInitiallyChecked;
                } else {
                    selectAllProductsCheckbox.disabled = true; // Disable if no products
                }
            }

            // Optional: Add similar "Select All" for promotions if you implement it in HTML
            // const selectAllPromotionsCheckbox = document.getElementById('select_all_promotions');
            // const promotionCheckboxesContainer = document.getElementById('promotion_checkboxes');
            // if (selectAllPromotionsCheckbox && promotionCheckboxesContainer) { ... }
        });

        // The scrollDiv function is not used in this specific HTML,
        // but keeping it if it's used elsewhere or planned.
        function scrollDiv(divId, scrollAmount) {
            const div = document.getElementById(divId);
            if (div) {
                div.scrollBy({ top: scrollAmount, behavior: 'smooth' });
            }
        }
    </script>
</body>

</html>