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

$stmt->close();
$conn->close();
?>
<php lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@500&family=Nunito&family=Palanquin+Dark:wght@500&family=Roboto+Slab:wght@700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/style3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Nike Store</title>
</head>

<header>
    <nav id="nav">
        <div class="navItem">
            <a href="index.php"></a><img class="logo" src="../../img/download.png" alt="HamroSportDokan"></a>
        </div>
        <div class="navLink">
            <ul></ul>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="contact.php">Contact us</a></li>
                <li><a href="../../include/logout.php">logout</a></li>
                <li><a href="profile.php"><?php echo htmlspecialchars($user['fullname']); ?></a></aside></li>
                <li><a href="cart.php"><i class="fas fa-shopping-cart"></i></a></li>
            </ul>

        </div>
    </nav>
    </header>

    <main>
        <section class="contact-section">
            <div class="contact-header">
                <h1>Get in Touch</h1>
                <p>We'd love to hear from you! Whether you have a question, feedback, or just want to say hi, feel free
                    to reach out to us.</p>
            </div>

            <div class="contact-content">
                <div class="contact-info">
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <h2>Our Location</h2>
                        <p>123 Nike Street, Sports City, SC 12345, USA</p>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-phone-alt"></i>
                        <h2>Phone Number</h2>
                        <p>(+1) 234-567-890</p>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <h2>Email Address</h2>
                        <p>support@nikestore.com</p>
                    </div>
                </div>

                <div class="contact-form">
                    <h2>Send Us a Message</h2>
                    <form action="/submit_form" method="post">
                        <div class="form-group">
                            <label for="name">Name:</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="message">Message:</label>
                            <textarea id="message" name="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="submit-btn">Send Message</button>
                    </form>
                </div>
            </div>
        </section>

        <section class="map-section">
            <h2>Find Us on the Map</h2>
            <div id="map"></div>
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
    <script src="/js/scrip.js"></script>
    </body>

</php>