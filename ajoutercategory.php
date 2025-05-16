<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Catégorie - Elegance</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #FAF6F4; /* Main background color */
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
            flex-direction: column;
            color: #333;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            margin: 80px auto 30px; /* Adjust top margin to account for fixed header */
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 600px;
        }

        h2 {
            font-family: 'Playfair Display', serif;
            color: #8C5356; /* Logo text color */
            text-align: center;
            margin-bottom: 25px;
            font-size: 2em;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-size: 0.9em;
            font-weight: 500;
        }

        input[type="text"],
        button[type="submit"] {
            width: 100%; /* Make them the same width */
            padding: 10px;
            border-radius: 4px;
            font-size: 0.9em;
            box-sizing: border-box;
            outline: none;
        }

        input[type="text"] {
            border: 1px solid #ddd;
            margin-bottom: 10px; /* Add some space between input and button */
        }

        input[type="text"]:focus {
            border-color: #8C5356; /* Darker accent color */
            box-shadow: 0 0 5px rgba(140, 83, 86, 0.3);
        }

        button[type="submit"] {
             background-color: #8C5356; /* Button background color */
            color: white;
            border: none;
            padding: 15px 25px;
            border-radius: 30px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 1.1em;
            font-family: 'Roboto', sans-serif; /* Consistent font */
            outline: none; /* Remove default focus outline */
        }

        button[type="submit"]:hover {
            background-color: #734043; /* Even darker accent for hover */
        }

        #message {
            margin-top: 10px;
            font-size: 0.9em;
        }
        .error { color: red; }
        .success { color: green; }

        .back-link {
             display: block;
            margin-top: 20px;
            text-align: center;
            color: #8C5356; /* Accent color */
            text-decoration: none;
            font-family: 'Roboto', sans-serif; /* Consistent font */
        }

        .back-link:hover {
            color: #734043; /* Even darker accent for link hover */
        }

        .header {
            background-color: #fff;
            padding: 15px 20px; /* Slightly smaller padding */
            text-align: left;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 101;
        }


    </style>
</head>
<body>

    <div class="container">
        <h2>Add new category</h2>
        <div id="message"></div>
        <form id="addCategoryForm">
            <div class="form-group">
                <label for="new_category_name">Nom de la Nouvelle Catégorie:</label>
                <input type="text" id="new_category_name" name="new_category_name" required>
                <button type="submit">Add category</button>
            </div>
        </form>
        <a href="./index.php" class="back-link">back to the dashboard</a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addCategoryForm = document.getElementById('addCategoryForm');
            const messageDiv = document.getElementById('message');

            addCategoryForm.addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent the default form submission

                const formData = new FormData(addCategoryForm);

                fetch('handle_add_category.php', { // Submit to your PHP script
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json()) // Parse the JSON response
                .then(data => {
                    messageDiv.textContent = data.message;
                    messageDiv.className = data.status; // Apply 'success' or 'error' class

                    if (data.status === 'success' && window.opener && !window.opener.closed) {
                        // Send the new category details to the opener window (add_product.php)
                        window.opener.postMessage({
                            type: 'newCategoryAdded',
                            name: data.new_category,
                            value: data.new_category_value
                        }, '*'); // '*' allows messages from any origin (for simplicity)
                        addCategoryForm.reset(); // Clear the form after successful addition
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    messageDiv.textContent = 'Une erreur s\'est produite lors de la communication avec le serveur.';
                    messageDiv.className = 'error';
                });
            });
        });
    </script>
</body>
</html>