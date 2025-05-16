<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet";

try {
    $conn = new PDO('mysql:host=' . $servername . ';dbname=' . $dbname, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Determine the active category from the URL or default to 'all'
    $active_category = isset($_GET['category']) ? $_GET['category'] : 'all';

    // Handle search query
    $search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
    $search_condition = '';
    if (!empty($search_term)) {
        // Use the LOWER() function to perform a case-insensitive comparison
        $search_condition = " AND LOWER(nom) LIKE LOWER(:search)";
    }

    // Fetch products based on the active category and search term
    if ($active_category === 'all') {
        $stmt = $conn->prepare("SELECT * FROM produits WHERE 1" . $search_condition);
        if (!empty($search_term)) {
            $stmt->bindValue(':search', '%' . $search_term . '%', PDO::PARAM_STR);
        }
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt = $conn->prepare("SELECT * FROM produits WHERE category = :category" . $search_condition);
        $stmt->bindParam(':category', $active_category);
        if (!empty($search_term)) {
            // Use the LOWER() function here as well
            $stmt->bindValue(':search', '%' . $search_term . '%', PDO::PARAM_STR);
        }
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch all unique categories for the sidebar menu
    $categories_stmt = $conn->query("SELECT DISTINCT category FROM produits ORDER BY category");
    $all_categories = $categories_stmt->fetchAll(PDO::FETCH_COLUMN);

    // --- Dynamic Banner Data (Example - Replace with your actual data fetching) ---
    $dynamic_banners = [
        [
            'image_path' => 'path/to/your/banner1.jpg',
            'alt_text' => 'Exclusive Offer',
            'link' => '#'
        ],
        [
            'image_path' => 'path/to/your/banner2.jpg',
            'alt_text' => 'New Arrivals',
            'link' => '#'
        ],
        [
            'image_path' => 'path/to/your/banner3.jpg',
            'alt_text' => 'Limited Time Sale',
            'link' => '#'
        ]
    ];
    // --------------------------------------------------------------------------

} catch(PDOException $e) {
    echo "Database Connection Error: " . $e->getMessage();
    $products = [];
    $all_categories = [];
    $dynamic_banners = []; // Initialize even on error
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elegance - Discover Your Style</title>
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

    .logo {
        font-family: 'Great Vibes', cursive;
        font-size: 2.4em; /* Slightly smaller logo */
        font-weight: bold;
        color: #8C5356; /* Darker accent color */
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
    }

    .menu-button {
        font-size: 22px; /* Slightly smaller menu icon */
        cursor: pointer;
        padding: 6px; /* Slightly smaller padding */
        border: none;
        background: none;
        outline: none;
        display: flex;
        align-items: center;
        color: #777;
        transition: color 0.3s ease;
    }

    .menu-button:hover {
        color: #8C5356;
    }

    .menu-icon {
        transition: transform 0.3s ease-in-out;
    }

    .menu-icon.open {
        transform: rotate(90deg);
    }

    .sidebar {
        position: fixed;
        top: 0;
        left: -250px; /* Slightly smaller width */
        width: 250px; /* Slightly smaller width */
        height: 100%;
        background-color: #fff;
        border-right: 1px solid #f0f0f0;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
        padding-top: 60px; /* Adjusted padding */
        transition: left 0.3s ease-in-out;
        z-index: 100;
        overflow-y: auto;
    }

    .sidebar.open {
        left: 0;
    }

    .sidebar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 20px; /* Slightly smaller padding */
        border-bottom: 1px solid #f0f0f0;
    }

    .sidebar-title {
        font-weight: 500;
        color: #555;
        font-size: 1em; /* Smaller title */
    }

    .close-button {
        font-size: 1.2em; /* Smaller close button */
        cursor: pointer;
        background: none;
        border: none;
        outline: none;
        color: #999;
        transition: color 0.3s ease;
    }

    .close-button:hover {
        color: #555;
    }

    .sidebar a {
        display: block;
        padding: 14px 20px; /* Smaller padding for links */
        text-decoration: none;
        color: #555;
        transition: background-color 0.3s ease, color 0.3s ease;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.9em; /* Smaller font size for links */
    }

    .sidebar a:last-child {
        border-bottom: none;
    }

    .sidebar a:hover {
        background-color: #F5CACB; /* Lighter accent for hover */
        color: #8C5356;
    }

    .main-content {
        flex-grow: 1;
        padding-top: 110px; /* Adjusted padding to accommodate header */
        width: 100%;
        padding-bottom: 70px; /* Adjusted padding */
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
    }

    /* --- Dynamic Banner Styles --- */
    .dynamic-banner-container {
        width: 90%;
        max-width: 1200px;
        margin: 15px auto 0; /* Reduced top margin slightly */
        overflow: hidden;
        position: relative;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
    }

    .dynamic-banner-slide {
        display: none; /* Initially hide all slides */
        width: 100%;
        animation: slide-animation 15s infinite; /* Adjust duration as needed */
    }

    .dynamic-banner-slide a {
        display: block; /* Make the link cover the entire slide */
    }

    .dynamic-banner-slide img {
        width: 100%;
        display: block;
        height: auto; /* Maintain aspect ratio */
    }

    /* Keyframes for the sliding animation */
    @keyframes slide-animation {
        0% { opacity: 0; transform: translateX(100%); }
        10% { opacity: 1; transform: translateX(0%); }
        30% { opacity: 1; transform: translateX(0%); }
        40% { opacity: 0; transform: translateX(-100%); }
        100% { opacity: 0; transform: translateX(-100%); }
    }

    /* Target specific slides to control timing */
    <?php if (isset($dynamic_banners) && is_array($dynamic_banners)): ?>
    <?php foreach (array_keys($dynamic_banners) as $index): ?>
    .dynamic-banner-slide:nth-child(<?php echo $index + 1; ?>) {
        animation-delay: <?php echo $index * 5; ?>s; /* Adjust delay to match slide duration */
    }
    <?php endforeach; ?>
    <?php endif; ?>
    /* -------------------------- */

    .search-bar-container {
    background-color: #fff;
    padding: 12px 20px;
    margin-bottom: 12px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    width: 90%;
    max-width: 1200px;
    display: flex;
    justify-content: center;
    /* Dramatically increased negative margin-top */
    margin-top: -40px; /* Try this value, adjust as needed */
    margin-left: auto;
    margin-right: auto;
}

    .search-form {
        display: flex;
        width: 70%;
        max-width: 600px;
        border-radius: 20px; /* Smaller border radius */
        overflow: hidden; /* To contain rounded borders */
        border: 1px solid #ddd;
        background-color: #f9f9f9;
    }

    .search-bar {
        padding: 10px 15px; /* Smaller padding */
        border: none;
        outline: none;
        font-size: 0.9em; /* Smaller font size */
        color: #333;
        background-color: transparent;
        flex-grow: 1;

    }

    .search-button {
        background-color: #8C5356; /* Darker accent color */
        color: white;
        border: none;
        padding: 10px 20px; /* Smaller padding */
        font-size: 0.9em; /* Smaller font size */
        cursor: pointer;
        transition: background-color 0.3s ease;
        outline: none;
        border-radius: 0 20px 20px 0; /* Smaller border radius */
    }

    .search-button:hover {
        background-color: #734043; /* Even darker accent for hover */
    }

    .category-navigation {
        background-color: #fff;
        padding: 8px 20px; /* Smaller padding */
        margin-bottom: 12px; /* Reduced bottom margin */
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        width: 90%;
        max-width: 1200px;
    }

    .category-navigation ul {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        gap: 10px; /* Reduced gap */
        justify-content: center;
        flex-wrap: wrap; /* Allow categories to wrap on smaller screens */
    }

    .category-navigation ul li a {
        text-decoration: none;
        color: #555;
        font-weight: 500;
        transition: color 0.3s ease, background-color 0.3s ease;
        padding: 6px 10px; /* Reduced padding */
        border-radius: 15px; /* Smaller border radius */
        display: block; /* Make the entire link clickable */
        font-size: 0.85em; /* Slightly smaller font */
    }

    .category-navigation ul li a:hover {
        color: #8C5356;
        background-color: #F5CACB;
    }

    .category-navigation ul li a.active {
        color: #fff;
        background-color: #8C5356;
    }

    .product-category {
        margin: 12px auto; /* Reduced top margin to close the gap */
        width: 90%;
        max-width: 1200px;
        background-color: #fff;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        border-radius: 10px; /* Smaller border radius */
        padding: 15px; /* Reduced padding */
    }

    .category-title {
        text-align: center;
        color: #555;
        font-size: 1.8em; /* Smaller title */
        margin-bottom: 12px; /* Slightly reduced bottom margin for the title */
        font-family: 'Playfair Display', serif;
        font-style: italic;
        font-weight: 400;
    }

    .carousel-container {
        position: relative;
        overflow: hidden;
    }

    .product-list {
        display: flex;
        flex-wrap: nowrap;
        gap: 10px; /* Reduced gap */
        padding-bottom: 10px; /* Reduced padding */
        transition: transform 0.4s ease-in-out;
    }

    .product-item {
        width: 160px; /* Reduced width */
        flex-shrink: 0;
        text-align: center;
        padding: 10px; /* Reduced padding */
        border: 1px solid #f0f0f0;
        border-radius: 8px; /* Smaller border radius */
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        background-color: #f9f9f9; /* Light background for product items */
    }

    .product-item:hover {
        transform: translateY(-3px); /* Reduced lift */
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1); /* Reduced shadow */
    }

    .product-image {
        width: 100%;
        height: auto;
        max-height: 120px; /* Reduced max height */
        object-fit: cover;
        border-radius: 6px; /* Smaller border radius */
        margin-bottom: 8px; /* Reduced margin */
    }

    .product-name {
        font-weight: 500;
        color: #333;
        margin-bottom: 4px; /* Reduced margin */
        font-size: 0.8em; /* Smaller font size */
    }

    .price {
        color: #8C5356; /* Darker accent for price */
        font-style: italic;
        font-size: 0.9em; /* Smaller font size */
    }

    .carousel-button {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background-color: rgba(255, 255, 255, 0.7);
        border: none;
        font-size: 1em; /* Reduced size */
        color: #8C5356;
        cursor: pointer;
        opacity: 0.8;
        transition: opacity 0.3s ease, background-color 0.3s ease;
        width: 25px; /* Reduced width */
        height: 25px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10;
        outline: none;
    }

    .carousel-button:hover {
        opacity: 1;
        background-color: rgba(255, 255, 255, 0.9);
    }

    .carousel-button.prev {
        left: 5px; /* Reduced position */
    }
    .carousel-button.next {
        right: 5px; /* Reduced position */
    }

    a {
        text-decoration: none;
        color: inherit;
        transition: color 0.3s ease;
    }

    a:hover {
        color: #734043; /* Even darker accent for link hover */
    }

    .deconnexion-link {
        padding: 0 15px; /* Smaller padding */
    }

    .deconnexion-link a {
        background-color: #d32f2f;
        color: white;
        padding: 6px 10px; /* Reduced padding */
        border-radius: 15px; /* Smaller border radius */
        transition: background-color 0.3s ease;
        font-size: 0.75em; /* Reduced font size */
    }

    .deconnexion-link a:hover {
        background-color: #b71c1c;
    }

    .footer {
        background-color: #fff;
        text-align: center;
        padding: 12px 0; /* Reduced padding */
        position: fixed;
        bottom: 0;
        width: 100%;
        box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.05);
        font-size: 0.75em; /* Reduced font size */
        color: #777;
    }

    .no-products {
        text-align: center;
        color: #777;
        padding: 15px; /* Reduced padding */
        font-size: 0.9em; /* Smaller font size */
    }
