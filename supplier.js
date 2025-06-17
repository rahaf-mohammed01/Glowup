
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

        function updateOrderStatus(orderId, status) {
            if (confirm(`Update order #${orderId} to ${status}?`)) {
                // You can implement AJAX call here or redirect to update form
                document.getElementById('order_id').value = orderId;
                document.getElementById('status').value = status;
                alert(`Order #${orderId} status will be updated to ${status}`);
            }
        }

        function viewOrderDetails(orderId) {
            // Mock order details
            const orderDetails = `
                <p><strong>Order ID:</strong> #${orderId}</p>
                <p><strong>Customer:</strong> John Smith</p>
                <p><strong>Email:</strong> john.smith@email.com</p>
                <p><strong>Phone:</strong> +1-555-0123</p>
                <p><strong>Address:</strong> 123 Main St, City, State 12345</p>
                <p><strong>Items:</strong></p>
                <ul>
                    <li>Blue Jeans - Size M - Qty: 2</li>
                    <li>White T-Shirt - Size L - Qty: 1</li>
                </ul>
                <p><strong>Total:</strong> $149.99</p>
                <p><strong>Order Date:</strong> 2024-12-15</p>
            `;
            
            document.getElementById('orderDetails').innerHTML = orderDetails;
            document.getElementById('orderModal').style.display = 'block';
        }

        function processReturn(returnId) {
            if (confirm(`Process return #${returnId}?`)) {
                document.getElementById('return_id').value = returnId;
                alert(`Return #${returnId} will be processed`);
            }
        }

        function viewReturnDetails(returnId) {
            // Mock return details
            const returnDetails = `
                <p><strong>Return ID:</strong> #${returnId}</p>
                <p><strong>Original Order:</strong> #995</p>
                <p><strong>Customer:</strong> Emma Wilson</p>
                <p><strong>Reason:</strong> Size Issue</p>
                <p><strong>Items to Return:</strong></p>
                <ul>
                    <li>Blue Dress - Size M - Reason: Too small</li>
                </ul>
                <p><strong>Return Date:</strong> 2024-12-14</p>
                <p><strong>Customer Notes:</strong> Ordered medium but fits like small</p>
            `;
            
            document.getElementById('returnDetails').innerHTML = returnDetails;
            document.getElementById('returnModal').style.display = 'block';
        }

        function resolveIssue(issueId) {
            if (confirm(`Mark issue #${issueId} as resolved?`)) {
                document.getElementById('issue_id').value = issueId;
                alert(`Issue #${issueId} will be resolved`);
            }
        }

        function contactCustomer(issueId) {
            alert(`Contact customer for issue #${issueId}`);
            // You can implement customer contact functionality here
        }

        function updateDeliveryStatus(trackingNumber) {
            document.getElementById('tracking_id').value = trackingNumber;
            alert(`Update delivery status for tracking #${trackingNumber}`);
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }

        // Auto-fill tracking number with current date
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date();
            const tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 3); // Default 3 days for delivery
            
            const deliveryDate = tomorrow.toISOString().split('T')[0];
            document.getElementById('estimated_delivery').value = deliveryDate;
        });

        // Form validation
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.style.borderColor = '#d9534f';
                    } else {
                        field.style.borderColor = '#ddd';
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    alert('Please fill in all required fields.');
                }
            });
        });

        // Real-time search/filter functionality
        function filterTable(tableId, searchTerm) {
            const table = document.getElementById(tableId);
            if (!table) return;
            
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm.toLowerCase())) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Add search boxes to tables (optional enhancement)
        function addSearchToTables() {
            const tables = document.querySelectorAll('.data-table');
            tables.forEach((table, index) => {
                const searchBox = document.createElement('input');
                searchBox.type = 'text';
                searchBox.placeholder = 'Search...';
                searchBox.style.marginBottom = '10px';
                searchBox.style.padding = '8px';
                searchBox.style.border = '1px solid #ddd';
                searchBox.style.borderRadius = '4px';
                
                searchBox.addEventListener('input', function() {
                    filterTable(`table-${index}`, this.value);
                });
                
                table.id = `table-${index}`;
                table.parentNode.insertBefore(searchBox, table);
            });
        }

        // Initialize search functionality
        // addSearchToTables(); // Uncomment to enable search boxes
        
        // JavaScript functions for modal handling and other interactions
        function showTab(tabName) {
            // Hide all tab contents
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => tab.classList.remove('active'));
            
            // Remove active class from all tab buttons
            const tabButtons = document.querySelectorAll('.tab');
            tabButtons.forEach(button => button.classList.remove('active'));
            
            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            
            // Add active class to clicked button
            event.target.classList.add('active');
        }

        function editProduct(productId) {
            // You would typically fetch product data via AJAX here
            // For now, just show the modal
            document.getElementById('editProductModal').style.display = 'block';
            document.getElementById('edit_product_id').value = productId;
        }

        function deleteProduct(productId) {
            document.getElementById('deleteModal').style.display = 'block';
            document.getElementById('delete_product_id').value = productId;
        }

        function closeEditModal() {
            document.getElementById('editProductModal').style.display = 'none';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        function updateOrderStatus(orderId, status) {
            // Auto-fill the order status form
            document.getElementById('order_id').value = orderId;
            document.getElementById('status').value = status;
            
            // Optionally scroll to the form
            document.getElementById('order_id').scrollIntoView({ behavior: 'smooth' });
        }

        function processReturn(returnId) {
            // Auto-fill the return processing form
            document.getElementById('return_id').value = returnId;
            
            // Scroll to the form
            document.getElementById('return_id').scrollIntoView({ behavior: 'smooth' });
        }

        function resolveIssue(issueId) {
            // Auto-fill the issue resolution form
            document.getElementById('issue_id').value = issueId;
            
            // Scroll to the form
            document.getElementById('issue_id').scrollIntoView({ behavior: 'smooth' });
        }

        function updateInventory(productId) {
            // Auto-fill the inventory update form
            document.getElementById('inventory_product_id').value = productId;
            document.getElementById('inventory_action').value = 'add';
            
            // Scroll to the form
            document.getElementById('inventory_product_id').scrollIntoView({ behavior: 'smooth' });
        }

        function viewOrderDetails(orderId) {
            alert('Order details for Order #' + orderId + ' - This would typically open a detailed view');
        }

        function viewReturnDetails(returnId) {
            alert('Return details for Return #' + returnId + ' - This would typically open a detailed view');
        }

        function viewIssueDetails(issueId) {
            alert('Issue details for Issue #' + issueId + ' - This would typically open a detailed view');
        }

        function updateDeliveryStatus(trackingNumber) {
            // Auto-fill the delivery status form
            document.getElementById('tracking_id').value = trackingNumber;
            
            // Scroll to the form
            document.getElementById('tracking_id').scrollIntoView({ behavior: 'smooth' });
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const editModal = document.getElementById('editProductModal');
            const deleteModal = document.getElementById('deleteModal');
            
            if (event.target == editModal) {
                editModal.style.display = 'none';
            }
            if (event.target == deleteModal) {
                deleteModal.style.display = 'none';
            }
        }

        // Auto-hide messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const messageElement = document.querySelector('.message');
            if (messageElement) {
                setTimeout(function() {
                    messageElement.style.opacity = '0';
                    setTimeout(function() {
                        messageElement.style.display = 'none';
                    }, 300);
                }, 5000);
            }
        });
