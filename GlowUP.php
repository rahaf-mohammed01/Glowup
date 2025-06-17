<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>GlowUp - Your Fashion Destination</title>

    <link rel="stylesheet" type="text/css" href="Glowup.css">
    <script src="Glowup.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
</head>

<body>
    
<!-- Header Section -->
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
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>


   <!-- <h2>
        <em>Clothes Women</em>
    </h2>
    <div class="product-container">
        <div class="product">
            <img src="https://rino-pelle.com/cdn/shop/files/Kuna.7002312_Blanc-Stone_00.jpg?v=1694536149&width=533"
                alt="Product 1">
            <div class="product-info">
                <h4>Jacket</h4>
                <p class="price">$199.99</p>
                <button id="add-to-bag-1">ADD TO BAG</button>
            </div>
        </div>

        <div class="product">
            <img src="https://rino-pelle.com/cdn/shop/files/Raiko.7002311_Lurex-Tile_00.jpg?v=1694536166&width=1066"
                alt="Product 2">
            <div class="product-info">
                <h4>cardigan & Blouse</h4>
                <p class="price">$399.99</p>
                <button id="add-to-bag-2">ADD TO BAG</button>
            </div>
        </div>

        <div class="product">
            <img src="https://rino-pelle.com/cdn/shop/files/Kolle.7002312_Wildlime_00.jpg?v=1694536203" alt="Product 3">
            <div class="product-info">
                <h4>Jacket</h4>
                <p class="price">$249.99</p>
                <button id="add-to-bag-3">ADD TO BAG</button>
            </div>
        </div>

        <div class="product">
            <img src="https://rino-pelle.com/cdn/shop/files/Dinty.7002312_Sage_00.jpg?v=1694536235&width=533"
                alt="Product 4">
            <div class="product-info">
                <h4>Dinty sweater</h4>
                <p class="price">$299.99</p>
                <button id="add-to-bag-4">ADD TO BAG</button>
            </div>
        </div>

        <div class="product">
            <img src="https://rino-pelle.com/cdn/shop/files/Jojo.7002310_cookie_00_84bcb524-a570-4f67-9d58-7a1418209ec8.jpg?v=1696369738&width=533"
                alt="Product 5">
            <div class="product-info">
                <h4>Coat</h4>
                <p class="price">$244.99</p>
                <button id="add-to-bag-5">ADD TO BAG</button>
            </div>
        </div>

        <div class="product">
            <img src="p6.jpg" alt="Product 6">
            <div class="product-info">
                <h4>Blouse</h4>
                <p class="price">$229.99</p>
                <button id="add-to-bag-6">ADD TO BAG</button>
            </div>
        </div>

        <div class="product">
            <img src="https://rino-pelle.com/cdn/shop/files/tenzil.7002311_dove_01.jpg?v=1690796356" alt="Product 7">
            <div class="product-info">
                <h4>sweater dress</h4>
                <p class="price">$239.99</p>
                <button id="add-to-bag-7">ADD TO BAG</button>
            </div>
        </div>

    </div>


    <h2>
        <em>Clothes Men</em>
    </h2>
    <div class="product-container">
        <div class="product">
            <img src="https://s3.eu-west-1.amazonaws.com/als-ecom-pimshm-prod-s3/assets/HNM/16761414/0974a5419ae5f2851a8d6a3627caae491556e74f/1/0974a5419ae5f2851a8d6a3627caae491556e74f.jpg"
                alt="Product 1">
            <div class="product-info">
                <h4>Hoodie</h4>
                <p class="price">$199.99</p>
                <button id="add-to-bag-1">ADD TO BAG</button>
            </div>
        </div>

        <div class="product">
            <img src="https://sa.hm.com/assets/styles/HNM/17008634/7ca674c098304ac89851d1b1fe28bcdd2f7cd574/1/image-thumb__5085211__product_zoom_medium_606x504/7ca674c098304ac89851d1b1fe28bcdd2f7cd574.jpg"
                alt="Product 2">
            <div class="product-info">
                <h4>Sweatshirt</h4>
                <p class="price">$199.99</p>
                <button id="add-to-bag-2">ADD TO BAG</button>
            </div>
        </div>

        <div class="product">
            <img src="https://s3.eu-west-1.amazonaws.com/als-ecom-pimshm-prod-s3/assets/HNM/17005572/c78b468f8c6d6009b2fe4591234a8caa1740c74b/1/c78b468f8c6d6009b2fe4591234a8caa1740c74b.jpg"
                alt="Product 3">
            <div class="product-info">
                <h4>Sweatshirt</h4>
                <p class="price">$249.99</p>
                <button id="add-to-bag-3">ADD TO BAG</button>
            </div>
        </div>

        <div class="product">
            <img src="https://sa.hm.com/assets/styles/HNM/16961609/eb9177a3bfc193a34c78291b578c80846ff7551c/1/image-thumb__5029398__product_zoom_medium_606x504/eb9177a3bfc193a34c78291b578c80846ff7551c.jpg"
                alt="Product 4">
            <div class="product-info">
                <h4>Jacket</h4>
                <p class="price">$299.99</p>
                <button id="add-to-bag-4">ADD TO BAG</button>
            </div>
        </div>

        <div class="product">
            <img src="https://sa.hm.com/assets/styles/HNM/16978959/662f816f0966df27b4c13ae7e75de23c027153df/1/image-thumb__5041949__product_zoom_medium_606x504/662f816f0966df27b4c13ae7e75de23c027153df.jpg"
                alt="Product 5">
            <div class="product-info">
                <h4>Sweatshirt</h4>
                <p class="price">$244.99</p>
                <button id="add-to-bag-5">ADD TO BAG</button>
            </div>
        </div>

        <div class="product">
            <img src="https://sa.hm.com/assets/styles/HNM/15690837/bf2573d3128296510d43299336fd4025c116c62e/1/image-thumb__4500660__product_zoom_medium_606x504/bf2573d3128296510d43299336fd4025c116c62e.jpg"
                alt="Product 6">
            <div class="product-info">
                <h4>Jacket</h4>
                <p class="price">$229.99</p>
                <button id="add-to-bag-6">ADD TO BAG</button>
            </div>
        </div>

        <div class="product">
            <img src="https://s3.eu-west-1.amazonaws.com/als-ecom-pimshm-prod-s3/assets/HNM/16740915/c6ea0b990966fbc77d7a7ff680e0d1248644d285/1/c6ea0b990966fbc77d7a7ff680e0d1248644d285.jpg"
                alt="Product 7">
            <div class="product-info">
                <h4>Hoodie</h4>
                <p class="price">$239.99</p>
                <button id="add-to-bag-7">ADD TO BAG</button>
            </div>
        </div>

    </div>


    <h2>
        <em>Kids girls</em>
    </h2>
    <div class="product-container">
        <div class="product">
            <img src="https://sa.hm.com/assets/styles/HNM/17312146/41c2ebefd4b1f49d151711a235c01fbf280df98c/1/image-thumb__5373004__product_zoom_large_800x800/41c2ebefd4b1f49d151711a235c01fbf280df98c.jpg"
                alt="Product 1">
            <div class="product-info">
                <h4>Lace-detail blouse</h4>
                <p class="price">$199.99</p>
                <button id="add-to-bag-1">ADD TO BAG</button>
            </div>
        </div>

        <div class="product">
            <img src="https://sa.hm.com/assets/styles/HNM/17083883/ec8fba1e1794acd04eec9d8c1e13b35ab0cdd019/1/image-thumb__5152775__product_zoom_large_800x800/ec8fba1e1794acd04eec9d8c1e13b35ab0cdd019.jpg"
                alt="Product 2">
            <div class="product-info">
                <h4>Wide joggers</h4>
                <p class="price">$200.99</p>
                <button id="add-to-bag-2">ADD TO BAG</button>
            </div>
        </div>

        <div class="product">
            <img src="https://sa.hm.com/assets/styles/HNM/16961534/cea5dd8b0de0fbb8de60e06a066e2f0f82ff705b/1/image-thumb__5030000__product_zoom_large_800x800/cea5dd8b0de0fbb8de60e06a066e2f0f82ff705b.jpg"
                alt="Product 3">
            <div class="product-info">
                <h4>Wide trousers</h4>
                <p class="price">$175.99</p>
                <button id="add-to-bag-3">ADD TO BAG</button>
            </div>
        </div>

        <div class="product">
            <img src="https://sa.hm.com/assets/styles/HNM/17217850/5337978774292c9a39cfadce3415cf90d607ce37/1/image-thumb__5318197__product_zoom_large_800x800/5337978774292c9a39cfadce3415cf90d607ce37.jpg"
                alt="Product 4">
            <div class="product-info">
                <h4>Dinty sweater</h4>
                <p class="price">$340.99</p>
                <button id="add-to-bag-4">ADD TO BAG</button>
            </div>
        </div>

        <div class="product">
            <img src="https://sa.hm.com/assets/styles/HNM/17061693/0bea7207ee683567b4b74db23ac36c92b3cf1452/1/image-thumb__5137649__product_zoom_large_800x800/0bea7207ee683567b4b74db23ac36c92b3cf1452.jpg"
                alt="Product 5">
            <div class="product-info">
                <h4>Oversized hoodie </h4>
                <p class="price">$400.99</p>
                <button id="add-to-bag-5">ADD TO BAG</button>
            </div>
        </div>

        <div class="product">
            <img src=" https://sa.hm.com/assets/styles/HNM/17207119/6bfd58731b11cf67c86d9d91a5a16c584def7438/1/image-thumb__5291982__product_zoom_large_800x800/6bfd58731b11cf67c86d9d91a5a16c584def7438.jpg"
                alt="Product 6">
            <div class="product-info">
                <h4>Ribbed dress </h4>
                <p class="price">$290.99</p>
                <button id="add-to-bag-6">ADD TO BAG</button>
            </div>
        </div>

        <div class="product">
            <img src="https://sa.hm.com/assets/styles/HNM/17035452/dc8f7a3545514ea65ad732d4476e417c5dd99931/1/image-thumb__5108788__product_zoom_large_800x800/dc8f7a3545514ea65ad732d4476e417c5dd99931.jpg"
                alt="Product 7">
            <div class="product-info">
                <h4>dress </h4>
                <p class="price">$280.99</p>
                <button id="add-to-bag-7">ADD TO BAG</button>
            </div>
        </div>

    </div>


    <h2>
        <em>Kids boys</em>
    </h2>
    <div class="product-container">
        <div class="product">
            <img src="https://sa.hm.com/assets/styles/HNM/17172624/cbeadccf24e45a6bc1706138bde7b9ffc8dfec98/1/image-thumb__5242400__product_zoom_large_800x800/cbeadccf24e45a6bc1706138bde7b9ffc8dfec98.jpg"
                alt="Product 1">
            <div class="product-info">
                <h4> sweatshirt</h4>
                <p class="price">$190.99</p>
                <button id="add-to-bag-1">ADD TO BAG</button>
            </div>
        </div>
        <div class="product">
            <img src="https://sa.hm.com/assets/styles/HNM/16985807/48f3dc710cd3d11ec983e91b0b7d435c87868b94/1/image-thumb__5051316__product_zoom_large_800x800/48f3dc710cd3d11ec983e91b0b7d435c87868b94.jpg"
                alt="Product 2">
            <div class="product-info">
                <h4>Printed joggers</h4>
                <p class="price">$199.99</p>
                <button id="add-to-bag-2">ADD TO BAG</button>
            </div>
        </div>

        <div class="product">
            <img src="https://sa.hm.com/assets/styles/HNM/17208227/b6df9636354a91f59431b3f2190efb3edc558dba/1/image-thumb__5290582__product_zoom_large_800x800/b6df9636354a91f59431b3f2190efb3edc558dba.jpg"
                alt="Product 4">
            <div class="product-info">
                <h4>neck top </h4>
                <p class="price">$100.99</p>
                <button id="add-to-bag-4">ADD TO BAG</button>
            </div>
        </div>

        <div class="product">
            <img src="https://sa.hm.com/assets/styles/HNM/17185803/50496553c5fef988c0ccadfaffa54563bd444c77/1/image-thumb__5262482__product_zoom_large_800x800/50496553c5fef988c0ccadfaffa54563bd444c77.jpg"
                alt="Product 5">
            <div class="product-info">
                <h4>sweatshirt</h4>
                <p class="price">$400.99</p>
                <button id="add-to-bag-5">ADD TO BAG</button>
            </div>
        </div>

        <div class="product">
            <img src="https://sa.hm.com/assets/styles/HNM/16705145/9dc95845f437f311dbcc9d1a6cbb46242bbc198b/1/image-thumb__4864489__product_zoom_large_800x800/9dc95845f437f311dbcc9d1a6cbb46242bbc198b.jpg"
                alt="Product 6">
            <div class="product-info">
                <h4>jacket</h4>
                <p class="price">$300.99</p>
                <button id="add-to-bag-6">ADD TO BAG</button>
            </div>
        </div>

        <div class="product">
            <img src="https://sa.hm.com/assets/styles/HNM/17312083/c4a4ab3844db7a36860ae8b6525d3f87f15ea949/1/image-thumb__5372109__product_zoom_large_800x800/c4a4ab3844db7a36860ae8b6525d3f87f15ea949.jpg"
                alt="Product 7">
            <div class="product-info">
                <h4>trousers </h4>
                <p class="price">$200.99</p>
                <button id="add-to-bag-7">ADD TO BAG</button>
            </div>
        </div>

        <div class="product">
            <img src="https://sa.hm.com/assets/styles/HNM/16894919/e4c174bfbbe570b67f83a729f7e73e162cdf0aed/1/image-thumb__4970420__product_zoom_large_800x800/e4c174bfbbe570b67f83a729f7e73e162cdf0aed.jpg"
                alt="Product 7">
            <div class="product-info">
                <h4>Denim dungarees </h4>
                <p class="price">$199.99</p>
                <button id="add-to-bag-7">ADD TO BAG</button>
            </div>
        </div>

    </div> -->

    <!-- Chat Button -->
