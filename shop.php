<?php
include 'include/db.php'; // Include the database connection file

// Initialize variables for filtering
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : [];
$priceFilter = isset($_GET['price']) ? intval($_GET['price']) : 500;
$sizeFilter = isset($_GET['size']) ? $_GET['size'] : [];
$colorFilter = isset($_GET['color']) ? $_GET['color'] : [];

// Build the SQL query with filters
$sql = "SELECT * FROM product WHERE price <= ?";

// Add category filter
if (!empty($categoryFilter)) {
    $categoriesPlaceholder = implode(',', array_fill(0, count($categoryFilter), '?'));
    $sql .= " AND category IN ($categoriesPlaceholder)";
}

// Add size filter (assuming you store sizes in a comma-separated string)
if (!empty($sizeFilter)) {
    $sizesPlaceholder = implode(',', array_fill(0, count($sizeFilter), '?'));
    $sql .= " AND size IN ($sizesPlaceholder)";
}

// Add color filter
if (!empty($colorFilter)) {
    $colorsPlaceholder = implode(',', array_fill(0, count($colorFilter), '?'));
    $sql .= " AND color IN ($colorsPlaceholder)";
}

// Prepare statement
$stmt = $conn->prepare($sql);

// Bind parameters
$params = [$priceFilter];
$types = 'i'; // 'i' for integer price filter

// Bind category filter
if (!empty($categoryFilter)) {
    $types .= str_repeat('s', count($categoryFilter)); // 's' for string categories
    $params = array_merge($params, $categoryFilter);
}

// Bind size filter
if (!empty($sizeFilter)) {
    $types .= str_repeat('s', count($sizeFilter)); // 's' for string sizes
    $params = array_merge($params, $sizeFilter);
}

// Bind color filter
if (!empty($colorFilter)) {
    $types .= str_repeat('s', count($colorFilter)); // 's' for string colors
    $params = array_merge($params, $colorFilter);
}

// Bind parameters to statement
$stmt->bind_param($types, ...$params);

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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@500&family=Nunito&family=Palanquin+Dark:wght@500&family=Roboto+Slab:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./css/style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Nike Store</title>
</head>

<body>
    <header>
        <nav id="nav">
            <div class="navItem">
                <a href="index.html"><img class="logo" src="./img/download.png" alt="HamroSportDokan"></a>
            </div>
            <div class="navLink">
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="shop.php">Shop</a></li>
                    <li><a href="about.html">About us</a></li>
                    <li><a href="contact.html">Contact us</a></li>
                </ul>
                <ul>
                    <li><a href="user/login.php">Login</a></li>
                    <li><a href="user/register.php">Register</a></li>
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

                    <!-- Color Filter -->
                    <div class="filter-group">
                        <h3>Color</h3>
                        <label><input type="checkbox" name="color[]" value="red" <?php echo in_array('red', $colorFilter) ? 'checked' : ''; ?>> Red</label>
                        <label><input type="checkbox" name="color[]" value="blue" <?php echo in_array('blue', $colorFilter) ? 'checked' : ''; ?>> Blue</label>
                        <label><input type="checkbox" name="color[]" value="green" <?php echo in_array('green', $colorFilter) ? 'checked' : ''; ?>> Green</label>
                        <label><input type="checkbox" name="color[]" value="black" <?php echo in_array('black', $colorFilter) ? 'checked' : ''; ?>> Black</label>
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
                                <button class="btn"><img class="btn-img" src="img/cart1.png" alt="cart"></button>
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
        <div class="footerLeft">
            <div class="footerMenu">
                <h1 class="fMenuTitle">About Us</h1>
                <ul class="fList">
                    <li class="fListItem">Company</li>
                    <li class="fListItem">Contact</li>
                    <li class="fListItem">Careers</li>
                    <li class="fListItem">Affiliates</li>
                    <li class="fListItem">Stores</li>
                </ul>
            </div>
            <div class="footerMenu">
                <h1 class="fMenuTitle">Useful Links</h1>
                <ul class="fList">
                    <li class="fListItem">Support</li>
                    <li class="fListItem">Refund</li>
                    <li class="fListItem">FAQ</li>
                    <li class="fListItem">Feedback</li>
                    <li class="fListItem">Stories</li>
                </ul>
            </div>
            <div class="footerMenu">
                <h1 class="fMenuTitle">Shops</h1>
                <ul class="fList">
                    <li class="fListItem">Air Force</li>
                    <li class="fListItem">Air Jordan</li>
                    <li class="fListItem">Blazer</li>
                    <li class="fListItem">Crater</li>
                    <li class="fListItem">Hippie</li>
                </ul>
            </div>
        </div>
        <div class="footerRight">
            <div class="footerRightMenu">
                <h1 class="fMenuTitle">Subscribe to our newsletter</h1>
                <div class="fMail">
                    <input type="text" placeholder="your@email.com" class="fInput">
                    <button class="fButton">Join!</button>
                </div>
            </div>
            <div class="footerRightMenu">
                <h1 class="fMenuTitle">Follow Us</h1>
                <div class="fIcons">
                    <img src="./img/facebook.png" alt="" class="fIcon">
                    <img src="./img/twitter.png" alt="" class="fIcon">
                    <img src="./img/instagram.png" alt="" class="fIcon">
                    <img src="./img/whatsapp.png" alt="" class="fIcon">
                </div>
            </div>
            <div class="footerRightMenu">
                <span class="copyright">@John Doe. All rights reserved. 2022.</span>
            </div>
        </div>
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
