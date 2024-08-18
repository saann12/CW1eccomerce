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
$stmt->bind_param('s', $user_phone);
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}

$stmt->close();

// Handle product addition and update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Adding or Updating a Product
    if (!empty($_POST['title']) && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $title = $_POST['title'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $image = $_POST['image'];
        $categories = $_POST['categories'];
        $size = $_POST['size'];

        // If ID is 0, it means it's an addition (not update)
        if ($id === 0) {
            $sql = "INSERT INTO product (title, description, price, image, categories, size) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssdsss', $title, $description, $price, $image, $categories, $size);
        } else {
            // Update the product
            $sql = "UPDATE product SET title = ?, description = ?, price = ?, image = ?, categories = ?, size = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssdsssi', $title, $description, $price, $image, $categories, $size, $id);
        }

        if ($stmt->execute()) {
            // Redirect to avoid form resubmission
            header("Location: admin_panel.php");
            exit();
        } else {
            echo "Error saving product: " . $conn->error;
        }

        $stmt->close();
    }
    
    // Handling Product Deletion
    if (isset($_POST['delete'])) {
        $product_id = intval($_POST['delete']);
        
        $sql = "DELETE FROM product WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $product_id);

        if ($stmt->execute()) {
            header("Location: admin_panel.php");
            exit();
        } else {
            echo "Error deleting product: " . $conn->error;
        }

        $stmt->close();
    }
}

// Fetch products from the database
$sql = "SELECT id, title, description, price, image, categories, size FROM product";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    $products = $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch categories from the ENUM type
$sql = "SHOW COLUMNS FROM product LIKE 'categories'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$enum_values = $row['Type'];
preg_match_all("/'(.*?)'/", $enum_values, $matches);
$categories = $matches[1];

// Fetch sizes from the ENUM type
$sql = "SHOW COLUMNS FROM product LIKE 'size'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$enum_values = $row['Type'];
preg_match_all("/'(.*?)'/", $enum_values, $matches);
$sizes = $matches[1];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/admin_panel.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Additional CSS for better presentation */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .actions a {
            margin-right: 10px;
        }
        .edit-form {
            display: none;
            margin: 20px 0;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .edit-form input, .edit-form select, .edit-form textarea {
            width: 100%;
            margin-bottom: 10px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
    <script>
        function toggleEditForm(id) {
            const form = document.getElementById('edit-form-' + id);
            form.style.display = form.style.display === 'none' || form.style.display === '' ? 'block' : 'none';
        }

        function confirmDelete(productId) {
            if (confirm('Are you sure you want to delete this product?')) {
                document.getElementById('delete-form-' + productId).submit();
            }
        }
    </script>
</head>
<body>
    <header>
        <nav id="nav">
            <div class="navItem">
                <a href="admin_panel.php"><img class="logo" src="../../img/download.png" alt="HamroSportDokan"></a>
            </div>
            <div class="navLink">
                <ul>
                    <li><a href="admin_panel.php">Add Product</a></li>
                    <li><a href="ucontact.php">Contact user</a></li>
                    <li><a href="../../include/logout.php">Logout</a></li>
                    <li><a href="admin_profile.php"><?php echo htmlspecialchars($user['fullname']); ?></a></li>
                </ul>
            </div>
        </nav>
    </header>
    <div class="add-item">
        <h1>Add Product</h1>
        <form action="admin_panel.php" method="POST">
            <div class="profile-info">
                <label for="title">Product Name:</label>
                <input type="text" id="title" name="title" placeholder="Nike Mercurial" required>
            </div>
            <div class="profile-info">
                <label for="description">Description:</label>
                <textarea id="description" name="description" placeholder="Product Description" required></textarea>
            </div>
            <div class="profile-info">
                <label for="price">Price:</label>
                <input type="number" id="price" name="price" step="0.01" placeholder="150.00" required>
            </div>
            <div class="profile-info">
                <label for="image">Image URL:</label>
                <input type="text" id="image" name="image" placeholder="http://example.com/image.jpg">
            </div>
            <div class="profile-info">
                <label for="categories">Categories:</label>
                <select id="categories" name="categories" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category); ?>"><?php echo htmlspecialchars($category); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="profile-info">
                <label for="size">Size:</label>
                <select id="size" name="size" required>
                    <?php foreach ($sizes as $size): ?>
                        <option value="<?php echo htmlspecialchars($size); ?>"><?php echo htmlspecialchars($size); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <input type="hidden" name="id" value="0"> <!-- ID is 0 for new products -->
            <button type="submit">Add Product</button>
        </form>
    </div>

    <div class="product-list" id="list">
        <h1>Product List</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th>Categories</th>
                    <th>Size</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['id']); ?></td>
                            <td><?php echo htmlspecialchars($product['title']); ?></td>
                            <td><?php echo htmlspecialchars($product['description']); ?></td>
                            <td><?php echo htmlspecialchars($product['price']); ?></td>
                            <td><img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" width="100"></td>
                            <td><?php echo htmlspecialchars($product['categories']); ?></td>
                            <td><?php echo htmlspecialchars($product['size']); ?></td>
                            <td class="actions">
                                <button onclick="toggleEditForm(<?php echo htmlspecialchars($product['id']); ?>)">Edit</button>
                                <form id="delete-form-<?php echo htmlspecialchars($product['id']); ?>" action="admin_panel.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="delete" value="<?php echo htmlspecialchars($product['id']); ?>">
                                    <button type="button" onclick="confirmDelete(<?php echo htmlspecialchars($product['id']); ?>)">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="8">
                                <div id="edit-form-<?php echo htmlspecialchars($product['id']); ?>" class="edit-form">
                                    <h2>Edit Product</h2>
                                    <form action="admin_panel.php" method="POST">
                                        <div class="profile-info">
                                            <label for="title">Product Name:</label>
                                            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($product['title']); ?>" required>
                                        </div>
                                        <div class="profile-info">
                                            <label for="description">Description:</label>
                                            <textarea id="description" name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                                        </div>
                                        <div class="profile-info">
                                            <label for="price">Price:</label>
                                            <input type="number" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                                        </div>
                                        <div class="profile-info">
                                            <label for="image">Image URL:</label>
                                            <input type="text" id="image" name="image" value="<?php echo htmlspecialchars($product['image']); ?>">
                                        </div>
                                        <div class="profile-info">
                                            <label for="categories">Categories:</label>
                                            <select id="categories" name="categories" required>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?php echo htmlspecialchars($category); ?>" <?php if ($product['categories'] === $category) echo 'selected'; ?>>
                                                        <?php echo htmlspecialchars($category); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="profile-info">
                                            <label for="size">Size:</label>
                                            <select id="size" name="size" required>
                                                <?php foreach ($sizes as $size): ?>
                                                    <option value="<?php echo htmlspecialchars($size); ?>" <?php if ($product['size'] === $size) echo 'selected'; ?>>
                                                        <?php echo htmlspecialchars($size); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">
                                        <button type="submit">Update Product</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No products found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
