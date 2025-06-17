<?php
// Start the session to manage user sessions
session_start();

include('auth_middleware.php');

// Require user role to access this page
requireCustomer();

// Check if the user is not logged in; if not, redirect to the login page
if (!isset($_SESSION['username'])) {
    header('location: login.php');
    exit(); // Stop further execution of the script
}

// Variable to control the display of the welcome message
$showWelcomeMessage = false;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>GlowUp - Home Page</title>

    <link rel="stylesheet" type="text/css" href="home.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>

</head>

<body>
    <div id="welcomeMessage"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const welcomeMessage = document.getElementById('welcomeMessage');

            // Check if the welcome message should be displayed
            const shouldDisplayWelcomeMessage = <?php echo isset($_SESSION['showWelcomeMessage']) ? 'true' : 'false'; ?>;

            if (shouldDisplayWelcomeMessage) {
                const username = '<?php echo $_SESSION['username']; ?>';

                // Create a div element for the colored square
                const coloredSquare = document.createElement('div');
                coloredSquare.classList.add('colored-square');

                // Set the background color of the colored square
                coloredSquare.style.backgroundColor = '#C5AB96';

                // Create a div element for the welcome message
                const welcomeMessageDiv = document.createElement('div');
                welcomeMessageDiv.textContent = 'Welcome to GlowUp, ' + username + '!';

                // Append the welcome message div to the colored square
                coloredSquare.appendChild(welcomeMessageDiv);

                // Append the colored square to the welcomeMessage container
                welcomeMessage.appendChild(coloredSquare);

                // Show the welcome message container
                welcomeMessage.style.display = 'block';

                // Hide the welcome message after 8 seconds 
                setTimeout(function() {
                    welcomeMessage.style.display = 'none';
                }, 8000);

                // Remove the session variable to prevent showing the message on page reload
                <?php unset($_SESSION['showWelcomeMessage']); ?>;
            }
        });
        // Scroll to top function
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    </script>

    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
            <div class="container">
                <a class="navbar-brand" href="GlowUP.php">
                    <h4><em>GlowUp</em></h4>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">




                        <!-- Left section: Home -->

                        <li class="nav-item">
                            <a class="nav-link" href="home.php" aria-label="Home">
                                <i class="fas fa-home"></i>
                                <span>Home</span>
                            </a>
                        </li>


                        <!-- Center section: Main navigation -->

                        <li class="nav-item">
                            <a class="nav-link" href="Women.php" aria-label="Women's Collection">
                                <i class="fas fa-female"></i>
                                <span>Women</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="Men.php" aria-label="Men's Collection">
                                <i class="fas fa-male"></i>
                                <span>Men</span>
                            </a>
                        </li>
                        <li class="nav-item position-relative">
                            <a class="nav-link wishlist-link" href="Wishlist.php" aria-label="Wishlist">
                                <i class="bx bx-heart"></i>
                                <span>Wishlist</span>
                                <span class="wishlist-counter" id="wishlistCounter"> </span>
                            </a>
                        </li>
                        <li class="nav-item position-relative">
                            <a class="nav-link" href="ShoppingBag.php" aria-label="Shopping Bag">
                                <i class="fas fa-shopping-bag"></i>
                                <span>Shopping Bag</span>
                                <span class="shopping-bag-counter" id="bagCounter" style="display: none;"> </span>
                            </a>
                        </li>


                        <!-- Right section: Search and Account -->
                        <div class="navbar-nav navbar-nav-right">
                            <!-- Search bar -->
                            <div class="search-container">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" class="search-input" placeholder="Search products..." aria-label="Search">
                            </div>

                            <!-- Account -->

                            <li class="nav-item">
                                <a class="nav-link" href="Account.php" aria-label="My Account">
                                    <i class="fas fa-user"></i>
                                    <span>Account</span>
                                </a>
                            </li>
                    </ul>

                </div>
            </div>
            </div>
            </div>
        </nav>
    </header>


    <!-- Main Content Section -->
    <div id="shopify-section-template--19799974773067__264455d4-f47a-4ee6-93df-555e68d05dbf" class="shopify-section">
        <!-- Main Banner Section -->
        <section class="section-main-banner firstbnr_cls section-id-template--19799974773067__264455d4-f47a-4ee6-93df-555e68d05dbf">
            <!-- Row for banner content -->
            <div class="row b_imgdatarow">
                <!-- Banner content snippet -->
                <div class="snippet-main-banner bnr_template--19799974773067__264455d4-f47a-4ee6-93df-555e68d05dbf full-width">
                    <!-- Video container -->
                    <div class="video-container">
                        <!-- Video element -->
                        <video playsinline="true" preload="metadata" muted="muted" loop="loop" autoplay="autoplay"
                            class="fullvideo"
                            poster="//rino-pelle.com/cdn/shop/files/preview_images/4a1df70d39124f5c89199bdb769a96ab.thumbnail.0000000000_small.jpg?v=1694531101">
                            <source
                                src="https://cdn.shopify.com/videos/c/vp/4a1df70d39124f5c89199bdb769a96ab/4a1df70d39124f5c89199bdb769a96ab.HD-1080p-7.2Mbps-18326668.mp4"
                                type="video/mp4">
                            <img
                                src="//rino-pelle.com/cdn/shop/files/preview_images/4a1df70d39124f5c89199bdb769a96ab.thumbnail.0000000000_small.jpg?v=1694531101">
                        </video>
                        <!-- Video content overlay -->
                        <div class="video-content">
                            <h1>
                                <em>GlowUp</em>
                            </h1>
                            <h3>
                                <em>Discover Your Style</em>
                            </h3>
                            <p>
                                <em>Get inspired by our curated collection of fashionable clothing</em>
                            </p>
                            <a href="GlowUp.php" class="mainbnr_wrap">
                                <span class="button-border">Discover now</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Chat Button -->
    <a href="chat.php" class="simple-chat-btn" target="_top" title="Chat with Fashion Assistant">
        <i class="fas fa-comments"></i>
        <span class="chat-badge">!</span>
    </a>


    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h5>Company Info</h5>
                    <ul>
                        <li><a href="#">About GlowUp</a></li>
                        <li><a href="#">Fashion Blogger</a></li>
                        <li><a href="#">Features</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h5>Help</h5>
                    <ul>
                        <li><a href="#">Shipping Info</a></li>
                        <li><a href="#">Returns</a></li>
                        <li><a href="#">Refund</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h5>Customer Care</h5>
                    <ul>
                        <li><a href="#">Contact Us</a></li>
                        <li><a href="#">Payment Method</a></li>
                        <li><a href="#">Bonus Points</a></li>
                    </ul>
                </div>
            </div>

            <div class="social-icons">
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-whatsapp"></i></a>
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-tiktok"></i></a>
            </div>

            <div class="payment-methods">
                <img src="https://cdn.msaaq.com/assets/images/payments/applepay.svg" alt="Apple Pay">
                <img src="https://cdn.msaaq.com/assets/images/payments/visa.svg" alt="Visa">
                <img src="https://cdn.msaaq.com/assets/images/payments/master.svg" alt="Mastercard">
                <img src="https://cdn.msaaq.com/assets/images/payments/mada.svg" alt="Mada">
            </div>

            <div class="footer-bottom">
                <p>&copy; 2023 GlowUp. All rights reserved.</p>
            </div>
        </div>
    </footer>
      <div class="c-footer" title="back to top">
        <button type="button" id="backtotop" onclick="scrollToTop()">&#8593;</button>
    </div>



</body>

</html>