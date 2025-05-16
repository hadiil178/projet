<?php
// Configuration for database connection
$host = 'localhost'; // Or 127.0.0.1
$dbname = 'projet'; // Database name
$username = 'root'; // Default username for XAMPP
$password = ''; // Default password for XAMPP

try {
    // Attempt to establish a database connection using PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Set PDO error mode to exception, which will throw exceptions for errors
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Catch any exceptions thrown during connection attempt
    die("Database connection failed: " . $e->getMessage()); // Stop script execution and display error message
}

// Interface for Statistics data
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics Interface</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Roboto', sans-serif; /* Consistent font */
            background-color: #FAF6F4; /* Main background color */
            margin: 20px;
            padding: 0;
            display: flex;
            flex-direction: column; /* Arrange items vertically */
            align-items: center;
            min-height: 100vh;
            color: #333; /* Default text color */
        }

        .data-container { /* New container for the table with scroll */
            background-color: #fff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 900px; /* Increased max-width */
            text-align: center;
            margin-bottom: 20px; /* Increased margin */
            position: relative; /* For positioning scroll arrows */
            max-height: 400px; /* Set a maximum height for the data container */
            overflow-y: auto; /* Enable vertical scrolling */
            height: auto !important;
            height: 400px;
        }

        .back-link-container { /* Container for the back link */
            background-color: #fff;
            padding: 15px 20px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            width: 90%;
            max-width: 900px;
            text-align: center;
            margin-bottom: 30px; /* Increased margin */
        }

        h1 {
            font-family: 'Playfair Display', serif; /* More elegant heading font */
            color: #8C5356; /* Logo text color */
            margin-bottom: 25px;
            font-size: 2.8em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: #f5f5f0;
            color: #555; /* Label color */
            font-weight: 500;
            font-size: 1.1em;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        p {
            font-size: 1.1em;
            color: #333;
            margin-bottom: 15px;
        }

        a {
            color: #8C5356; /* Accent color */
            text-decoration: none;
            transition: color 0.3s ease;
            font-weight: bold;
        }

        a:hover {
            color: #734043; /* Darker accent for hover */
            text-decoration: underline;
        }

        .chart-container {
            background-color: #fff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            width: 90%;
            max-width: 900px;
            margin-top: 20px; /* Increased top margin */
            margin-bottom: 30px;
            display: flex; /* Arrange charts horizontally */
            justify-content: space-around; /* Space out the charts */
            align-items: flex-start; /* Align items at the top */
            flex-wrap: wrap; /* Allow charts to wrap on smaller screens */
        }

        .chart-wrapper {
            width: calc(50% - 20px); /* Adjust width for two charts side by side */
            min-width: 350px; /* Minimum width for each chart */
            margin-bottom: 25px;
        }

        .chart-wrapper h2 {
            color: #8C5356; /* Accent color */
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 1.8em;
            text-align: center;
        }

        .scroll-arrow {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            background-color: #f0f0f0;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            opacity: 0.3;
            transition: opacity 0.3s ease, background-color 0.3s ease;
            font-size: 1em;
            width: 40px;
            text-align: center;
            color: #555;
        }

        .scroll-arrow:hover {
            opacity: 0.7;
            background-color: #e0e0e0;
        }

        .scroll-up {
            top: 15px; /* Adjust position as needed */
            margin-bottom: 10px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }

        .scroll-down {
            bottom: 15px; /* Adjust position as needed */
            margin-top: 10px;
            box-shadow: 0px -2px 5px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .chart-wrapper {
                width: 100%; /* Full width for single chart on smaller screens */
                min-width: auto;
            }
        }
    </style>
    <script>
        function scrollContent(containerId, direction) {
            const container = document.getElementById(containerId);
            const scrollAmount = 80;

            if (direction === 'up') {
                container.scrollBy({ top: -scrollAmount, behavior: 'smooth' });
            } else {
                container.scrollBy({ top: scrollAmount, behavior: 'smooth' });
            }
        }
    </script>
</head>
<body>
    <div class="data-container" id="product_stock_container">
        <h1>Product Stock</h1>
        <button type="button" class="scroll-arrow scroll-up" onclick="scrollContent('product_stock_container', 'up')">&#8593;</button>
        <?php
        // SQL query to retrieve the stock of each product
        $query_product_stock = "SELECT nom, stock FROM produits";

        try {
            $stmt_product_stock = $pdo->query($query_product_stock);
            $product_stock_data = $stmt_product_stock->fetchAll(PDO::FETCH_ASSOC);

            if ($product_stock_data && count($product_stock_data) > 0) {
                echo "<table>
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Stock Quantity</th>
                                </tr>
                            </thead>
                            <tbody>";
                foreach ($product_stock_data as $row) {
                    echo "<tr>
                                    <td>" . htmlspecialchars($row['nom']) . "</td>
                                    <td>" . htmlspecialchars($row['stock']) . "</td>
                                </tr>";
                }
                echo "</tbody>
                        </table>";
            } else {
                echo "<p>No product stock information available.</p>";
            }
        } catch (PDOException $e) {
            echo "Error retrieving product stock information: " . $e->getMessage();
        }
        ?>
        <button type="button" class="scroll-arrow scroll-down" onclick="scrollContent('product_stock_container', 'down')">&#8595;</button>
    </div>

    <div class="back-link-container">
        <p><a href="index.php">Back to Main Page</a></p>
    </div>

    <div class="chart-container">
        <div class="chart-wrapper">
            <h2>Stock Level</h2>
            <canvas id="stockChart"></canvas>
            <?php
            // SQL query to get stock data per product
            $queryStock = "SELECT nom, stock FROM produits";
            try {
                $stmtStock = $pdo->query($queryStock);
                $stockData = $stmtStock->fetchAll(PDO::FETCH_ASSOC);

                $productNames = array_column($stockData, 'nom');
                $stockValues = array_column($stockData, 'stock');

                ?>
                <script>
                    const stockChartCanvas = document.getElementById('stockChart');
                    const stockChart = new Chart(stockChartCanvas, {
                        type: 'doughnut',
                        data: {
                            labels: <?php echo json_encode($productNames); ?>,
                            datasets: [{
                                label: 'Stock Quantity',
                                data: <?php echo json_encode($stockValues); ?>,
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.7)',
                                    'rgba(54, 162, 235, 0.7)',
                                    'rgba(255, 206, 86, 0.7)',
                                    'rgba(75, 192, 192, 0.7)',
                                    'rgba(153, 102, 255, 0.7)',
                                    'rgba(255, 159, 64, 0.7)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        font: {
                                            family: 'Roboto',
                                            size: 12
                                        }
                                    }
                                }
                            }
                        }
                    });
                </script>
                <?php
            } catch (PDOException $e) {
                echo "<p>Error retrieving stock data for chart: " . $e->getMessage() . "</p>";
            }
            ?>
        </div>

        <div class="chart-wrapper">
            <h2>Total Revenue</h2>
            <canvas id="revenueChart"></canvas>
            <?php
            // SQL query to get revenue data from the statistics table
            $queryRevenue = "SELECT revenu_total FROM statistics WHERE stat_id = 1";
            try {
                $stmtRevenue = $pdo->query($queryRevenue);
                $revenueData = $stmtRevenue->fetch(PDO::FETCH_ASSOC);
                $revenueValue = $revenueData['revenu_total'] ?? 0; // Use null coalescing operator

                ?>
                <script>
                    const revenueChartCanvas = document.getElementById('revenueChart');
                    const revenueChart = new Chart(revenueChartCanvas, {
                        type: 'bar',
                        data: {
                            labels: ['Total Revenue'],
                            datasets: [{
                                label: 'Revenue (DA)',
                                data: [<?php echo json_encode($revenueValue); ?>],
                                backgroundColor: ['rgba(140, 83, 86, 0.8)'], // Updated color
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false // Hide the legend for single bar
                                },
                                title: {
                                    display: true,
                                    text: 'Total Revenue (DA)',
                                    font: {
                                        family: 'Roboto',
                                        size: 14
                                    }
                                }
                            }
                        }
                    });
                </script>
                <?php
            } catch (PDOException $e) {
                echo "<p>Error retrieving revenue data: " . $e->getMessage() . "</p>";
            }
            ?>
        </div>

        <div class="chart-wrapper">
            <h2>Total Sales</h2>
            <canvas id="salesChart"></canvas>
            <?php
            // SQL query to get sales data from the statistics table
            $querySales = "SELECT les_ventes_totals FROM statistics WHERE stat_id = 1";
            try {
                $stmtSales = $pdo->query($querySales);
                $salesData = $stmtSales->fetch(PDO::FETCH_ASSOC);
                $salesValue = $salesData['les_ventes_totals'] ?? 0; // Use null coalescing operator
                ?>
                <script>
                    const salesChartCanvas = document.getElementById('salesChart');
                    const salesChart = new Chart(salesChartCanvas, {
                        type: 'line',
                        data: {
                            labels: ['Total Sales'],
                            datasets: [{
                                label: 'Sales',
                                data: [<?php echo json_encode($salesValue); ?>],
                                borderColor: 'rgba(0, 123, 255, 0.8)',  // Blue
                                borderWidth: 2,
                                fill: false
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false // Hide legend for single line
                                },
                                title: {
                                    display: true,
                                    text: 'Total Sales',
                                    font: {
                                        family: 'Roboto',
                                        size: 14
                                    }
                                }
                            }
                        }
                    });
                </script>
                <?php
            } catch (PDOException $e) {
                echo "<p>Error retrieving sales data: " . $e->getMessage() . "</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>