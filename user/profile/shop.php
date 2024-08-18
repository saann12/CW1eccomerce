<?php
// Start the session to get user info
session_start();

// Include the database connection file
include '../../include/db.php';

// Ensure user is logged in
if (!isset($_SESSION['phone'])) {
    header("Location: login.php");
    exit();
}

// Get user phone number from session
$user_phone = $_SESSION['phone'];

// Fetch user information from the database
$sql = "SELECT id, fullname, email, phone, privilege FROM user_reg WHERE phone = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $user_phone); // Bind as string since phone numbers are usually strings
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}

// Initialize variables for filtering
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : [];
$priceFilter = isset($_GET['price']) ? intval($_GET['price']) : 500;
$sizeFilter = isset($_GET['size']) ? $_GET['size'] : [];
$colorFilter = isset($_GET['color']) ? $_GET['color'] : [];

// Build the SQL query with filters
$sql = "SELECT * FROM product WHERE price <= ?";

// Prepare arrays for binding parameters
$filterParams = [$priceFilter];
$filterTypes = 'i'; // 'i' for integer price filter

// Add category filter
if (!empty($categoryFilter)) {
    $categoryPlaceholders = implode(',', array_fill(0, count($categoryFilter), '?'));
    $sql .= " AND categories IN ($categoryPlaceholders)";
    $filterParams = array_merge($filterParams, $categoryFilter);
    $filterTypes .= str_repeat('s', count($categoryFilter)); // 's' for string categories
}

// Add size filter
if (!empty($sizeFilter)) {
    $sizePlaceholders = implode(',', array_fill(0, count($sizeFilter), '?'));
    $sql .= " AND size IN ($sizePlaceholders)";
    $filterParams = array_merge($filterParams, $sizeFilter);
    $filterTypes .= str_repeat('s', count($sizeFilter)); // 's' for string sizes
}

// Add color filter
if (!empty($colorFilter)) {
    $colorPlaceholders = implode(',', array_fill(0, count($colorFilter), '?'));
    $sql .= " AND color IN ($colorPlaceholders)";
    $filterParams = array_merge($filterParams, $colorFilter);
    $filterTypes .= str_repeat('s', count($colorFilter)); // 's' for string colors
}

// Prepare the statement
$stmt = $conn->prepare($sql);

// Check if preparation was successful
if (!$stmt) {
    die('Prepare failed: ' . $conn->error);
}

// Bind parameters
$stmt->bind_param($filterTypes, ...$filterParams);

// Execute statement
$stmt->execute();

// Get results
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);

// Close connection
$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Nike Store</title>
</head>
<body>
<header>
    <nav id="nav">
        <div class="navItem">
            <a href="index.php"><img class="logo" src="../../img/download.png" alt="HamroSportDokan"></a>
        </div>
        <div class="navLink">
        <ul> </ul>   
        <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="contact.php">Contact us</a></li>
                <li><a href="../../include/logout.php">Logout</a></li>
                <li><a href="profile.php"><?php echo htmlspecialchars($user['fullname']); ?></a></li>
                <li><a href="cart.php"><i class="fas fa-shopping-cart"></i></a></li>
            </ul>

        </div>
    </nav>
</header>
<main>
    <section class="search-filter">
        <div class="filter">
            <h2>Filter Products</h2>
            <form id="filter-form" method="GET" action="shop.php">
                <!-- Category Filter -->
                <div class="filter-group">
                    <h3>Category</h3>
                    <label><input type="checkbox" name="category[]" value="jackets" <?php echo in_array('jackets', $categoryFilter) ? 'checked' : ''; ?>> Jackets</label>
                    <label><input type="checkbox" name="category[]" value="footwear" <?php echo in_array('footwear', $categoryFilter) ? 'checked' : ''; ?>> Footwear</label>
                    <label><input type="checkbox" name="category[]" value="tshirts" <?php echo in_array('tshirts', $categoryFilter) ? 'checked' : ''; ?>> T-shirt</label>
                    <label><input type="checkbox" name="category[]" value="Shorts" <?php echo in_array('Shorts', $categoryFilter) ? 'checked' : ''; ?>> Shorts</label>
                    <label><input type="checkbox" name="category[]" value="football jersey" <?php echo in_array('football jersey', $categoryFilter) ? 'checked' : ''; ?>> Football Jersey</label>
                    <label><input type="checkbox" name="category[]" value="football boots" <?php echo in_array('football boots', $categoryFilter) ? 'checked' : ''; ?>> Football boots</label>
                </div>

                <!-- Price Range Filter -->
                <div class="filter-group">
                    <h3>Price Range</h3>
                    <input type="range" min="0" max="500" step="10" id="price-range" name="price" value="<?php echo htmlspecialchars($priceFilter); ?>">
                    <p>Price: Rs.<span id="price-value"><?php echo htmlspecialchars($priceFilter); ?></span> - Rs.500</p>
                </div>

                <!-- Size Filter -->
                <div class="filter-group">
                    <h3>Size</h3>
                    <label><input type="checkbox" name="size[]" value="s" <?php echo in_array('s', $sizeFilter) ? 'checked' : ''; ?>> Small</label>
                    <label><input type="checkbox" name="size[]" value="m" <?php echo in_array('m', $sizeFilter) ? 'checked' : ''; ?>> Medium</label>
                    <label><input type="checkbox" name="size[]" value="l" <?php echo in_array('l', $sizeFilter) ? 'checked' : ''; ?>> Large</label>
                    <label><input type="checkbox" name="size[]" value="xl" <?php echo in_array('xl', $sizeFilter) ? 'checked' : ''; ?>> Extra Large</label>
                </div>
                <button type="submit" class="filter-btn">Apply Filters</button>
            </form>
        </div>
    </section>

    <section class="cards">
        <?php if ($products): ?>
            <?php foreach ($products as $product): ?>
                <div class="card">
    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
    <div class="card-body">
        <h2 class="card-title"><?php echo htmlspecialchars($product['title']); ?></h2>
        <p class="card-description"><?php echo htmlspecialchars($product['description']); ?></p>
        <div class="price-cart">
            <p class="card-price">Rs.<?php echo htmlspecialchars($product['price']); ?></p>
            <a class="btn" href="cart.php?action=add&id=<?php echo htmlspecialchars($product['id']); ?>"><img class="btn-img" src="../../img/cart1.png" alt="Add to cart"></a>
        </div>
    </div>
</div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No products found.</p>
        <?php endif; ?>
    </section>
</main>

<footer>
    <!-- Footer content -->
</footer>

<script>
    // JavaScript to update price value
    const priceRange = document.getElementById('price-range');
    const priceValue = document.getElementById('price-value');

    priceValue.textContent = priceRange.value;
    priceRange.addEventListener('input', function () {
        priceValue.textContent = this.value;
    });
</script>

</body>
</html>
