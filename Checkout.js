let selectedPaymentMethod = '';

// Initialize on page load
document.addEventListener('DOMContentLoaded', function () {
    // Check if payment method is already selected (from PHP)
    const methodSelect = document.getElementById('method');
    if (methodSelect && methodSelect.value) {
        selectedPaymentMethod = methodSelect.value;
        updatePaymentDisplay();
    }

    // Initialize tooltips if Bootstrap is available
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Initial progress update
    updateProgress();
});

// Payment method selection
function selectPayment(method) {
    // Remove previous selections
    document.querySelectorAll('.payment-option').forEach(option => {
        option.classList.remove('selected');
    });
    document.querySelectorAll('.payment-option i.fa-check-circle').forEach(check => {
        check.style.display = 'none';
        check.classList.add('d-none');
    });

    // Add selection to clicked option
    const selectedOption = document.getElementById(`payment-${method}`);
    const checkIcon = document.getElementById(`check-${method}`);

    if (selectedOption && checkIcon) {
        selectedOption.classList.add('selected');
        checkIcon.style.display = 'block';
        checkIcon.classList.remove('d-none');
    }

    // Update hidden select
    const methodSelect = document.getElementById('method');
    if (methodSelect) {
        methodSelect.value = method;
    }
    selectedPaymentMethod = method;

    // Show/hide relevant fields
    updatePaymentDisplay();
    updateProgress();
}

function updatePaymentDisplay() {
    hideAllPaymentFields();

    if (selectedPaymentMethod === 'credit_card') {
        const creditFields = document.getElementById('creditCardFields');
        if (creditFields) creditFields.style.display = 'block';
    } else if (selectedPaymentMethod === 'Apple pay') {
        const appleFields = document.getElementById('applePayFields');
        if (appleFields) appleFields.style.display = 'block';
    } else if (selectedPaymentMethod === 'cash') {
        const cashFields = document.getElementById('cashFields');
        if (cashFields) cashFields.style.display = 'block';
    }
}

function hideAllPaymentFields() {
    const fields = ['creditCardFields', 'applePayFields', 'cashFields'];
    fields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) field.style.display = 'none';
    });
}

// Card number formatting
function formatCardNumber(input) {
    let value = input.value.replace(/\D/g, '');
    value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
    input.value = value;
}

// Expiry date formatting
function formatExpiryDate(input) {
    let value = input.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2, 4);
    }
    input.value = value;
}

// Progress tracking
function updateProgress() {
    const requiredFields = ['name', 'email', 'address', 'phone'];
    let filledFields = 0;
    let totalFields = requiredFields.length + 1; // +1 for payment method

    requiredFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field && field.value.trim() !== '') {
            filledFields++;
        }
    });

    if (selectedPaymentMethod) {
        filledFields++;
    }

    const progressPercent = Math.round((filledFields / totalFields) * 100);
    const progressBar = document.getElementById('progressBar');
    if (progressBar) {
        progressBar.style.width = progressPercent + '%';
    }

    // Update steps
    updateSteps(progressPercent);
}

function updateSteps(progress) {
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const step3 = document.getElementById('step3');

    if (!step1 || !step2 || !step3) return;

    // Reset all steps
    [step1, step2, step3].forEach(step => {
        step.classList.remove('active', 'completed');
    });

    if (progress < 40) {
        step1.classList.add('active');
    } else if (progress < 80) {
        step1.classList.add('completed');
        step2.classList.add('active');
    } else {
        step1.classList.add('completed');
        step2.classList.add('completed');
        step3.classList.add('active');
    }
}

// Form validation
function validateForm() {
    const requiredFields = ['name', 'email', 'address', 'phone'];
    let isValid = true;
    let errors = [];

    // Clear previous errors
    document.querySelectorAll('.error-message').forEach(error => {
        error.remove();
    });
    document.querySelectorAll('.is-invalid').forEach(field => {
        field.classList.remove('is-invalid');
    });

    // Check required fields
    requiredFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (!field || field.value.trim() === '') {
            isValid = false;
            errors.push(`${fieldId.charAt(0).toUpperCase() + fieldId.slice(1)} is required`);
            if (field) {
                field.classList.add('is-invalid');
                showFieldError(field, `${fieldId.charAt(0).toUpperCase() + fieldId.slice(1)} is required`);
            }
        }
    });

    // Validate email format
    const emailField = document.getElementById('email');
    if (emailField && emailField.value.trim() !== '') {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailField.value.trim())) {
            isValid = false;
            errors.push('Please enter a valid email address');
            emailField.classList.add('is-invalid');
            showFieldError(emailField, 'Please enter a valid email address');
        }
    }

    // Validate phone number
    const phoneField = document.getElementById('phone');
    if (phoneField && phoneField.value.trim() !== '') {
        const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
        if (!phoneRegex.test(phoneField.value.replace(/\s|-|\(|\)/g, ''))) {
            isValid = false;
            errors.push('Please enter a valid phone number');
            phoneField.classList.add('is-invalid');
            showFieldError(phoneField, 'Please enter a valid phone number');
        }
    }

    // Check payment method selection
    if (!selectedPaymentMethod) {
        isValid = false;
        errors.push('Please select a payment method');
        showNotification('Please select a payment method', 'warning');
    }

    // Validate payment method specific fields
    if (selectedPaymentMethod === 'credit_card') {
        isValid = validateCreditCard() && isValid;
    }

    return isValid;
}

