<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if customer ID is provided
    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
        $customer_id = $_POST['id'];

        // Delete the customer from the database
        $stmt = $conn->prepare("DELETE FROM customers WHERE id = :id");
        $stmt->bindParam(':id', $customer_id, PDO::PARAM_INT);
        $stmt->execute();

        // Check if the deletion was successful
        if ($stmt->rowCount() > 0) {
            echo "success"; // Send a success message back to the client
        } else {
            echo "Customer not found or already deleted."; // Send an error message
        }
    } else {
        echo "Invalid customer ID."; // Send an error message
    }

} catch (PDOException $e) {
        echo "Database error: " . $e->getMessage(); // Send a database error message
}

$conn = null; // Close the database connection
?>
