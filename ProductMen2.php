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
                <img src="https://image.hm.com/assets/hm/90/8f/908f04cd80bd1933b069c496295324605b28d933.jpg?imwidth=820"
                alt="Product 1">
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
                    <span class="price_num">SAR 199.99</span>
                </div>
            </div>

            <div class="button">
                <button class="add-to-bag">Add to Bag</button>
            </div>
                         <button class="wishlist-heart" data-product-id="Hoodie2-1" onclick="toggleWishlist(this)">
                <i class="bx bx-heart"></i>
            </button>
        </div>


        <div class="product-card">
            <div class="shoe-details">
                 <img src="https://media.alshaya.com/adobe/assets/urn:aaid:aem:73647b09-a171-4d83-9e81-3705e25fa104/as/EID-cad789607871c2c2211607bc4393890fa6e89ec3.jpg?preferwebp=true&height=630"
                    alt="Product 2">
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
                    <span class="price_num">SAR 199.99</span>
                </div>
            </div>

            <div class="button">
                <button class="add-to-bag">Add to Bag</button>
            </div>
                         <button class="wishlist-heart" data-product-id="Sweatshirt4" onclick="toggleWishlist(this)">
                <i class="bx bx-heart"></i>
            </button>
        </div>


        


        <div class="product-card">
            <div class="shoe-details">
               <img src="https://media.alshaya.com/adobe/assets/urn:aaid:aem:11595f35-e67e-4ee7-95f7-49c6a96858e0/as/EID-8e833cbe8c9c4a36b3f2dd841579af44b73103ed.jpg?preferwebp=true&height=630"
                    alt="Product 5">
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
                    <span class="price_num">SAR 244.99</span>
                </div>
            </div>

            <div class="button">
                <button class="add-to-bag">Add to Bag</button>
            </div>
                         <button class="wishlist-heart" data-product-id="Sweatshirt5-1" onclick="toggleWishlist(this)">
                <i class="bx bx-heart"></i>
            </button>
        </div>


        <div class="product-card">
            <div class="shoe-details">
                   <img src="https://image.hm.com/assets/hm/34/fc/34fc957c3f38933d7deedf3efbe78f4a7d9e9ccf.jpg?imwidth=2160"
                 alt="Product 6">
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
                    <span class="price_num">SAR 229.99</span>
                </div>
            </div>

            <div class="button">
                <button class="add-to-bag">Add to Bag</button>
            </div>
                         <button class="wishlist-heart" data-product-id="Jacket6-1" onclick="toggleWishlist(this)">
                <i class="bx bx-heart"></i>
            </button>
        </div>


        <div class="product-card">
            <div class="shoe-details">
               <img src="https://image.hm.com/assets/hm/d9/6a/d96a7e749ff8cb6428fc0420c757ef9486c7b9af.jpg?imwidth=820"
                    alt="Product 7">
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
                         <button class="wishlist-heart" data-product-id="Hoodie3-1" onclick="toggleWishlist(this)">
                <i class="bx bx-heart"></i>
            </button>
        </div>


        <div class="product-card">
            <div class="shoe-details">
                 <img src="	https://image.hm.com/assets/hm/6e/03/6e0330770d9eb458afef704e30256e7fcd4176d7.jpg?imwidth=2160"
                    alt="Product 8">
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
                         <button class="wishlist-heart" data-product-id="Jacket8-1" onclick="toggleWishlist(this)">
                <i class="bx bx-heart"></i>
            </button>
        </div>
    </div>

    <div class="pagination-container">
        <a href="Men.php" class="pagination-item">
            <img src="https://f.nooncdn.com/s/app/com/sivvi/design-system/icons-v2/chevron-back.svg" alt="chevronBack"
                class="pagination-icon" loading="eager">
        </a>
        
        <a href="Men.php" class="pagination-item">1</a>
        <a href="Men2.php" class="pagination-item">2</a>
        <span class="ellipsis">...</span>
        <a href="Men3.php" class="pagination-item">3</a>

        <a href="Men3.php" class="pagination-item">
            <img src="https://f.nooncdn.com/s/app/com/sivvi/design-system/icons-v2/chevron-forward.svg"
                alt="chevronForward" class="pagination-icon" loading="eager">
        </a>
    </div>
    
    <div class="c-footer" title="back to top">
        <button id="backtotop">&#8593;</button>
    </div>
    
</body>