function validateCreditCard() {
    let isValid = true;
    const cardNumber = document.getElementById('cardNumber');
    const expiryDate = document.getElementById('expiryDate');
    const cvv = document.getElementById('cvv');
    const cardName = document.getElementById('cardName');

    // Validate card number
    if (!cardNumber || cardNumber.value.replace(/\s/g, '').length < 13) {
        isValid = false;
        if (cardNumber) {
            cardNumber.classList.add('is-invalid');
            showFieldError(cardNumber, 'Please enter a valid card number');
        }
    }

    // Validate expiry date
    if (!expiryDate || !isValidExpiryDate(expiryDate.value)) {
        isValid = false;
        if (expiryDate) {
            expiryDate.classList.add('is-invalid');
            showFieldError(expiryDate, 'Please enter a valid expiry date (MM/YY)');
        }
    }

    // Validate CVV
    if (!cvv || cvv.value.length < 3) {
        isValid = false;
        if (cvv) {
            cvv.classList.add('is-invalid');
            showFieldError(cvv, 'Please enter a valid CVV');
        }
    }

    // Validate cardholder name
    if (!cardName || cardName.value.trim() === '') {
        isValid = false;
        if (cardName) {
            cardName.classList.add('is-invalid');
            showFieldError(cardName, 'Please enter the cardholder name');
        }
    }

    return isValid;
}

function isValidExpiryDate(dateStr) {
    if (!dateStr || dateStr.length !== 5) return false;

    const parts = dateStr.split('/');
    if (parts.length !== 2) return false;

    const month = parseInt(parts[0], 10);
    const year = parseInt('20' + parts[1], 10);

    if (month < 1 || month > 12) return false;

    const currentDate = new Date();
    const currentYear = currentDate.getFullYear();
    const currentMonth = currentDate.getMonth() + 1;

    if (year < currentYear || (year === currentYear && month < currentMonth)) {
        return false;
    }

    return true;
}

function showFieldError(field, message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message text-danger small mt-1';
    errorDiv.textContent = message;
    errorDiv.style.cssText = `
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: block;
        font-family: 'Rubik', sans-serif;
    `;

    // Insert after the field
    if (field.parentNode) {
        field.parentNode.insertBefore(errorDiv, field.nextSibling);
    }
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
        background: ${type === 'success' ? '#28a745' : type === 'danger' ? '#dc3545' : type === 'warning' ? '#ffc107' : '#17a2b8'};
        color: ${type === 'warning' ? '#212529' : 'white'};
        padding: 15px 20px;
        border-radius: 8px;
        z-index: 10000;
        transform: translateX(400px);
        transition: transform 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        font-family: 'Rubik', sans-serif;
        font-size: 14px;
        max-width: 350px;
        word-wrap: break-word;
    `;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'}" style="margin-right: 8px;"></i>
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

// Alias for backward compatibility
function showAlert(message, type = 'info') {
    showNotification(message, type);
}

// Form submission
function submitForm(event) {
    if (event) event.preventDefault();

    if (!validateForm()) {
        showNotification('Looks like some information is missing or incorrect. Please review and try again.','danger');


        return false;
    }

    // Show loading state
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
    }

    // Simulate processing delay
    setTimeout(() => {
        // In a real application, you would submit the form data here
        showNotification('Payment processed successfully!', 'success');

        // Reset button
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Complete Payment';
        }

        // You can redirect or perform other actions here
        // window.location.href = '/success';
    }, 2000);

    return false;
}

// Real-time field validation
function setupFieldValidation() {
    const fields = ['name', 'email', 'address', 'phone', 'cardNumber', 'expiryDate', 'cvv', 'cardName'];

    fields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', function () {
                // Remove error styling on input
                this.classList.remove('is-invalid');
                const errorMsg = this.parentNode.querySelector('.error-message');
                if (errorMsg) {
                    errorMsg.remove();
                }

                // Update progress
                updateProgress();
            });

            field.addEventListener('blur', function () {
                // Validate individual field on blur
                validateSingleField(this);
            });
        }
    });
}

function validateSingleField(field) {
    const fieldId = field.id;

    switch (fieldId) {
        case 'email':
            if (field.value.trim() !== '') {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(field.value.trim())) {
                    field.classList.add('is-invalid');
                    showFieldError(field, 'Please enter a valid email address');
                }
            }
            break;
        case 'phone':
            if (field.value.trim() !== '') {
                const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
                if (!phoneRegex.test(field.value.replace(/\s|-|\(|\)/g, ''))) {
                    field.classList.add('is-invalid');
                    showFieldError(field, 'Please enter a valid phone number');
                }
            }
            break;
        case 'cardNumber':
            if (field.value.replace(/\s/g, '').length > 0 && field.value.replace(/\s/g, '').length < 13) {
                field.classList.add('is-invalid');
                showFieldError(field, 'Please enter a valid card number');
            }
            break;
        case 'expiryDate':
            if (field.value.length > 0 && !isValidExpiryDate(field.value)) {
                field.classList.add('is-invalid');
                showFieldError(field, 'Please enter a valid expiry date (MM/YY)');
            }
            break;
        case 'cvv':
            if (field.value.length > 0 && field.value.length < 3) {
                field.classList.add('is-invalid');
                showFieldError(field, 'Please enter a valid CVV');
            }
            break;
    }
}

// CVV formatting (numbers only)
function formatCVV(input) {
    let value = input.value.replace(/\D/g, '');
    value = value.substring(0, 4); // Max 4 digits for CVV
    input.value = value;
}

// Phone number formatting
function formatPhoneNumber(input) {
    let value = input.value.replace(/\D/g, '');
    if (value.length >= 6) {
        value = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
    } else if (value.length >= 3) {
        value = value.replace(/(\d{3})(\d{0,3})/, '($1) $2');
    }
    input.value = value;
}

// Initialize field validation when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    setupFieldValidation();

    // Add event listeners for form submission
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', submitForm);
    }

    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.addEventListener('click', submitForm);
    }
});