<a href="chat.php" class="simple-chat-btn" target="_top" title="Chat with Fashion Assistant">
  <i class="fas fa-comments"></i>
  <span class="chat-badge">!</span>
</a>
 <!-- Footer Section -->
    <footer class="footer">
        <div class="container">
            <!-- Footer Content Links -->
            <div class="footer-content">
                <div class="footer-section">
                    <h5>Company Info</h5>
                    <ul>
                        <li><a href="#" aria-label="About GlowUp">About GlowUp</a></li>
                        <li><a href="#" aria-label="Fashion Blogger">Fashion Blogger</a></li>
                        <li><a href="#" aria-label="Features">Features</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h5>Help</h5>
                    <ul>
                        <li><a href="#" aria-label="Shipping Information">Shipping Info</a></li>
                        <li><a href="#" aria-label="Returns Policy">Returns</a></li>
                        <li><a href="#" aria-label="Refund Policy">Refund</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h5>Customer Care</h5>
                    <ul>
                        <li><a href="#" aria-label="Contact Us">Contact Us</a></li>
                        <li><a href="#" aria-label="Payment Methods">Payment Method</a></li>
                        <li><a href="#" aria-label="Bonus Points Program">Bonus Points</a></li>
                    </ul>
                </div>
            </div>
            
            <!-- Social Media Icons -->
            <div class="social-icons">
                <a href="#" aria-label="Follow us on Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" aria-label="Contact us on WhatsApp"><i class="fab fa-whatsapp"></i></a>
                <a href="#" aria-label="Follow us on Facebook"><i class="fab fa-facebook"></i></a>
                <a href="#" aria-label="Follow us on Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" aria-label="Follow us on TikTok"><i class="fab fa-tiktok"></i></a>
            </div>

            <!-- Payment Methods -->
            <div class="payment-methods">
                <img src="https://cdn.msaaq.com/assets/images/payments/applepay.svg" alt="Apple Pay">
                <img src="https://cdn.msaaq.com/assets/images/payments/visa.svg" alt="Visa">
                <img src="https://cdn.msaaq.com/assets/images/payments/master.svg" alt="Mastercard">
                <img src="https://cdn.msaaq.com/assets/images/payments/mada.svg" alt="Mada">
            </div>

            <!-- Copyright -->
            <div class="footer-bottom">
                <p>&copy; 2023 GlowUp. All rights reserved.</p>
            </div>
        </div>
    </footer>

<div class="c-footer" title="back to top">
        <button type="button" id="backtotop" onclick="scrollToTop()">&#8593;</button>
    </div>

    <script> // Scroll to top function
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

 </script>

</body>

</html>