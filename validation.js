/**
 * Assignment Tracker - Form Validation
 */

document.addEventListener('DOMContentLoaded', function() {
    // Registration form validation
    const registrationForm = document.getElementById('registrationForm');
    
    if (registrationForm) {
        registrationForm.addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const roleTeacher = document.getElementById('teacher');
            const roleStudent = document.getElementById('student');
            let isValid = true;
            let errorMessage = '';
            
            // Validate name
            if (name === '') {
                errorMessage = 'Name is required';
                isValid = false;
            }
            // Validate email
            else if (email === '') {
                errorMessage = 'Email is required';
                isValid = false;
            }
            else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                errorMessage = 'Please enter a valid email address';
                isValid = false;
            }
            // Validate password
            else if (password === '') {
                errorMessage = 'Password is required';
                isValid = false;
            }
            else if (password.length < 6) {
                errorMessage = 'Password must be at least 6 characters';
                isValid = false;
            }
            // Validate confirm password
            else if (confirmPassword === '') {
                errorMessage = 'Please confirm your password';
                isValid = false;
            }
            else if (password !== confirmPassword) {
                errorMessage = 'Passwords do not match';
                isValid = false;
            }
            // Validate role selection
            else if (!roleTeacher.checked && !roleStudent.checked) {
                errorMessage = 'Please select a role (Teacher or Student)';
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                
                // Display error message
                let alertElement = document.querySelector('.alert-danger');
                if (!alertElement) {
                    alertElement = document.createElement('div');
                    alertElement.className = 'alert alert-danger';
                    registrationForm.insertBefore(alertElement, registrationForm.firstChild);
                }
                
                alertElement.textContent = errorMessage;
                
                // Scroll to the top of the form
                window.scrollTo({
                    top: registrationForm.offsetTop - 20,
                    behavior: 'smooth'
                });
            }
        });
    }
    
    // Login form validation
    const loginForm = document.getElementById('loginForm');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            let isValid = true;
            let errorMessage = '';
            
            // Validate email
            if (email === '') {
                errorMessage = 'Email is required';
                isValid = false;
            }
            else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                errorMessage = 'Please enter a valid email address';
                isValid = false;
            }
            // Validate password
            else if (password === '') {
                errorMessage = 'Password is required';
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                
                // Display error message
                let alertElement = document.querySelector('.alert-danger');
                if (!alertElement) {
                    alertElement = document.createElement('div');
                    alertElement.className = 'alert alert-danger';
                    loginForm.insertBefore(alertElement, loginForm.firstChild);
                }
                
                alertElement.textContent = errorMessage;
            }
        });
    }
    
    // Create assignment form validation
    const createAssignmentForm = document.getElementById('createAssignmentForm');
    
    if (createAssignmentForm) {
        createAssignmentForm.addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const description = document.getElementById('description').value.trim();
            const dueDate = document.getElementById('due_date').value;
            const studentCheckboxes = document.querySelectorAll('input[name="students[]"]:checked');
            let isValid = true;
            let errorMessage = '';
            
            // Validate title
            if (title === '') {
                errorMessage = 'Assignment title is required';
                isValid = false;
            }
            // Validate description
            else if (description === '') {
                errorMessage = 'Description is required';
                isValid = false;
            }
            // Validate due date
            else if (dueDate === '') {
                errorMessage = 'Due date is required';
                isValid = false;
            }
            // Validate student selection
            else if (studentCheckboxes.length === 0) {
                errorMessage = 'Please select at least one student';
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                
                // Display error message
                let alertElement = document.querySelector('.alert-danger');
                if (!alertElement) {
                    alertElement = document.createElement('div');
                    alertElement.className = 'alert alert-danger';
                    createAssignmentForm.insertBefore(alertElement, createAssignmentForm.firstChild);
                }
                
                alertElement.textContent = errorMessage;
                
                // Scroll to the top of the form
                window.scrollTo({
                    top: createAssignmentForm.offsetTop - 20,
                    behavior: 'smooth'
                });
            }
        });
    }
    
    // Password strength meter
    const passwordInput = document.getElementById('password');
    
    if (passwordInput) {
        const createStrengthMeter = () => {
            const meterContainer = document.createElement('div');
            meterContainer.className = 'password-strength mt-2';
            
            const strengthText = document.createElement('small');
            strengthText.className = 'text-muted';
            strengthText.textContent = 'Password strength: ';
            
            const strengthValue = document.createElement('span');
            strengthValue.id = 'password-strength-text';
            strengthValue.textContent = 'Type a password';
            
            const strengthMeter = document.createElement('div');
            strengthMeter.className = 'progress mt-1';
            strengthMeter.style.height = '5px';
            
            const strengthBar = document.createElement('div');
            strengthBar.id = 'password-strength-meter';
            strengthBar.className = 'progress-bar';
            strengthBar.style.width = '0%';
            strengthBar.setAttribute('role', 'progressbar');
            strengthBar.setAttribute('aria-valuenow', '0');
            strengthBar.setAttribute('aria-valuemin', '0');
            strengthBar.setAttribute('aria-valuemax', '100');
            
            strengthMeter.appendChild(strengthBar);
            strengthText.appendChild(strengthValue);
            meterContainer.appendChild(strengthText);
            meterContainer.appendChild(strengthMeter);
            
            passwordInput.parentNode.insertBefore(meterContainer, passwordInput.nextSibling);
        };
        
        createStrengthMeter();
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const meter = document.getElementById('password-strength-meter');
            const text = document.getElementById('password-strength-text');
            
            if (password === '') {
                meter.style.width = '0%';
                meter.className = 'progress-bar';
                text.textContent = 'Type a password';
                return;
            }
            
            // Simple password strength calculation
            let strength = 0;
            
            // Length check
            if (password.length >= 8) {
                strength += 25;
            } else if (password.length >= 6) {
                strength += 10;
            }
            
            // Character variety checks
            if (/[A-Z]/.test(password)) strength += 20; // Uppercase
            if (/[a-z]/.test(password)) strength += 15; // Lowercase
            if (/[0-9]/.test(password)) strength += 20; // Numbers
            if (/[^A-Za-z0-9]/.test(password)) strength += 20; // Special chars
            
            // Update meter and text
            meter.style.width = strength + '%';
            
            if (strength < 30) {
                meter.className = 'progress-bar bg-danger';
                text.textContent = 'Weak';
            } else if (strength < 60) {
                meter.className = 'progress-bar bg-warning';
                text.textContent = 'Fair';
            } else if (strength < 80) {
                meter.className = 'progress-bar bg-info';
                text.textContent = 'Good';
            } else {
                meter.className = 'progress-bar bg-success';
                text.textContent = 'Strong';
            }
        });
    }
});
