document.addEventListener("DOMContentLoaded", function() {
    console.log("Wishlist page loaded");
    
    // Load wishlist items from server on page load
    loadWishlistItems();
    
    // Back to top functionality
    const backToTopButton = document.getElementById('backtotop');
    if (backToTopButton) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 200) {
                backToTopButton.style.display = 'block';
            } else {
                backToTopButton.style.display = 'none';
            }
        });
    }
});

// Load wishlist items from server
async function loadWishlistItems() {
    console.log("Loading wishlist items...");
    
    try {
        const response = await fetch('Wishlist.php?ajax=1');
        console.log("Response status:", response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log("Received data:", data);
        
        if (data.status === 'success') {
            displayWishlistItems(data.items || []);
            updateWishlistCounter(data.count || 0);
        } else {
            console.warn('Server response:', data);
            showEmptyState();
        }
    } catch (error) {
        console.error('Error loading wishlist:', error);
        showEmptyState();
    }
}

// Display wishlist items dynamically
function displayWishlistItems(items) {
    console.log("Displaying items:", items);
    
    const container = document.querySelector('.wishlist-container');
    const emptyState = document.getElementById('emptyWishlist');
    
    if (!container) {
        console.error('Wishlist container not found');
        return;
    }
    
    // Clear existing content
    container.innerHTML = '';
    
    if (items.length === 0) {
        showEmptyState();
        return;
    }
    
    container.style.display = 'grid';
    if (emptyState) emptyState.style.display = 'none';
    
    // Create cards for each item
    items.forEach(item => {
        const productCard = createProductCard(item);
        container.appendChild(productCard);
    });
}

// Create product card dynamically
function createProductCard(item) {
    const productCard = document.createElement('div');
    productCard.className = 'product-card';
    
    // Handle image path - check if it's a full URL or needs uploads/ prefix
    let imageSrc = item.image || 'https://via.placeholder.com/300x300';
    if (item.image && !item.image.startsWith('http') && !item.image.startsWith('uploads/')) {
        imageSrc = 'uploads/' + item.image;
    }
    
    productCard.innerHTML = `
        <button type="button" class="wishlist-heart" data-product-id="${item.id}" onclick="removeFromWishlist(this, event);">
            <i class='bx bxs-heart'></i>
        </button>
        <div class="shoe-details">
            <img src="${imageSrc}" alt="${item.name}" onerror="this.src='https://via.placeholder.com/300x300'">
            <span class="shoe-name">${item.name}</span>
            <p>${item.description || item.product_description || 'No description available'}</p>
        </div>
        <div class="color-size-price">
            <div class="color-option">
                <span class="color">Color:</span>
                <div class="circles">
                    <span class="circle beige ${item.color === 'beige' ? 'active' : ''}" onclick="selectColor(this)"></span>
                    <span class="circle black ${item.color === 'black' ? 'active' : ''}" onclick="selectColor(this)"></span>
                    <span class="circle brown ${item.color === 'brown' ? 'active' : ''}" onclick="selectColor(this)"></span>
                </div>
            </div>
            <div class="size-option">
                <span class="size">Size:</span>
                <div class="sizes">
                    <span class="size-btn ${item.size === 'XS' ? 'active' : ''}" onclick="selectSize(this)">XS</span>
                    <span class="size-btn ${item.size === 'S' ? 'active' : ''}" onclick="selectSize(this)">S</span>
                    <span class="size-btn ${item.size === 'M' ? 'active' : ''}" onclick="selectSize(this)">M</span>
                    <span class="size-btn ${item.size === 'L' ? 'active' : ''}" onclick="selectSize(this)">L</span>
                    <span class="size-btn ${item.size === 'XL' ? 'active' : ''}" onclick="selectSize(this)">XL</span>
                </div>
            </div>
        </div>
        <div class="price">SAR ${item.price}</div>
        <div class="wishlist-actions">
            <button type="button" class="add-to-bag" onclick="addToBag(this, event);">Add to Bag</button>
            <button type="button" class="remove-from-wishlist" data-product-id="${item.id}" onclick="removeFromWishlist(this, event);" title="Remove from Wishlist">
                <i class='bx bx-trash'></i>
            </button>
        </div>
    `;
    
    return productCard;
}

// Show empty state
function showEmptyState() {
    console.log("Showing empty state");
    
    const container = document.querySelector('.wishlist-container');
    const emptyState = document.getElementById('emptyWishlist');
    
    if (container) container.style.display = 'none';
    if (emptyState) emptyState.style.display = 'block';
    
    updateWishlistCounter(0);
}

// Update wishlist counter in navigation
function updateWishlistCounter(count) {
    const counter = document.querySelector('.wishlist-counter');
    if (counter) {
        counter.textContent = count;
        counter.style.display = count > 0 ? 'inline' : 'none';
    }
    console.log("Updated wishlist counter to:", count);
}

// Remove from wishlist function
async function removeFromWishlist(button, event) {
    // Prevent any default behavior or form submission
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    try {
        const productId = button.getAttribute('data-product-id');
        
        if (!productId) {
            console.error('No product ID found');
            showNotification('Error: Product ID not found', 'error');
            return false;
        }

        console.log("Removing product ID:", productId);

        // Show loading state
        const originalContent = button.innerHTML;
        button.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i>';
        button.disabled = true;

        const response = await fetch('Wishlist.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'remove',
                productId: productId
            })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log("Remove response:", data);
        
        if (data.status === 'removed') {
            // Remove the product card from DOM with animation
            const productCard = button.closest('.product-card');
            if (productCard) {
                productCard.style.animation = 'fadeOut 0.3s ease-out';
                setTimeout(() => {
                    productCard.remove();
                    
                    // Check if wishlist is now empty
                    const remainingCards = document.querySelectorAll('.wishlist-container .product-card');
                    if (remainingCards.length === 0) {
                        showEmptyState();
                    }
                    
                    // Update counter with remaining items
                    updateWishlistCounter(remainingCards.length);
                }, 300);
            }
            
            showNotification('Item removed from wishlist!', 'info');
        } else {
            showNotification(data.message || 'Failed to remove item', 'error');
            // Reset button state on error
            button.innerHTML = originalContent;
            button.disabled = false;
        }

    } catch (error) {
        console.error('Error removing from wishlist:', error);
        showNotification('Error removing item from wishlist', 'error');
        // Reset button state on error
        button.innerHTML = originalContent;
        button.disabled = false;
    }
    
    return false; // Prevent any default behavior
}

