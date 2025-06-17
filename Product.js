// Product page functionality for Men.php

document.addEventListener('DOMContentLoaded', function() {
    // Color selection functionality
    document.querySelectorAll('.circles .circle').forEach(function(circle) {
        circle.addEventListener('click', function() {
            // Remove active class from all circles in this product
            const productCard = this.closest('.product-card');
            productCard.querySelectorAll('.circle').forEach(function(c) {
                c.classList.remove('active');
            });
            // Add active class to clicked circle
            this.classList.add('active');
        });
    });

    // Size selection functionality
    document.querySelectorAll('.sizes .size-option').forEach(function(sizeOption) {
        sizeOption.addEventListener('click', function() {
            // Remove active class from all sizes in this product
            const productCard = this.closest('.product-card');
            productCard.querySelectorAll('.size-option').forEach(function(s) {
                s.classList.remove('active');
            });
            // Add active class to clicked size
            this.classList.add('active');
        });
    });

    // Add to bag functionality
    document.querySelectorAll('.add-to-bag').forEach(function(button) {
        button.addEventListener('click', function() {
            const productCard = this.closest('.product-card');
            
            // Get product information
            const name = productCard.querySelector('.shoe-name').textContent.trim();
            const image = productCard.querySelector('img').src;
            const priceText = productCard.querySelector('.price_num').textContent.trim();
            
            // Get selected color
            const activeColor = productCard.querySelector('.circle.active');
            if (!activeColor) {
                showNotification('Please select a color', 'error');
                return;
            }
            let color = '';
            if (activeColor.classList.contains('beige')) color = 'Beige';
            else if (activeColor.classList.contains('black')) color = 'Black';
            else if (activeColor.classList.contains('brown')) color = 'Brown';
            
            // Get selected size
            const activeSize = productCard.querySelector('.size-option.active');
            if (!activeSize) {
                showNotification('Please select a size', 'error');
                return;
            }
            const size = activeSize.textContent.trim();
            
            // Show loading state
            const originalText = this.textContent;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
            this.disabled = true;
            
            // Send data to server
            const formData = new FormData();
            formData.append('name', name);
            formData.append('image', image);
            formData.append('price', priceText);
            formData.append('color', color);
            formData.append('size', size);
            
            fetch('addToShoppingBag.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showNotification('Item added to cart successfully!', 'success');
                    // Update cart count if you have a cart counter in your UI
                    updateCartCount(data.cart_count);
                } else {
                    showNotification(data.message || 'Failed to add item to cart', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Network error. Please try again.', 'error');
            })
            .finally(() => {
                // Reset button
                this.innerHTML = originalText;
                this.disabled = false;
            });
        });
    });

    // Back to top functionality
    const backToTopBtn = document.getElementById('backtotop');
    if (backToTopBtn) {
        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Show/hide back to top button based on scroll position
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopBtn.style.display = 'block';
            } else {
                backToTopBtn.style.display = 'none';
            }
        });
    }
});

