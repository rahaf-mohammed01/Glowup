// Shopping cart functionality for ShoppingBag.php

function updateQuantity(itemIndex, newQuantity) {
    if (newQuantity <= 0) {
        removeItem(itemIndex);
        return;
    }

    // Show loading state
    const cartItem = document.querySelector(`[data-id="${itemIndex}"]`);
    if (cartItem) {
        cartItem.classList.add('loading');
    }

    // Send AJAX request to update quantity
    const formData = new FormData();
    formData.append('action', 'update_quantity');
    formData.append('item_index', itemIndex);
    formData.append('quantity', newQuantity);

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update quantity display
                const quantityDisplay = document.getElementById(`quantity-${itemIndex}`);
                if (quantityDisplay) {
                    quantityDisplay.textContent = newQuantity;
                }

                // Update quantity buttons
                const minusBtn = cartItem.querySelector('.quantity-btn:first-child');
                if (minusBtn) {
                    minusBtn.disabled = newQuantity <= 1;
                    minusBtn.onclick = () => updateQuantity(itemIndex, newQuantity - 1);
                }

                const plusBtn = cartItem.querySelector('.quantity-btn:last-child');
                if (plusBtn) {
                    plusBtn.onclick = () => updateQuantity(itemIndex, newQuantity + 1);
                }

                // Update summary
                document.getElementById('subtotal').innerText = 'SAR ' + data.summary.subtotal.toFixed(2);
                document.getElementById('tax').innerText = 'SAR ' + data.summary.tax.toFixed(2);
                document.getElementById('total').innerText = 'SAR ' + data.summary.total.toFixed(2);


                showNotification('Quantity updated', 'success');
            } else {
                showNotification(data.message || 'Failed to update quantity', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Network error. Please try again.', 'error');
        })
        .finally(() => {
            if (cartItem) {
                cartItem.classList.remove('loading');
            }
        });
}

function removeItem(itemIndex) {
    // Show loading state
    const cartItem = document.querySelector(`[data-id="${itemIndex}"]`);
    if (cartItem) {
        cartItem.classList.add('loading');
    }

    // Send AJAX request to remove item
    const formData = new FormData();
    formData.append('action', 'remove_item');
    formData.append('item_index', itemIndex);

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Item removed from cart', 'success');

                // Since removing items affects indices, reload the page to refresh everything
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showNotification(data.message || 'Failed to remove item', 'error');
                if (cartItem) {
                    cartItem.classList.remove('loading');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Network error. Please try again.', 'error');
            if (cartItem) {
                cartItem.classList.remove('loading');
            }
        });
}

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

function proceedToCheckout() {
    const checkoutBtn = document.getElementById('checkoutBtn');
    checkoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    checkoutBtn.disabled = true;

    // Simulate processing
    setTimeout(() => {
        window.location.href = 'checkout.php';
    }, 2000);
}

// Listen for cart update events
window.addEventListener('cartUpdated', function () {
    // Refresh cart display or update cart count
    console.log('Cart was updated');
});

// Initialize page
document.addEventListener('DOMContentLoaded', function () {
    console.log('Shopping cart page loaded');

    // Add loading styles if not present
    const style = document.createElement('style');
    style.textContent = `
        .cart-item.loading {
            opacity: 0.6;
            pointer-events: none;
        }
        
        .cart-item.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .cart-item {
            position: relative;
        }
    `;
    document.head.appendChild(style);
});

function fetchCartState() {
    const formData = new URLSearchParams({
        action: 'get_cart'
    });

    fetch('ShoppingBag.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: formData.toString()
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Cart state:', data);
                // Update UI with current cart state if needed
            }
        })
        .catch(error => {
            console.error('Error fetching cart state:', error);
        });
}
// Scroll to top function
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}