</style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuButton = document.querySelector('.menu-button');
            const menuIcon = document.querySelector('.menu-icon');
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            const closeButton = document.querySelector('.close-button');
            const searchFormContainer = document.querySelector('.search-bar-container');
            const searchForm = document.querySelector('.search-form');
            const searchInput = document.querySelector('.search-bar');

            menuButton.addEventListener('click', function() {
                sidebar.classList.toggle('open');
                menuIcon.classList.toggle('open');
            });

            closeButton.addEventListener('click', function() {
                sidebar.classList.remove('open');
                menuIcon.classList.remove('open');
            });

            mainContent.addEventListener('click', function(event) {
                if (sidebar.classList.contains('open') && !event.target.closest('.sidebar') && event.target !== menuButton) {
                    sidebar.classList.remove('open');
                    menuIcon.classList.remove('open');
                }
            });

            const productCategories = document.querySelectorAll('.product-category');

            productCategories.forEach(category => {
                const carouselContainer = category.querySelector('.carousel-container');
                const productList = category.querySelector('.product-list');

                if (productList && carouselContainer) {
                    const prevButton = document.createElement('button');
                    const nextButton = document.createElement('button');

                    prevButton.classList.add('carousel-button', 'prev');
                    prevButton.innerHTML = '<i class="fas fa-chevron-left"></i>';
                    nextButton.classList.add('carousel-button', 'next');
                    nextButton.innerHTML = '<i class="fas fa-chevron-right"></i>';

                    carouselContainer.appendChild(prevButton);
                    carouselContainer.appendChild(nextButton);

                    let scrollAmount = 0;
                    const step = 195; // Adjust based on product item width + gap

                    nextButton.addEventListener('click', () => {
                        scrollAmount += step;
                        productList.style.transform = `translateX(-${scrollAmount}px)`;
                        // Basic check to disable button if end is reached (can be improved)
                        if (scrollAmount > productList.scrollWidth - carouselContainer.offsetWidth) {
                            scrollAmount = productList.scrollWidth - carouselContainer.offsetWidth;
                            productList.style.transform = `translateX(-${scrollAmount}px)`;
                        }
                    });

                    prevButton.addEventListener('click', () => {
                        scrollAmount -= step;
                        productList.style.transform = `translateX(-${scrollAmount}px)`;
                        if (scrollAmount < 0) {
                            scrollAmount = 0;
                            productList.style.transform = `translateX(0px)`;
                        }
                    });
                }
            });

            if (searchForm && searchInput) {
                searchForm.addEventListener('submit', function(event) {
                    if (!searchInput.value.trim()) {
                        event.preventDefault(); // Prevent submitting empty search
                        window.location.href = 'index.php'; // Redirect to show all if search is empty
                    }
                });
            }
        });
    </script>
