<!DOCTYPE html>
<html>

<head>
    <title>GlowUp-Clothes Women</title>
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
                 <img src="https://rino-pelle.com/cdn/shop/files/Tolga.7002413_Dark-brown_01_7c2b56aa-b387-4545-beba-3a9c6b77ed87.jpg?v=1736798542"
                    alt="Product 1">
                <span class="shoe-name">Dress sweater</span>
                <p>This dress sweater is made from high-quality materials and features a stylish design that is perfect
                    for any occasion.<br />
                    It offers a comfortable fit and is available in multiple colors and sizes.<br />
                    Choose your preferred color and size from the options below and add this trendy dress sweater to
                    your wardrobe today!</p>
                
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
                         <button class="wishlist-heart" data-product-id="Dress sweater-1" onclick="toggleWishlist(this)">
                <i class="bx bx-heart"></i>
            </button>
        </div>


        <div class="product-card">
            <div class="shoe-details">
              <img src="https://rino-pelle.com/cdn/shop/files/Harvard.7002412_Birch_01.jpg?v=1725013407"
                    alt="Product 2">
                <span class="shoe-name">Coat</span>
                <p>This stylish coat is perfect for all seasons. It features a comfortable fit and high-quality<br />
                    materials to keep you warm and stylish. Available in various colors and sizes.</p>
               
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
                         <button class="wishlist-heart" data-product-id="coat4-1" onclick="toggleWishlist(this)">
                <i class="bx bx-heart"></i>
            </button>
        </div>

        <div class="product-card">
            <div class="shoe-details">
                 <img src="https://rino-pelle.com/cdn/shop/files/Bubbly.5002520_stone_02-18280.jpg?v=1737651641" alt="Product 3">
                <span class="shoe-name">Jacket</span>
                <p>This jacket is made of high-quality materials and designed for both style and comfort.
                    It features a modern cut and a sleek design that will elevate any outfit.
                    The jacket is perfect for both casual and formal occasions, and it's suitable for all seasons.
                    Stay fashionable and cozy with this versatile piece!</p>
                
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
                    <span class="price_num">SAR 199.99</span>
                </div>
            </div>

            <div class="button">
                <button class="add-to-bag">Add to Bag</button>
            </div>
                         <button class="wishlist-heart" data-product-id="Jacket3-1" onclick="toggleWishlist(this)">
                <i class="bx bx-heart"></i>
            </button>
        </div>

        <div class="product-card">
            <div class="shoe-details">
                <img src="https://rino-pelle.com/cdn/shop/files/Mako.7002412_Black_01.jpg?v=1723729415"
                    alt="Product 4">
                <span class="shoe-name">Mako trousers</span>
                <p>The Mako trousers are stylish and comfortable, made from high-quality materials.<br />
                    With multiple pockets and a versatile design, they are perfect for various occasions,<br />
                    whether a day at the office or a casual outing.</p>
               
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
                         <button class="wishlist-heart" data-product-id="Mako trousers" onclick="toggleWishlist(this)">
                <i class="bx bx-heart"></i>
            </button>
        </div>
    </div>


    <div class="pagination-container">
        <a href="Women2.php" class="pagination-item">
            <img src="https://f.nooncdn.com/s/app/com/sivvi/design-system/icons-v2/chevron-back.svg" alt="chevronBack"
                class="pagination-icon" loading="eager">
        </a>

        <a href="Women.php" class="pagination-item">1</a>
        <a href="Women2.php" class="pagination-item">2</a>
        <span class="ellipsis">...</span>
        <a href="Women3.php" class="pagination-item">3</a>

        <a href="Men.php" class="pagination-item">
            <img src="https://f.nooncdn.com/s/app/com/sivvi/design-system/icons-v2/chevron-forward.svg"
                alt="chevronForward" class="pagination-icon" loading="eager">
        </a>
    </div>

    <div class="c-footer" title="back to top">
        <button id="backtotop">&#8593;</button>
    </div>

</body>