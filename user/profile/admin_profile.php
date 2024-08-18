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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/profile.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
<div class="profile-container">
    <h1>User Profile</h1>
    <div class="profile-info">
        <label for="fullname">Full Name:</label>
        <p id="fullname"><?php echo htmlspecialchars($user['fullname']); ?></p>
    </div>
    <div class="profile-info">
        <label for="email">Email:</label>
        <p id="email"><?php echo htmlspecialchars($user['email']); ?></p>
    </div>
    <div class="profile-info">
        <label for="phone">Phone:</label>
        <p id="phone"><?php echo htmlspecialchars($user['phone']); ?></p>
    </div>
</div>
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
</body>
</html>
