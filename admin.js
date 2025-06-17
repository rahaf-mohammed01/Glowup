// Tab functionality
function showTab(tabName) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => {
        content.classList.remove('active');
    });

    // Remove active class from all tabs
    const tabs = document.querySelectorAll('.tab');
    tabs.forEach(tab => {
        tab.classList.remove('active');
    });

    // Show selected tab content
    document.getElementById(tabName).classList.add('active');

    // Add active class to clicked tab
    event.target.classList.add('active');
}

// Fixed Edit Product Modal Functions
function editProduct(id, name, description, price, stock, category) {
    console.log('Edit product called with:', {id, name, description, price, stock, category});
    
    try {
        // Set form values
        document.getElementById('edit_product_id').value = id;
        document.getElementById('edit_product_name').value = name;
        document.getElementById('edit_product_description').value = description;
        document.getElementById('edit_price').value = parseFloat(price) || 0;
        document.getElementById('edit_stock').value = parseInt(stock) || 0;
        document.getElementById('edit_category').value = category;
        
        // Show modal
        const modal = document.getElementById('editProductModal');
        if (modal) {
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
            console.log('Modal opened successfully');
        } else {
            console.error('Modal element not found');
        }
        
    } catch (error) {
        console.error('Error in editProduct function:', error);
        alert('Error opening edit form. Please check the console for details.');
    }
}

function closeEditModal() {
    const modal = document.getElementById('editProductModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    const modal = document.getElementById('editProductModal');
    if (event.target === modal) {
        closeEditModal();
    }
}

// Handle escape key to close modal
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('editProductModal');
        if (modal && modal.style.display !== 'none') {
            closeEditModal();
        }
    }
});


// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#ff4444';
                    isValid = false;
                } else {
                    field.style.borderColor = '';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });
});

// Confirmation dialogs for dangerous actions
function confirmDelete(type, name) {
    return confirm(`Are you sure you want to delete this ${type}? This action cannot be undone.\n\nItem: ${name}`);
}

// Real-time stock status updates
function updateStockStatus() {
    const stockCells = document.querySelectorAll('[data-stock]');
    stockCells.forEach(cell => {
        const stock = parseInt(cell.dataset.stock);
        const row = cell.closest('tr');
        
        // Remove existing classes
        row.classList.remove('out-of-stock', 'low-stock');
        
        // Add appropriate class
        if (stock === 0) {
            row.classList.add('out-of-stock');
        } else if (stock <= 10) {
            row.classList.add('low-stock');
        }
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateStockStatus();
    
    // Debug modal functionality
    console.log('DOM loaded, checking for modal...');
    const modal = document.getElementById('editProductModal');
    console.log('Modal found:', !!modal);
    
    // Test if edit buttons exist
    const editButtons = document.querySelectorAll('button[onclick*="editProduct"]');
    console.log('Edit buttons found:', editButtons.length);
    
    // Add event listeners to edit buttons as fallback
    editButtons.forEach((button, index) => {
        console.log(`Edit button ${index}:`, button.getAttribute('onclick'));
    });
});
// Add this at the end of your scriptadmin.js file
function debugEditFunction() {
    console.log('Testing edit modal...');
    const modal = document.getElementById('editProductModal');
    console.log('Modal element:', modal);
    
    if (modal) {
        modal.style.display = 'block';
        console.log('Modal should be visible now');
    } else {
        console.error('Modal not found in DOM');
    }
}
// JavaScript functions for the edit product modal
function editProduct(id, name, description, price, stock, category) {
    try {
        console.log('Editing product:', { id, name, description, price, stock, category });
        
        // Get modal elements
        const modal = document.getElementById('editProductModal');
        if (!modal) {
            console.error('Edit modal not found!');
            alert('Edit modal not found! Please make sure the modal HTML is included.');
            return;
        }

        // Populate form fields
        document.getElementById('edit_product_id').value = id;
        document.getElementById('edit_product_name').value = name || '';
        document.getElementById('edit_product_description').value = description || '';
        document.getElementById('edit_price').value = price || '';
        document.getElementById('edit_stock').value = stock || '';
        
        // Set category dropdown
        const categorySelect = document.getElementById('edit_category');
        if (categorySelect) {
            categorySelect.value = category || '';
        }

        // Show modal
        modal.style.display = 'block';
        
    } catch (error) {
        console.error('Error in editProduct function:', error);
        alert('Error opening edit form: ' + error.message);
    }
}

function closeEditModal() {
    const modal = document.getElementById('editProductModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('editProductModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}

// Tab functionality
function showTab(tabName) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => {
        content.classList.remove('active');
    });
    
    // Remove active class from all tabs
    const tabs = document.querySelectorAll('.tab');
    tabs.forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Show selected tab content
    const selectedTab = document.getElementById(tabName);
    if (selectedTab) {
        selectedTab.classList.add('active');
    }
    
    // Add active class to clicked tab
    event.target.classList.add('active');
}


