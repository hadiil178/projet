<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the search query is set
    if (isset($_GET['query'])) {
        $search_query = trim($_GET['query']);

        // Ensure the query is not empty
        if (!empty($search_query)) {
            // Prepare the SQL query to search for products
            $sql = "SELECT * FROM produits WHERE nom LIKE :query OR description LIKE :query";
            $stmt = $conn->prepare($sql);

            // Bind the search query parameter
            $query_param = "%" . $search_query . "%";
            $stmt->bindParam(':query', $query_param, PDO::PARAM_STR);

            // Execute the query
            $stmt->execute();

            // Fetch the search results
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Return the results as JSON
            header('Content-Type: application/json');
            echo json_encode($results);
        } else {
            // If the search query is empty, return an empty JSON array
            header('Content-Type: application/json');
            echo json_encode([]);
        }
    } else {
        // If the 'query' parameter is not set, return an error
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Search query parameter is missing.']);
    }

} catch(PDOException $e) {
    // Handle database connection errors
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} finally {
    // Close the database connection
    $conn = null;
}
?>