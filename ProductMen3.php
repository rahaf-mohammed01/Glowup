<!DOCTYPE html>
<html>

<head>
    <title>GlowUp-Men Clothes</title>

    <link rel="stylesheet" type="text/css" href="style4.css">
    <script src="script4.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>

</head>

<body>
   <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
            <div class="container">
                <a class="navbar-brand" href="#">
                    <h4><em>GlowUp</em></h4>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="Women.php">Women</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="Men.php">Men</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="Wishlist.php" style="color: #b07154; font-weight: bold;">
                                <i class='bx bx-heart'></i> Wishlist
                            </a>
                        </li>
                          <li class="nav-item">
                            <a class="nav-link" href="ShoppingBag.php">
                            <i class="fa-solid fa-bag-shopping style="color: #969696;"></i>
                               Shopping Bag
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="Account.php">
                            <i class="fa-solid fa-user" style="color: #969696;"></i>
                                Account
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    
    <div class="product-cards-container">

        <div class="product-card">
            <div class="shoe-details">
                 <img src="https://image.hm.com/assets/hm/20/fa/20fa93e3aff7cca10b40a6c6da5786dc91b23e28.jpg?imwidth=2160"
                    alt="Product 1">
                <span class="shoe-name">Sweatshirt</span>
                <p>This stylish sweatshirt is made from high-quality cotton fabric, providing comfort and durability.<br />
                    It features a trendy design with a relaxed fit, perfect for casual wear. Available in various colors and sizes.</p>
        
            </div>

            <div class="color-size-price">
                <div class="color-option">
                    <span class="color">Color:</span>
                    <div class="circles">
                        <span class="circle beige active" id="beige"></span>
                        <span class="circle black" id="black"></span>
                        <span class="circle brown" id="brown"></span>
                    </div>
                </div>


                <div class="size-option">
                    <span class="size">Size:</span>
                    <div class="sizes">
                        <span class="size-option">XS</span>
                        <span class="size-option">S</span>
                        <span class="size-option">M</span>
                        <span class="size-option">L</span>
                        <span class="size-option">XL</span>
                    </div>
                </div>

                <div class="price">
                    <span class="price_num">SAR 249.99</span>
                </div>
            </div>

            <div class="button">
                <button class="add-to-bag">Add to Bag</button>
            </div>
                         <button class="wishlist-heart" data-product-id="Sweatshirt6-1" onclick="toggleWishlist(this)">
                <i class="bx bx-heart"></i>
            </button>
        </div>


        <div class="product-card">
            <div class="shoe-details">
                 <img src="https://image.hm.com/assets/hm/17/2f/172f1d8288e54d28b932edb0f7bc40a412815ecf.jpg?imwidth=1260"
                    alt="Product 2">
                <span class="shoe-name">Jacket</span>
                <p>This stylish jacket is perfect for any season. It is made from high-quality materials, ensuring durability and comfort.<br />
                     The jacket features a modern design with a sleek fit. Stay warm and fashionable with this must-have addition to your wardrobe.</p>
               
            </div>
            <div class="color-size-price">
                <div class="color-option">
                    <span class="color">Color:</span>
                    <div class="circles">
                        <span class="circle beige active" id="beige"></span>
                        <span class="circle black" id="black"></span>
                        <span class="circle brown" id="brown"></span>
                    </div>
                </div>


                <div class="size-option">
                    <span class="size">Size:</span>
                    <div class="sizes">
                        <span class="size-option">XS</span>
                        <span class="size-option">S</span>
                        <span class="size-option">M</span>
                        <span class="size-option">L</span>
                        <span class="size-option">XL</span>
                    </div>
                </div>

                <div class="price">
                    <span class="price_num">SAR 299.99</span>
                </div>
            </div>

            <div class="button">
                <button class="add-to-bag">Add to Bag</button>
            </div>
                         <button class="wishlist-heart" data-product-id="Jacket9-1" onclick="toggleWishlist(this)">
                <i class="bx bx-heart"></i>
            </button>
        </div>

        <div class="product-card">
            <div class="shoe-details">
                <img src="https://image.hm.com/assets/hm/38/dc/38dc82136785b0fafe44c4126efd68b5b0799137.jpg?imwidth=1260"
                    alt="Product 3">
                <span class="shoe-name">Hoodie</span>
                <p>A comfortable and stylish hoodie for any occasion. Made from high-quality materials, this hoodie is perfect for keeping you warm and cozy during the colder months.<br />
                    Available in multiple colors and sizes to suit your preference.</p>
               
            </div>

            <div class="color-size-price">
                <div class="color-option">
                    <span class="color">Color:</span>
                    <div class="circles">
                        <span class="circle beige active" id="beige"></span>
                        <span class="circle black" id="black"></span>
                        <span class="circle brown" id="brown"></span>
                    </div>
                </div>


                <div class="size-option">
                    <span class="size">Size:</span>
                    <div class="sizes">
                        <span class="size-option">XS</span>
                        <span class="size-option">S</span>
                        <span class="size-option">M</span>
                        <span class="size-option">L</span>
                        <span class="size-option">XL</span>
                    </div>
                </div>

                <div class="price">
                    <span class="price_num">SAR 239.99</span>
                </div>
            </div>

            <div class="button">
                <button class="add-to-bag">Add to Bag</button>
            </div>
                         <button class="wishlist-heart" data-product-id="Hoodie5-1" onclick="toggleWishlist(this)">
                <i class="bx bx-heart"></i>
            </button>
        </div>


        <div class="product-card">
            <div class="shoe-details">
                 <img src="https://media.alshaya.com/adobe/assets/urn:aaid:aem:1851cd7e-a95a-4ffb-ab13-872c33bd83bc/as/EID-b6bbe296d9a1cf3f4e3b83ea4b456ca703afe245.jpg?preferwebp=true&height=630"
                    alt="Product 4">
                    <span class="shoe-name">Jacket</span>
                <p>This stylish jacket is perfect for any season. It is made from high-quality materials, ensuring durability and comfort.<br />
                     The jacket features a modern design with a sleek fit. Stay warm and fashionable with this must-have addition to your wardrobe.</p>
               
            </div>

            <div class="color-size-price">
                <div class="color-option">
                    <span class="color">Color:</span>
                    <div class="circles">
                        <span class="circle beige active" id="beige"></span>
                        <span class="circle black" id="black"></span>
                        <span class="circle brown" id="brown"></span>
                    </div>
                </div>


                <div class="size-option">
                    <span class="size">Size:</span>
                    <div class="sizes">
                        <span class="size-option">XS</span>
                        <span class="size-option">S</span>
                        <span class="size-option">M</span>
                        <span class="size-option">L</span>
                        <span class="size-option">XL</span>
                    </div>
                </div>

                <div class="price">
                    <span class="price_num">SAR 232.99</span>
                </div>
            </div>

            <div class="button">
                <button class="add-to-bag">Add to Bag</button>
            </div>
                         <button class="wishlist-heart" data-product-id="Jacket10-1" onclick="toggleWishlist(this)">
                <i class="bx bx-heart"></i>
            </button>
        </div>
    </div>

    <div class="pagination-container">
        <a href="Men2.php" class="pagination-item">
            <img src="https://f.nooncdn.com/s/app/com/sivvi/design-system/icons-v2/chevron-back.svg" alt="chevronBack"
                class="pagination-icon" loading="eager">
        </a>

        <a href="Men.php" class="pagination-item">1</a>
        <a href="Men2.php" class="pagination-item">2</a>
        <span class="ellipsis">...</span>
        <a href="Men3.php" class="pagination-item">3</a>

        </a>
    </div>

    <div class="c-footer" title="back to top">
        <button id="backtotop">&#8593;</button>
    </div>
    
</body>