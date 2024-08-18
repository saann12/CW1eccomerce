<?php
// Start the session
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

// Add product to cart
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])) {
    $product_id = $conn->real_escape_string($_GET['id']);

    // Fetch product details
    $sql = "SELECT * FROM product WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();

        // Initialize the cart if not already
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }

        // Add product to the cart
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity']++;
        } else {
            $_SESSION['cart'][$product_id] = array(
                'title' => $product['title'],
                'price' => $product['price'],
                'quantity' => 1
            );
        }

        // Redirect to cart page
        header('Location: cart.php');
        exit();
    } else {
        echo "Product not found!";
        exit();
    }
}

// Remove product from cart
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $product_id = $conn->real_escape_string($_GET['id']);

    // Remove product from the cart
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }

    // Redirect to cart page
    header('Location: cart.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/cart.css"> 
    <!-- <link rel="stylesheet" href="../../css/style.css"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Shopping Cart</title>
</head>
<body>
<header>
    <nav id="nav">
        <div class="navItem">
            <a href="index.php"><img class="logo" src="../../img/download.png" alt="Logo"></a>
        </div>
        <div class="navLink">
            <ul></ul>
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
    <section class="cart-page">
        <h3>Your Cart</h3>
        <form method="POST" action="cart.php">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                        $total = 0;
                        foreach ($_SESSION['cart'] as $product_id => $product) {
                            $product_total = $product['price'] * $product['quantity'];
                            $total += $product_total;
                            echo "<tr>
                                    <td>" . htmlspecialchars($product['title']) . "</td>
                                    <td>Rs." . htmlspecialchars($product['price']) . "</td>
                                    <td>" . htmlspecialchars($product['quantity']) . "</td>
                                    <td>Rs." . htmlspecialchars($product_total) . "</td>
                                    <td><a href='cart.php?action=remove&id=" . htmlspecialchars($product_id) . "' class='remove-item' onclick='return confirm(\"Are you sure you want to remove this item?\")'>Remove</a></td>
                                  </tr>";
                        }
                        echo "<tr>
                                <td colspan='3' class='total-label'>Total</td>
                                <td class='total-amount'>Rs." . htmlspecialchars($total) . "</td>
                                <td class='order-button'>
                                    <button type='button' id='checkoutButton' class='productButton'>Proceed to Checkout</button>
                                </td>
                              </tr>";
                    } else {
                        echo "<tr><td colspan='5'>Your cart is empty.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </form>
        <div class="payment" id="paymentSection">
            <div class="payment-content">
                <span class="close" id="closePayment">X</span>
                <h1 class="payTitle">Personal Information</h1>
                <label>Name and Surname</label>
                <input type="text" placeholder="John Doe" class="payInput">
                <br><label>Phone Number</label>
                <input type="text" placeholder="+1 234 5678" class="payInput">
                <br><label>Address</label>
                <input type="text" placeholder="Elton St 21 22-145" class="payInput">
                <h1 class="payTitle">Card Information</h1>
                <div class="cardIcons">
                    <img src="../../img/visa.png" width="40" alt="" class="cardIcon">
                    <img src="../../img/master.png" alt="" width="40" class="cardIcon">
                </div>
                <input type="password" class="payInput" placeholder="Card Number">
                <div class="cardInfo">
                    <input type="text" placeholder="mm" class="payInput sm">
                    <input type="text" placeholder="yyyy" class="payInput sm">
                    <input type="text" placeholder="cvv" class="payInput sm">
                </div>
                <button class="payButton">Checkout!</button>
            </div>
        </div>
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
                <h1 class="fMenuTitle">shotps</h1>
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
                    <img src="../../img/facebook.png" alt="" class="fIcon">
                    <img src="../../img/twitter.png" alt="" class="fIcon">
                    <img src="../../img/instagram.png" alt="" class="fIcon">
                    <img src="../../img/whatsapp.png" alt="" class="fIcon">
                </div>
            </div>
            <div class="footerRightMenu">
                <span class="copyright">@John Doe. All rights reserved. 2022.</span>
            </div>
        </div>
    </footer>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get elements
        const checkoutButton = document.getElementById('checkoutButton');
        const paymentSection = document.getElementById('paymentSection');
        const closePayment = document.getElementById('closePayment');

        // Toggle payment section visibility
        checkoutButton.addEventListener('click', function() {
            paymentSection.style.display = 'flex'; // Show the payment section
        });

        // Close payment section
        closePayment.addEventListener('click', function() {
            paymentSection.style.display = 'none'; // Hide the payment section
        });
    });
</script>

</body>
</html>
