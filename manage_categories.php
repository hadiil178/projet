<?php
// Database connection (as in index.php)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all categories (assuming you have a 'categories' table)
    $stmt = $conn->prepare("SELECT id, nom FROM categories"); // Select only necessary columns
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    echo "Erreur de connexion à la base de données: " . $e->getMessage();
    $categories = [];
}

// Function to safely display HTML.
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Manage Categories</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
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
            max-width: 1000px;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            color: #8C5356; /* Logo text color */
            text-align: center;
            margin-bottom: 25px;
            font-size: 2.5em;
        }

        .add-category-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #8C5356; /* Accent color */
            text-decoration: none;
            transition: color 0.3s ease;
            font-weight: bold;
        }

        .add-category-link:hover {
            color: #734043; /* Darker accent for hover */
            text-decoration: underline;
        }

        table {
            width: 100%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden; /* for rounded corners of thead/tbody */
        }

        thead {
            background-color: #f5f5f0;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            color: #555; /* Label color */
            font-weight: 500;
            font-size: 1.1em;
        }

        td {
            color: #333;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .actions a, .actions button {
            margin-right: 10px;
            text-decoration: none;
            color: #8C5356; /* Accent color */
            font-weight: bold;
            transition: color 0.3s ease;
            border: none;
            background: none;
            padding: 0;
            cursor: pointer;
        }

        .actions a:hover, .actions button:hover {
            color: #734043; /* Darker accent for hover */
            text-decoration: underline;
        }

        .actions a:last-child, .actions button:last-child {
            margin-right: 0;
        }

        .delete-button {
            color: red;
        }

        .delete-button:hover {
            color: darkred;
        }

        .back-to-index {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #8C5356; /* Accent color */
            text-decoration: none;
            transition: color 0.3s ease;
            font-weight: bold;
            font-size: 1.1em;
        }

        .back-to-index:hover {
            color: #734043; /* Darker accent for hover */
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Categories</h1>
        <a href="ajoutercategory.html" class="add-category-link">Add New Category</a>
        <?php if (!empty($categories)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?php echo $category['id']; ?></td>
                            <td><?php echo h($category['nom']); ?></td>
                            <td class="actions">
                                <a href="edit_category.php?id=<?php echo $category['id']; ?>">Edit</a>
                                <button class="delete-button" onclick="deleteCategory(<?php echo h($category['id']); ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No categories found.</p>
        <?php endif; ?>
        <a href="index.php" class="back-to-index">Back to Home</a>
    </div>

    <script>
    function deleteCategory(categoryId) {
        if (confirm("Are you sure you want to delete this category?")) {
            // Use AJAX to send a POST request to the server for deletion
            fetch('delete_category.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id=' + categoryId,
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to delete category.');
                }
                return response.text();
            })
            .then(data => {
                if (data === "success") {
                    // Remove the category row from the table
                    const button = document.querySelector(`button[onclick="deleteCategory('${categoryId}')"]`);
                    if (button && button.parentNode && button.parentNode.parentNode) {
                        const row = button.parentNode.parentNode;
                        row.remove();
                        alert("Category deleted successfully.");

                        // Check if the table is empty
                        const categoryTableBody = document.querySelector("table tbody");
                        if (categoryTableBody && categoryTableBody.rows.length === 0) {
                            const table = document.querySelector("table");
                            if (table) {
                                table.style.display = "none";
                            }
                            const noCategoriesMessage = document.querySelector(".container p");
                            if (noCategoriesMessage) {
                                noCategoriesMessage.textContent = "No categories found.";
                            } else {
                                const newNoCategoriesMessage = document.createElement("p");
                                newNoCategoriesMessage.textContent = "No categories found.";
                                document.querySelector(".container").appendChild(newNoCategoriesMessage);
                            }
                        }
                    }
                } else {
                    alert("Error deleting category: " + data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("An error occurred while deleting the category.");
            });
        }
    }
    </script>
</body>
</html>
<?php $conn = null; ?>