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
              <img src="https://rino-pelle.com/cdn/shop/files/Zinna.5002413_Black-feather_02.jpg?v=1726754477"
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
             <button class="wishlist-heart" data-product-id="Dress-1" onclick="toggleWishlist(this)">
                <i class="bx bx-heart"></i>
            </button>
        </div>





        <div class="product-card">
            <div class="shoe-details">
                <img src="https://rino-pelle.com/cdn/shop/files/Holda.7002522_lilac-pink_04.jpg?v=1734011629"
                    alt="Product 2">
                <span class="shoe-name">Blouse</span>
                <p>This blouse is made from high-quality fabric and features a trendy design that is perfect for any
                    occasion.<br />
                    Its comfortable fit and stylish details make it a must-have addition to your wardrobe. Pair it with
                    your
                    favorite jeans or a skirt for a chic and fashionable look.</p>

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
                    <span class="price_num">SAR 399.99</span>
                </div>
            </div>

            <div class="button">
                <button class="add-to-bag">Add to Bag</button>
            </div>
             <button class="wishlist-heart" data-product-id="Blouse2-1" onclick="toggleWishlist(this)">
                <i class="bx bx-heart"></i>
            </button>
        

        </div>


        <div class="product-card">
            <div class="shoe-details">
                 <img src="https://rino-pelle.com/cdn/shop/files/Isarea.5002521_Snow-white_01.jpg?v=1734605012"
                    alt="Product 3">
                <span class="shoe-name">Cardigan and Blouse</span>
                <p>A stylish and comfortable cardigan and blouse set that is perfect for both casual and formal
                    occasions.<br />
                    The cardigan features a soft and cozy fabric, while the blouse is made from a lightweight and
                    breathable material.<br />
                    The set can be easily paired with jeans, skirts, or trousers for a versatile and trendy look.</p>

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
             <button class="wishlist-heart" data-product-id="Cardigan2-1" onclick="toggleWishlist(this)">
                <i class="bx bx-heart"></i>
            </button>
        </div>


        <div class="product-card">
            <div class="shoe-details">
               <img src="https://rino-pelle.com/cdn/shop/files/Seo.5002414_graphite_01_4b2822d1-adfd-4e73-8478-232aa0b2d163.jpg?v=1736769806" alt="Product 4">
                <span class="shoe-name">Dinty sweater</span>
                <p>A cozy and stylish sweater for any occasion. Made from high-quality materials, the Dinty sweater
                    offers both comfort and fashion.<br />
                    Its soft fabric keeps you warm during chilly days, while the trendy design adds a touch of elegance
                    to your outfit.<br />
                    Choose your preferred color and size to make a fashion statement.</p>
               
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
             <button class="wishlist-heart" data-product-id="sweater-1" onclick="toggleWishlist(this)">
                <i class="bx bx-heart"></i>
            </button>
        </div>


        <div class="product-card">
            <div class="shoe-details">
                 <img src="https://rino-pelle.com/cdn/shop/files/Keila.7002411_Graphite-and-purple_02.jpg?v=1722348823"
                    alt="Product 5">
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
                    <span class="price_num">SAR 244.99</span>
                </div>
            </div>

            <div class="button">
                <button class="add-to-bag">Add to Bag</button>
            </div>
             <button class="wishlist-heart" data-product-id="coat2-1" onclick="toggleWishlist(this)">
                <i class="bx bx-heart"></i>
            </button>
        </div>


        <div class="product-card">
            <div class="shoe-details">
                <img src="https://rino-pelle.com/cdn/shop/files/Jenny.7002411_Artichoke_01.jpg?v=1722499709" alt="Product 6">
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
                    <span class="price_num">SAR 229.99</span>
                </div>
            </div>

            <div class="button">
                <button class="add-to-bag">Add to Bag</button>
            </div>
             <button class="wishlist-heart" data-product-id="coat3-1" onclick="toggleWishlist(this)">
                <i class="bx bx-heart"></i>
            </button>
        </div>

    </div>


    <div class="pagination-container">
        <a href="Women.php" class="pagination-item">
            <img src="https://f.nooncdn.com/s/app/com/sivvi/design-system/icons-v2/chevron-back.svg" alt="chevronBack"
                class="pagination-icon" loading="eager">
        </a>

        <a href="Women.php" class="pagination-item">1</a>
        <a href="Women2.php" class="pagination-item">2</a>
        <span class="ellipsis">...</span>
        <a href="Women3.php" class="pagination-item">3</a>

        <a href="Women3.php" class="pagination-item">
            <img src="https://f.nooncdn.com/s/app/com/sivvi/design-system/icons-v2/chevron-forward.svg"
                alt="chevronForward" class="pagination-icon" loading="eager">
        </a>
    </div>

    <div class="c-footer" title="back to top">
        <button id="backtotop">&#8593;</button>
    </div>

</body>