// Password visibility toggle function
function togglePassword(inputId, toggleElement) {
    const input = document.getElementById(inputId);
    const icon = toggleElement.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
        toggleElement.title = 'Hide Password';
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
        toggleElement.title = 'Show Password';
    }
}

// Password strength checker
function checkPasswordStrength(password) {
    let strength = 0;
    let feedback = [];
    
    // Length check
    if (password.length >= 8) {
        strength += 1;
    } else {
        feedback.push('At least 8 characters');
    }
    
    // Uppercase check
    if (/[A-Z]/.test(password)) {
        strength += 1;
    } else {
        feedback.push('One uppercase letter');
    }
    
    // Lowercase check
    if (/[a-z]/.test(password)) {
        strength += 1;
    } else {
        feedback.push('One lowercase letter');
    }
    
    // Number check
    if (/[0-9]/.test(password)) {
        strength += 1;
    } else {
        feedback.push('One number');
    }
    
    // Special character check (bonus)
    if (/[^A-Za-z0-9]/.test(password)) {
        strength += 1;
    }
    
    return { strength, feedback };
}

// Update password strength indicator
function updatePasswordStrength() {
    const passwordInput = document.getElementById('signup_password');
    const strengthDiv = document.getElementById('password-strength');
    
    if (!passwordInput || !strengthDiv) return;
    
    const password = passwordInput.value;
    const { strength, feedback } = checkPasswordStrength(password);
    
    // Clear previous content
    strengthDiv.innerHTML = '';
    
    if (password.length === 0) {
        return;
    }
    
    // Create strength bar
    const strengthBar = document.createElement('div');
    strengthBar.className = 'strength-bar';
    strengthBar.style.cssText = `
        width: 100%;
        height: 4px;
        background-color: #e0e0e0;
        border-radius: 2px;
        margin: 5px 0;
        overflow: hidden;
    `;
    
    const strengthFill = document.createElement('div');
    strengthFill.style.cssText = `
        height: 100%;
        transition: all 0.3s ease;
        border-radius: 2px;
    `;
    
    // Set strength level and color
    let strengthText = '';
    let strengthColor = '';
    let strengthWidth = '';
    
    switch (strength) {
        case 0:
        case 1:
            strengthText = 'Very Weak';
            strengthColor = '#ff4444';
            strengthWidth = '20%';
            break;
        case 2:
            strengthText = 'Weak';
            strengthColor = '#ff8800';
            strengthWidth = '40%';
            break;
        case 3:
            strengthText = 'Fair';
            strengthColor = '#ffaa00';
            strengthWidth = '60%';
            break;
        case 4:
            strengthText = 'Good';
            strengthColor = '#88cc00';
            strengthWidth = '80%';
            break;
        case 5:
            strengthText = 'Strong';
            strengthColor = '#00cc44';
            strengthWidth = '100%';
            break;
    }
    
    strengthFill.style.width = strengthWidth;
    strengthFill.style.backgroundColor = strengthColor;
    
    strengthBar.appendChild(strengthFill);
    strengthDiv.appendChild(strengthBar);
    
    // Add strength text
    const strengthLabel = document.createElement('div');
    strengthLabel.style.cssText = `
        font-size: 12px;
        color: ${strengthColor};
        margin-top: 2px;
        font-weight: bold;
    `;
    strengthLabel.textContent = strengthText;
    strengthDiv.appendChild(strengthLabel);
    
    // Add feedback for missing requirements
    if (feedback.length > 0 && strength < 4) {
        const feedbackDiv = document.createElement('div');
        feedbackDiv.style.cssText = `
            font-size: 11px;
            color: #666;
            margin-top: 3px;
        `;
        feedbackDiv.textContent = 'Missing: ' + feedback.join(', ');
        strengthDiv.appendChild(feedbackDiv);
    }
}

// Form animation and switching
document.addEventListener('DOMContentLoaded', function() {
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const container = document.getElementById('container');
    const passwordInput = document.getElementById('signup_password');
    
    // Form switching animations
    if (signUpButton) {
        signUpButton.addEventListener('click', () => {
            container.classList.add('right-panel-active');
        });
    }
    
    if (signInButton) {
        signInButton.addEventListener('click', () => {
            container.classList.remove('right-panel-active');
        });
    }
    
    // Password strength checker
    if (passwordInput) {
        passwordInput.addEventListener('input', updatePasswordStrength);
        passwordInput.addEventListener('focus', updatePasswordStrength);
    }
    
    // Form validation enhancement
    const signupForm = document.getElementById('signupForm');
    const signinForm = document.getElementById('signinForm');
    
    if (signupForm) {
        signupForm.addEventListener('submit', function(e) {
            const password = document.getElementById('signup_password').value;
            const { strength } = checkPasswordStrength(password);
            
            if (strength < 3) {
                e.preventDefault();
                alert('Please use a stronger password (at least Fair strength)');
                return false;
            }
        });
    }
    
    // Auto-hide messages after 5 seconds
    const successMessage = document.querySelector('.success-message');
    const errorMessage = document.querySelector('.error-message');
    
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.opacity = '0';
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 300);
        }, 5000);
    }
    
    if (errorMessage) {
        setTimeout(() => {
            errorMessage.style.opacity = '0';
            setTimeout(() => {
                errorMessage.style.display = 'none';
            }, 300);
        }, 5000);
    }
});

// Real-time form validation
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validateUsername(username) {
    const usernameRegex = /^[a-zA-Z0-9\s]+$/;
    return username.length > 0 && usernameRegex.test(username);
}

// Add real-time validation listeners
document.addEventListener('DOMContentLoaded', function() {
    const usernameInput = document.querySelector('input[name="username"]');
    const emailInputs = document.querySelectorAll('input[type="email"]');
    
    if (usernameInput) {
        usernameInput.addEventListener('blur', function() {
            const errorDiv = this.nextElementSibling;
            if (!validateUsername(this.value) && this.value.length > 0) {
                errorDiv.textContent = 'Username must contain only letters, numbers, and spaces';
                errorDiv.style.color = '#dc3545';
            } else if (this.value.length > 0) {
                errorDiv.textContent = '';
            }
        });
    }
    
    emailInputs.forEach(input => {
        input.addEventListener('blur', function() {
            const errorDiv = this.nextElementSibling;
            if (!validateEmail(this.value) && this.value.length > 0) {
                errorDiv.textContent = 'Please enter a valid email address';
                errorDiv.style.color = '#dc3545';
            } else if (this.value.length > 0) {
                errorDiv.textContent = '';
            }
        });
    });
});