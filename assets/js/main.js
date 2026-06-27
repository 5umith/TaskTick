/**
 * Assignment Tracker - Main JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
    
    // Set minimum date for due date inputs to today
    var dueDateInputs = document.querySelectorAll('input[type="date"]');
    var today = new Date().toISOString().split('T')[0];
    
    dueDateInputs.forEach(function(input) {
        if (!input.getAttribute('min')) {
            input.setAttribute('min', today);
        }
    });
    
    // Confirm before deleting assignments (if delete functionality exists)
    var deleteButtons = document.querySelectorAll('.btn-delete');
    
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
    
    // Dynamic status updates via AJAX (for student assignments)
    var statusForms = document.querySelectorAll('.ajax-status-form');
    
    statusForms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            var formData = new FormData(form);
            var assignmentId = form.getAttribute('data-assignment-id');
            
            fetch('update_status.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    var statusMessage = document.createElement('div');
                    statusMessage.className = 'alert alert-success mt-2';
                    statusMessage.textContent = data.message;
                    
                    // Insert message after form
                    form.parentNode.insertBefore(statusMessage, form.nextSibling);
                    
                    // Update status badge if it exists
                    var statusBadge = document.querySelector('#status-badge-' + assignmentId);
                    if (statusBadge) {
                        var status = formData.get('status');
                        var badgeClass = 'badge ';
                        var badgeText = '';
                        
                        switch(status) {
                            case 'completed':
                                badgeClass += 'bg-success';
                                badgeText = 'Completed';
                                break;
                            case 'in_progress':
                                badgeClass += 'bg-warning';
                                badgeText = 'In Progress';
                                break;
                            default:
                                badgeClass += 'bg-danger';
                                badgeText = 'Not Started';
                                break;
                        }
                        
                        statusBadge.className = badgeClass;
                        statusBadge.textContent = badgeText;
                    }
                    
                    // Remove message after 3 seconds
                    setTimeout(function() {
                        statusMessage.remove();
                    }, 3000);
                } else {
                    // Show error message
                    var errorMessage = document.createElement('div');
                    errorMessage.className = 'alert alert-danger mt-2';
                    errorMessage.textContent = data.message;
                    
                    // Insert message after form
                    form.parentNode.insertBefore(errorMessage, form.nextSibling);
                    
                    // Remove message after 3 seconds
                    setTimeout(function() {
                        errorMessage.remove();
                    }, 3000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
    
    // Character counter for textareas
    var textareas = document.querySelectorAll('textarea[data-max-length]');
    
    textareas.forEach(function(textarea) {
        var maxLength = parseInt(textarea.getAttribute('data-max-length'));
        var counterElement = document.createElement('small');
        counterElement.className = 'text-muted d-block text-end';
        counterElement.textContent = textarea.value.length + '/' + maxLength + ' characters';
        
        textarea.parentNode.insertBefore(counterElement, textarea.nextSibling);
        
        textarea.addEventListener('input', function() {
            var currentLength = this.value.length;
            counterElement.textContent = currentLength + '/' + maxLength + ' characters';
            
            if (currentLength > maxLength) {
                counterElement.classList.add('text-danger');
            } else {
                counterElement.classList.remove('text-danger');
            }
        });
    });
});
