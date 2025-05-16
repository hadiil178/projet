<?php
// Database connection (as in index.php)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all customers
    $stmt = $conn->prepare("SELECT * FROM customers ORDER BY registration_date DESC");
    $stmt->execute();
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    echo "Erreur de connexion à la base de données: " . $e->getMessage();
    $customers = [];
}

// Function to safely display HTML.
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Customers</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #FAF6F4;
            margin: 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            color: #333;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 1200px;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            color: #8C5356;
            text-align: center;
            margin-bottom: 25px;
            font-size: 2.5em;
        }

        table {
            width: 100%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
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
            color: #555;
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

        .actions {
            text-align: center;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .actions a {
            text-decoration: none;
            color: #8C5356;
            font-weight: bold;
            transition: color 0.3s ease;
            font-size: 0.95em;
            padding: 8px 12px;
            border-radius: 5px;
            border: 1px solid #8C5356;
        }

        .actions a:hover {
            color: #734043;
            text-decoration: underline;
        }

        .actions a:hover {
            color: #734043;
            background-color: #f8f0fb;
        }


        .delete-btn {
            background-color: #DC143C;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 0.9em;
        }

        .delete-btn:hover {
            background-color: #B22222;
        }

        .back-to-index {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #8C5356;
            text-decoration: none;
            transition: color 0.3s ease;
            font-weight: bold;
            font-size: 1.1em;
        }

        .back-to-index:hover {
            color: #734043;
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
        <h1>Customers</h1>
        <?php if (!empty($customers)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Registration Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td><?php echo h($customer['id']); ?></td>
                            <td><?php echo h($customer['first_name']); ?></td>
                            <td><?php echo h($customer['last_name']); ?></td>
                            <td><?php echo h($customer['email']); ?></td>
                            <td><?php echo h($customer['registration_date']); ?></td>
                            <td class="actions">
                                <a href="view_customer_profile.php?id=<?php echo h($customer['id']); ?>">View Profile</a>
                                <button class="delete-btn" onclick="deleteCustomer(<?php echo h($customer['id']); ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No customers found.</p>
        <?php endif; ?>
        <a href="index.php" class="back-to-index">Back to Home</a>
    </div>

    <script>
    function deleteCustomer(customerId) {
        if (confirm("Are you sure you want to delete this customer?")) {
            // Use AJAX to send a DELETE request to the server
            fetch('delete_customer.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id=' + customerId,
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to delete customer.');
                }
                return response.text();
            })
            .then(data => {
                if (data === "success") {
                    // Remove the customer row from the table
                    const row = document.querySelector(`[onclick="deleteCustomer(${customerId})"]`).parentNode.parentNode;
                    row.remove();
                    alert("Customer deleted successfully.");
                    //check if the table is empty
                    const customerTable = document.querySelector("table tbody");
                    if (customerTable.rows.length === 0) {
                        document.querySelector("table").style.display = "none";
                        document.querySelector("p").innerHTML = "No customers found.";
                    }
                } else {
                    alert("Error deleting customer: " + data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("An error occurred while deleting the customer.");
            });
        }
    }
    </script>
</body>
</html>
<?php $conn = null; ?>