// Utility functions
function showNotification(message, type) {
    // Remove any existing notifications
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }

    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#28a745' : '#dc3545'};
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        z-index: 10000;
        transform: translateX(400px);
        transition: transform 0.3s ease;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        font-family: Arial, sans-serif;
        max-width: 300px;
    `;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        ${message}
    `;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);

    setTimeout(() => {
        notification.style.transform = 'translateX(400px)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

function updateCartCount(count) {
    // Update cart counter in navigation if it exists
    const cartCounter = document.querySelector('.cart-counter');
    if (cartCounter) {
        cartCounter.textContent = count;
        cartCounter.style.display = count > 0 ? 'inline' : 'none';
    }
}

// Wishlist functionality (if toggleWishlist function is not defined elsewhere)
function toggleWishlist(button) {
    const heartIcon = button.querySelector('i');
    const productId = button.getAttribute('data-product-id');
    
    if (heartIcon.classList.contains('bx-heart')) {
        // Add to wishlist
        heartIcon.classList.remove('bx-heart');
        heartIcon.classList.add('bxs-heart');
        button.style.color = '#e74c3c';
        showNotification('Added to wishlist', 'success');
        
        // Here you would typically send an AJAX request to add to wishlist
        // addToWishlist(productId);
    } else {
        // Remove from wishlist
        heartIcon.classList.remove('bxs-heart');
        heartIcon.classList.add('bx-heart');
        button.style.color = '#666';
        showNotification('Removed from wishlist', 'success');
        
        // Here you would typically send an AJAX request to remove from wishlist
        // removeFromWishlist(productId);
    }
}
  function showTab(tabName) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => content.classList.remove('active'));

            // Remove active class from all tabs
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => tab.classList.remove('active'));

            // Show selected tab content
            document.getElementById(tabName).classList.add('active');

            // Add active class to clicked tab
            event.target.classList.add('active');
        }

        function editProduct(productId) {
            // You can implement modal or redirect to edit page
            const newName = prompt('Enter new product name:');
            if (newName) {
                // Create a form to submit the edit
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="edit_product">
                    <input type="hidden" name="product_id" value="${productId}">
                    <input type="hidden" name="product_name" value="${newName}">
                    <input type="hidden" name="product_description" value="Updated product">
                    <input type="hidden" name="price" value="0">
                    <input type="hidden" name="stock" value="0">
                    <input type="hidden" name="category" value="Women">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function viewOrderDetails(orderId) {
            alert(`View order details for Order #${orderId}\n\nThis would typically open a detailed order view with:\n- Order items\n- Customer information\n- Shipping details\n- Payment information\n- Order history`);
        }

        // Auto-refresh page every 5 minutes for real-time updates
        setTimeout(function() {
            location.reload();
        }, 300000);

        // Add confirmation for critical actions
        document.addEventListener('DOMContentLoaded', function() {
            const dangerButtons = document.querySelectorAll('.btn-danger');
            dangerButtons.forEach(button => {
                if (!button.onclick) {
                    button.addEventListener('click', function(e) {
                        if (!confirm('Are you sure you want to perform this action?')) {
                            e.preventDefault();
                        }
                    });
                }
            });
        });
// Fixed toggleWishlist function for script4.js
async function toggleWishlist(button) {
    try {
        // Get product data from button attributes
        const productData = {
            id: button.getAttribute('data-product-id'),
            name: button.getAttribute('data-product-name'),
            price: button.getAttribute('data-product-price'),
            image: button.getAttribute('data-product-image'),
            description: button.getAttribute('data-product-description'),
            color: getSelectedColor(button) || 'beige',
            size: getSelectedSize(button) || 'M'
        };

        // Debug logging
        console.log('Product data:', productData);

        // Validate required data
        if (!productData.id || !productData.name) {
            showNotification('Error: Missing product information', 'error');
            return;
        }

        // Show loading state
        const icon = button.querySelector('i');
        const originalClass = icon.className;
        icon.className = 'bx bx-loader-alt bx-spin';
        button.disabled = true;

        // Send request to Wishlist.php (not Women.php)
        const response = await fetch('Wishlist.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'toggle',
                product: productData
            })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log('Response data:', data);
        
        if (data.status === 'added') {
            icon.className = 'bx bxs-heart'; // Filled heart
            button.style.color = '#e74c3c';
            showNotification('Added to wishlist!', 'success');
        } else if (data.status === 'removed') {
            icon.className = 'bx bx-heart'; // Empty heart
            button.style.color = '';
            showNotification('Removed from wishlist!', 'info');
        } else {
            throw new Error(data.message || 'Unknown error');
        }

    } catch (error) {
        console.error('Error toggling wishlist:', error);
        showNotification('Error updating wishlist', 'error');
        
        // Reset button state
        const icon = button.querySelector('i');
        icon.className = 'bx bx-heart';
        button.style.color = '';
    } finally {
        button.disabled = false;
    }
}

// Fixed helper function to get selected color
function getSelectedColor(button) {
    const productCard = button.closest('.product-card');
    const selectedColor = productCard.querySelector('.circles .circle.active');
    
    if (selectedColor) {
        if (selectedColor.classList.contains('beige')) return 'beige';
        if (selectedColor.classList.contains('black')) return 'black';
        if (selectedColor.classList.contains('brown')) return 'brown';
    }
    return 'beige'; // Default color
}

// Fixed helper function to get selected size
function getSelectedSize(button) {
    const productCard = button.closest('.product-card');
    const selectedSize = productCard.querySelector('.sizes .size-option.active');
    
    return selectedSize ? selectedSize.textContent.trim() : 'M'; // Default size
}

// Updated showNotification function (make sure this exists in script4.js)
function showNotification(message, type = 'info') {
    // Remove existing notification
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    
    const colors = {
        success: '#4CAF50',
        error: '#f44336',
        info: '#2196F3',
        warning: '#ff9800'
    };
    
    const icons = {
        success: 'bx-check-circle',
        error: 'bx-x-circle',
        info: 'bx-info-circle',
        warning: 'bx-error-circle'
    };
    
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: ${colors[type] || colors.info};
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        z-index: 10000;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transform: translateX(400px);
        transition: transform 0.3s ease;
        max-width: 300px;
        word-wrap: break-word;
    `;
    
    notification.innerHTML = `
        <i class="bx ${icons[type] || icons.info}"></i>
        ${message}
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Animate out and remove
    setTimeout(() => {
        notification.style.transform = 'translateX(400px)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 3000);
}

// Scroll to top function
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}
  let products = [];
        let currentFilter = 'all';
        let searchTimeout;

        const searchInput = document.getElementById('searchInput');
        const searchResults = document.getElementById('searchResults');
        const productGrid = document.getElementById('productGrid');
        const resultsCounter = document.getElementById('resultsCounter');

        // Initialize products from current page
        function extractProductsFromPage() {
            const productElements = document.querySelectorAll('.product-card');
            
            return Array.from(productElements).map((element, index) => {
                const titleEl = element.querySelector('.shoe-name');
                const descEl = element.querySelector('.shoe-details p');
                const priceEl = element.querySelector('.price_num');
                const imgEl = element.querySelector('.shoe-details img');
                const category = element.getAttribute('data-category') || '';
                
           return {
                    id: index,
                    title: titleEl ? titleEl.textContent.trim() : '',
                    description: descEl ? descEl.textContent.trim() : '',
                    price: priceEl ? priceEl.textContent.trim() : '',
                    image: imgEl ? imgEl.src : '',
                    category: category,
                    element: element
                };
            });
        }

        // Initialize products on page load
        function initializeProducts() {
            products = extractProductsFromPage();
            updateResultsCounter();
        }

        // Search functionality
        function searchProducts(query) {
            if (!query.trim()) {
                return products;
            }

            const searchTerm = query.toLowerCase();
            return products.filter(product => {
                return product.title.toLowerCase().includes(searchTerm) ||
                       product.description.toLowerCase().includes(searchTerm) ||
                       product.category.toLowerCase().includes(searchTerm);
            });
        }

        // Highlight search terms in text
        function highlightSearchTerm(text, searchTerm) {
            if (!searchTerm.trim()) return text;
            
            const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
            return text.replace(regex, '<span class="search-highlight">$1</span>');
        }

        // Display search results dropdown
        function displaySearchResults(results, searchTerm) {
            if (results.length === 0) {
                searchResults.innerHTML = '<div class="no-results">No products found</div>';
            } else {
                const resultsHTML = results.slice(0, 8).map(product => `
                    <div class="search-result-item" onclick="selectProduct(${product.id})">
                        <img src="${product.image}" alt="${product.title}" class="search-result-image">
                        <div class="search-result-content">
                            <div class="search-result-title">${highlightSearchTerm(product.title, searchTerm)}</div>
                            <div class="search-result-description">
                                <span>${highlightSearchTerm(product.description.substring(0, 50), searchTerm)}...</span>
                                <span>${product.price}</span>
                            </div>
                        </div>
                    </div>
                `).join('');
                
                searchResults.innerHTML = resultsHTML;
            }
            
            searchResults.classList.add('show');
        }

        // Select product from search results
        function selectProduct(productId) {
            const product = products.find(p => p.id === productId);
            if (product) {
                // Hide search results
                searchResults.classList.remove('show');
                searchInput.value = product.title;
                
                // Highlight the selected product
                document.querySelectorAll('.product-card').forEach(card => {
                    card.classList.remove('highlighted');
                });
                product.element.classList.add('highlighted');
                
                // Scroll to the product
                product.element.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
                
                // Clear search input after a delay
                setTimeout(() => {
                    searchInput.value = '';
                    product.element.classList.remove('highlighted');
                }, 3000);
            }
        }

        // Perform search and filter products
        function performSearch() {
            const query = searchInput.value.trim();
            if (!query) {
                showAllProducts();
                updateResultsCounter();
                return;
            }

            const results = searchProducts(query);
            
            // Hide all products first
            document.querySelectorAll('.product-card').forEach(card => {
                card.classList.add('hidden');
            });
            
            // Show matching products
            results.forEach(product => {
                product.element.classList.remove('hidden');
            });
            
            updateResultsCounter(results.length);
            searchResults.classList.remove('show');
        }

        // Filter products by category
        function filterProducts(category) {
            currentFilter = category;
            
            // Update active filter button
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Show/hide products based on category
            const productCards = document.querySelectorAll('.product-card');
            let visibleCount = 0;
            
            productCards.forEach(card => {
                const productCategory = card.getAttribute('data-category') || '';
                
                if (category === 'all' || productCategory.includes(category)) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });
            
            updateResultsCounter(visibleCount);
            
            // Clear search
            searchInput.value = '';
            searchResults.classList.remove('show');
        }

        // Show all products
        function showAllProducts() {
            document.querySelectorAll('.product-card').forEach(card => {
                card.classList.remove('hidden');
            });
        }

        // Update results counter
        function updateResultsCounter(count = null) {
            const visibleProducts = count !== null ? count : 
                document.querySelectorAll('.product-card:not(.hidden)').length;
            
            resultsCounter.textContent = `Showing ${visibleProducts} product${visibleProducts !== 1 ? 's' : ''}`;
        }

        // Real-time search as user types
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            // Clear previous timeout
            clearTimeout(searchTimeout);
            
            if (query.length === 0) {
                searchResults.classList.remove('show');
                showAllProducts();
                updateResultsCounter();
                return;
            }
            
            if (query.length < 2) {
                searchResults.classList.remove('show');
                return;
            }
            
            // Debounce search
            searchTimeout = setTimeout(() => {
                const results = searchProducts(query);
                displaySearchResults(results, query);
            }, 300);
        });

        // Handle search on Enter key
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch();
            }
        });

        // Hide search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-container')) {
                searchResults.classList.remove('show');
            }
        });

        // Color selection functionality
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('circle')) {
                // Remove active class from siblings
                e.target.parentNode.querySelectorAll('.circle').forEach(circle => {
                    circle.classList.remove('active');
                });
                // Add active class to clicked circle
                e.target.classList.add('active');
            }
        });

        // Size selection functionality
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('size-option')) {
                // Remove active class from siblings
                e.target.parentNode.querySelectorAll('.size-option').forEach(size => {
                    size.classList.remove('active');
                });
                // Add active class to clicked size
                e.target.classList.add('active');
            }
        });

   
           // Add to bag functionality
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('add-to-bag')) {
                const button = e.target;
                const originalText = button.textContent;
                
                // Visual feedback
                button.textContent = 'Added!';
                button.style.background = '#28a745';
                
                // Reset after 2 seconds
                setTimeout(() => {
                    button.textContent = originalText;
                    button.style.background = '';
                }, 2000);
            }
        });

        // Initialize everything when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initializeProducts();
            
            // Add some animation delays to product cards
            document.querySelectorAll('.product-card').forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });
        });

        // Add CSS animation for product cards entrance
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .product-card {
                animation: fadeInUp 0.6s ease forwards;
                opacity: 0;
            }
        `;
        document.head.appendChild(style);