</head>
<body>
    <div class="header">
        <button class="menu-button">
            <i class="fas fa-bars menu-icon"></i>
        </button>
        <div class="logo">Elegance</div>
        <div class="deconnexion-link"><a href="login.php">Logout</a></div>
    </div>

    <div class="sidebar">
        <div class="sidebar-header">
            <h3 class="sidebar-title">Admin Menu</h3>
            <button class="close-button">&times;</button>
        </div>
    
        <a href="./add_product.php?category=perfume">Add Product</a>
        <a href="./manage_products.php">Manage Products</a>
        <a href="./ajoutercategory.html">Add Category</a>
        <a href="./manage_categories.php">Manage Categories</a>
        <a href="./orders.php">Orders</a>
        <a href="./custumer.php">Custumers</a>
        <a href="./apply_promotion.php">Apply Promotion</a>

        


        
    </div>

    <div class="main-content">
        <div class="dynamic-banner-container">
            <?php if (isset($dynamic_banners) && is_array($dynamic_banners)): ?>
                <?php foreach ($dynamic_banners as $banner): ?>
                    <div class="dynamic-banner-slide">
                        <a href="<?php echo htmlspecialchars($banner['link']); ?>">
                            <img src="<?php echo htmlspecialchars($banner['image_path']); ?>" alt="<?php echo htmlspecialchars($banner['alt_text']); ?>">
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="search-bar-container">
            <form class="search-form" action="index.php" method="get">
                <input type="text" class="search-bar" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search_term); ?>">
                <button type="submit" class="search-button">Search</button>
                <?php if (isset($_GET['category'])): ?>
                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($_GET['category']); ?>">
                <?php endif; ?>
            </form>
        </div>

        <div class="category-navigation">
            <ul>
                <li><a href="index.php<?php if (!empty($search_term)) echo '?search=' . htmlspecialchars($search_term); ?>" class="<?php if ($active_category === 'all') echo 'active'; ?>">All</a></li>
                <li><a href="index.php?category=perfume<?php if (!empty($search_term)) echo '&search=' . htmlspecialchars($search_term); ?>" class="<?php if ($active_category === 'perfume') echo 'active'; ?>">Perfume</a></li>
                <li><a href="index.php?category=skincare<?php if (!empty($search_term)) echo '&search=' . htmlspecialchars($search_term); ?>" class="<?php if ($active_category === 'skincare') echo 'active'; ?>">Skincare</a></li>
                <li><a href="index.php?category=makeup<?php if (!empty($search_term)) echo '&search=' . htmlspecialchars($search_term); ?>" class="<?php if ($active_category === 'makeup') echo 'active'; ?>">Makeup</a></li>
                <?php foreach ($all_categories as $cat): ?>
                    <?php if (!in_array($cat, ['perfume', 'skincare', 'makeup'])): ?>
                        <li><a href="index.php?category=<?php echo htmlspecialchars($cat); ?><?php if (!empty($search_term)) echo '&search=' . htmlspecialchars($search_term); ?>" class="<?php if ($active_category === $cat) echo 'active'; ?>"><?php echo htmlspecialchars(ucfirst($cat)); ?></a></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>

        <?php if (empty($products) && (!empty($active_category) && $active_category !== 'all') && empty($search_term)): ?>
            <div class="product-category">
                <p class="no-products">No products available in the <?php echo htmlspecialchars(ucfirst($active_category)); ?> category.</p>
            </div>
        <?php elseif (empty($products) && !empty($search_term)): ?>
            <div class="product-category">
                <p class="no-products">No products found matching your search term: "<?php echo htmlspecialchars($search_term); ?>".</p>
            </div>
        <?php elseif (!empty($products)): ?>
            <div class="product-category">
                <?php
                $category_title = 'Our Exquisite Collection';
                if ($active_category === 'perfume') {
                    $category_title = 'The Art of Fragrance';
                } elseif ($active_category === 'skincare') {
                    $category_title = 'Secrets of the Skin';
                } elseif ($active_category === 'makeup') {
                    $category_title = 'Touches of Glamour';
                } elseif ($active_category !== 'all') {
                    $category_title = htmlspecialchars(ucfirst($active_category));
                }
                if (!empty($search_term)) {
                    $category_title = 'Search Results for: "' . htmlspecialchars($search_term) . '"';
                }
                ?>
                <h2 class="category-title"><?php echo $category_title; ?></h2>
                <div class="carousel-container">
                    <div class="product-list">
                        <?php foreach ($products as $product): ?>
                            <div class="product-item">
                                <a href="product_details.php?id=<?php echo $product['id']; ?>">
                                    <img class="product-image" src="<?php echo $product['image_path']; ?>" alt="<?php echo htmlspecialchars($product['nom']); ?>">
                                    <div class="product-name"><?php echo htmlspecialchars($product['nom']); ?></div>
                                    <div class="price"><?php echo htmlspecialchars($product['prix']); ?> DA</div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="product-category">
                <p class="no-products">No products available.</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="footer">
        &copy; 2025 Elegance. All rights reserved.
    </div>
</body>
</html>
<?php $conn = null; ?>