// Add to bag function
async function addToBag(button, event) {
    // Prevent any default behavior or form submission
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    const productCard = button.closest('.product-card');
    
    if (!productCard) {
        showNotification('Error: Product information not found', 'error');
        return false;
    }
    
    const nameElement = productCard.querySelector('.shoe-name');
    const priceElement = productCard.querySelector('.price');
    const imageElement = productCard.querySelector('img');
    const selectedColor = productCard.querySelector('.color-option .circle.active');
    const selectedSize = productCard.querySelector('.size-option .size-btn.active');

    if (!nameElement || !priceElement || !imageElement) {
        showNotification('Error: Missing product information', 'error');
        return false;
    }

    if (!selectedColor || !selectedSize) {
        showNotification('Please select color and size', 'error');
        return false;
    }

    // Disable button and show loading state
    button.disabled = true;
    const originalText = button.textContent;
    button.textContent = 'Adding...';

    try {
        const formData = new FormData();
        formData.append('name', nameElement.textContent.trim());
        formData.append('price', priceElement.textContent.trim().replace(/[^\d.]/g, '')); // Remove currency symbols
        formData.append('image', imageElement.src);
        formData.append('color', getColorName(selectedColor));
        formData.append('size', selectedSize.textContent.trim());

        const response = await fetch('addToShoppingBag.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        
        if (data.status === 'success') {
            button.textContent = 'Added!';
            button.style.backgroundColor = '#4CAF50';
            showNotification('Item added to shopping bag!', 'success');
            
            setTimeout(() => {
                button.disabled = false;
                button.textContent = originalText;
                button.style.backgroundColor = '';
            }, 2000);
        } else {
            throw new Error(data.message || 'Failed to add item');
        }
    } catch (error) {
        console.error('Error adding to bag:', error);
        showNotification('Error adding item to bag', 'error');
        button.disabled = false;
        button.textContent = originalText;
    }
    
    return false; // Prevent any default behavior
}

// Size selection function - Fixed
function selectSize(element) {
    // Remove active class from all size buttons in the same container
    const sizeContainer = element.closest('.size-option');
    const sizeOptions = sizeContainer.querySelectorAll('.size-btn');
    sizeOptions.forEach(option => option.classList.remove('active'));
    
    // Add active class to clicked element
    element.classList.add('active');
    
    console.log('Size selected:', element.textContent.trim());
}

// Color selection function - Also cleaned up for consistency
function selectColor(element) {
    // Remove active class from all color circles in the same container
    const colorContainer = element.closest('.color-option');
    const colorOptions = colorContainer.querySelectorAll('.circle');
    colorOptions.forEach(option => option.classList.remove('active'));
    
    // Add active class to clicked element
    element.classList.add('active');
    
    console.log('Color selected:', getColorName(element));
}

// Helper function to get color name
function getColorName(colorElement) {
    if (colorElement.classList.contains('beige')) return 'beige';
    if (colorElement.classList.contains('black')) return 'black';
    if (colorElement.classList.contains('brown')) return 'brown';
    return 'beige'; // default
}

// Updated validation function for add to bag
function validateSelection(productCard) {
    const selectedColor = productCard.querySelector('.color-option .circle.active');
    const selectedSize = productCard.querySelector('.size-option .size-btn.active');
    
    if (!selectedColor) {
        showNotification('Please select a color', 'warning');
        return false;
    }
    
    if (!selectedSize) {
        showNotification('Please select a size', 'warning');
        return false;
    }
    
    return true;
}
// Updated showNotification function
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

// Add CSS for fade out animation
if (!document.querySelector('#wishlist-animations')) {
    const style = document.createElement('style');
    style.id = 'wishlist-animations';
    style.textContent = `
        @keyframes fadeOut {
            from { opacity: 1; transform: scale(1); }
            to { opacity: 0; transform: scale(0.8); }
        }
        
        .size-btn {
            cursor: pointer;
            padding: 5px 10px;
            margin: 2px;
            border: 1px solid #ddd;
            border-radius: 3px;
            display: inline-block;
            transition: all 0.2s;
        }
        
        .size-btn:hover {
            background-color: #f0f0f0;
        }
        
        .size-btn.active {
            background-color: #b07154;
            color: white;
            border-color: #b07154;
        }
    `;
    document.head.appendChild(style);
}

// Make functions globally available
window.removeFromWishlist = removeFromWishlist;
window.addToBag = addToBag;
window.selectColor = selectColor;
window.selectSize = selectSize;
window.scrollToTop = scrollToTop;
window.updateWishlistCounter = updateWishlistCounter;