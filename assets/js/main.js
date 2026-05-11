// NOCIBE Frontend JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initializeComponents();
});

function initializeComponents() {
    // Notifications dropdown
    initializeNotifications();

    // Form validation
    initializeFormValidation();

    // Password strength indicator
    initializePasswordStrength();

    // Image preview for product uploads
    initializeImagePreview();

    // Cart functionality
    initializeCart();

    // Order form steps
    initializeOrderSteps();

    // Tracking search
    initializeTracking();

    // Modal confirmations
    initializeModals();

    // Filters and search
    initializeFilters();
}

function initializeNotifications() {
    const notificationMenus = document.querySelectorAll('.notification-menu');

    notificationMenus.forEach(menu => {
        const toggle = menu.querySelector('.notification-toggle');

        if (!toggle) return;

        toggle.addEventListener('click', function(event) {
            event.stopPropagation();

            notificationMenus.forEach(otherMenu => {
                if (otherMenu !== menu) {
                    otherMenu.classList.remove('is-open');
                    const otherToggle = otherMenu.querySelector('.notification-toggle');
                    if (otherToggle) otherToggle.setAttribute('aria-expanded', 'false');
                }
            });

            const isOpen = menu.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    });

    document.addEventListener('click', function() {
        notificationMenus.forEach(menu => {
            menu.classList.remove('is-open');
            const toggle = menu.querySelector('.notification-toggle');
            if (toggle) toggle.setAttribute('aria-expanded', 'false');
        });
    });
}

// Form Validation
function initializeFormValidation() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
}

// Password Strength Indicator
function initializePasswordStrength() {
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    passwordInputs.forEach(input => {
        input.addEventListener('input', function() {
            updatePasswordStrength(this);
        });
    });
}

function updatePasswordStrength(passwordInput) {
    const password = passwordInput.value;
    const strengthContainer = passwordInput.parentNode.querySelector('.password-strength');

    if (!strengthContainer) return;

    const strengthMeter = strengthContainer.querySelector('.strength-meter');
    const strengthFill = strengthContainer.querySelector('.strength-fill');
    const strengthText = strengthContainer.querySelector('.strength-text');

    let strength = 0;
    let feedback = [];

    if (password.length >= 8) strength++;
    else feedback.push('Au moins 8 caractères');

    if (/[a-z]/.test(password)) strength++;
    else feedback.push('Une lettre minuscule');

    if (/[A-Z]/.test(password)) strength++;
    else feedback.push('Une lettre majuscule');

    if (/[0-9]/.test(password)) strength++;
    else feedback.push('Un chiffre');

    if (/[^A-Za-z0-9]/.test(password)) strength++;
    else feedback.push('Un caractère spécial');

    strengthFill.className = 'strength-fill';

    if (strength <= 2) {
        strengthFill.classList.add('strength-weak');
        strengthText.textContent = 'Faible';
        strengthText.style.color = 'var(--danger)';
    } else if (strength <= 3) {
        strengthFill.classList.add('strength-medium');
        strengthText.textContent = 'Moyen';
        strengthText.style.color = 'var(--warning)';
    } else {
        strengthFill.classList.add('strength-strong');
        strengthText.textContent = 'Fort';
        strengthText.style.color = 'var(--success)';
    }
}

// Image Preview
function initializeImagePreview() {
    const imageInputs = document.querySelectorAll('input[type="file"][accept="image/*"]');
    imageInputs.forEach(input => {
        input.addEventListener('change', function() {
            previewImage(this);
        });
    });
}

function previewImage(input) {
    const previewId = input.id.replace('Image', 'Preview');
    const preview = document.getElementById(previewId);

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.classList.add('d-none');
    }
}

// Cart Functionality
function initializeCart() {
    // Update cart badge on page load
    updateCartBadge();
    
    // Quantity controls
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('quantity-controls') ||
            e.target.closest('.quantity-controls')) {
            return; // Don't interfere with quantity controls
        }
    });
}

function updateQuantity(itemId, change) {
    const quantitySpan = document.querySelector(`[onclick*="updateQuantity(${itemId}"]`).nextElementSibling;
    let quantity = parseInt(quantitySpan.textContent);
    quantity = Math.max(1, quantity + change);
    quantitySpan.textContent = quantity;

    // Update subtotal (this would normally be calculated server-side)
    updateCartTotal();
}

function removeItem(itemId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet article du panier ?')) {
        // Remove item from DOM
        const cartItem = document.querySelector(`[onclick*="removeItem(${itemId})"]`).closest('.cart-item');
        cartItem.remove();
        updateCartTotal();
    }
}

function updateCartTotal() {
    // Simple calculation for demo
    let total = 0;
    const items = document.querySelectorAll('.cart-item');
    items.forEach(item => {
        const price = parseInt(item.querySelector('.details strong').textContent.replace(/\D/g, ''));
        const quantity = parseInt(item.querySelector('.quantity-controls span').textContent);
        total += price * quantity;
    });

    const totalElement = document.querySelector('.order-summary .total strong:last-child');
    if (totalElement) {
        totalElement.textContent = total.toLocaleString() + ' FCFA';
    }
}

function addToCart(button) {
    // Get product data from button attributes
    const productId = button.getAttribute('data-product');
    const productName = button.getAttribute('data-name');
    const productPrice = parseInt(button.getAttribute('data-price'));

    // Initialize cart if not exists
    let cart = JSON.parse(localStorage.getItem('nocibe_cart')) || [];

    // Check if product already exists
    const existingProduct = cart.find(item => item.id === productId);
    
    if (existingProduct) {
        existingProduct.quantity += 1;
    } else {
        cart.push({
            id: productId,
            name: productName,
            price: productPrice,
            quantity: 1
        });
    }

    // Save to localStorage
    localStorage.setItem('nocibe_cart', JSON.stringify(cart));

    // Update cart badge
    updateCartBadge();

    // Show success message
    showNotification('✓ Produit ajouté au panier !', 'success');
    
    // Visual feedback
    button.innerHTML = '<i class="fas fa-check me-2"></i>Ajouté !';
    button.style.backgroundColor = 'var(--success)';
    
    setTimeout(() => {
        button.innerHTML = '<i class="fas fa-cart-plus me-2"></i>Ajouter au panier';
        button.style.backgroundColor = '';
    }, 2000);
}

function updateCartBadge() {
    const cart = JSON.parse(localStorage.getItem('nocibe_cart')) || [];
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    const badge = document.querySelector('.notification-badge');
    if (badge) {
        badge.textContent = totalItems;
    }
}

function showNotification(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}

// Order Steps
function initializeOrderSteps() {
    // Already handled in HTML with onclick
}

function nextStep(stepNumber) {
    // Hide all steps
    document.querySelectorAll('.step-content').forEach(step => {
        step.classList.add('d-none');
    });

    // Show target step
    document.getElementById('step' + stepNumber).classList.remove('d-none');

    // Update progress
    updateStepProgress(stepNumber);
}

function prevStep(stepNumber) {
    nextStep(stepNumber);
}

function updateStepProgress(activeStep) {
    const steps = document.querySelectorAll('.step');
    steps.forEach((step, index) => {
        const stepNumber = index + 1;
        step.classList.remove('completed', 'active');

        if (stepNumber < activeStep) {
            step.classList.add('completed');
        } else if (stepNumber === activeStep) {
            step.classList.add('active');
        }
    });
}

function selectPayment(method) {
    // Remove selection from all
    document.querySelectorAll('.payment-card').forEach(card => {
        card.classList.remove('border-secondary');
    });

    // Add selection to clicked
    document.querySelector(`[data-method="${method}"]`).classList.add('border-secondary');

    // Check radio button
    document.getElementById(method).checked = true;
}

// Tracking
function initializeTracking() {
    const trackingForm = document.getElementById('trackingForm');
    if (trackingForm) {
        trackingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const orderNumber = document.getElementById('orderNumber').value;
            if (orderNumber) {
                document.getElementById('trackingResult').classList.remove('d-none');
                // In real app, would fetch order data
            }
        });
    }
}

// Modals
function initializeModals() {
    // Delete confirmation
    window.confirmDelete = function() {
        return confirm('Êtes-vous sûr de vouloir supprimer cet élément ?');
    };

    // Product modals
    window.addProduct = function() {
        alert('Produit ajouté avec succès !');
        bootstrap.Modal.getInstance(document.getElementById('addProductModal')).hide();
    };

    window.editProduct = function() {
        alert('Produit modifié avec succès !');
        bootstrap.Modal.getInstance(document.getElementById('editProductModal')).hide();
    };

    // Order modal
    window.createOrder = function() {
        alert('Commande créée avec succès !');
        bootstrap.Modal.getInstance(document.getElementById('createOrderModal')).hide();
    };

    // Delivery modal
    let currentOrderId = null;
    window.setOrderId = function(orderId) {
        currentOrderId = orderId;
    };

    window.updateDeliveryStatus = function() {
        alert('Statut mis à jour avec succès !');
        bootstrap.Modal.getInstance(document.getElementById('updateStatusModal')).hide();
    };

    // Dynamic product rows in order creation
    const addProductBtn = document.getElementById('addProductRow');
    if (addProductBtn) {
        addProductBtn.addEventListener('click', addProductRow);
    }

    // Update calculations when product selection or quantity changes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select') || e.target.classList.contains('quantity-input')) {
            updateOrderCalculations();
        }
    });
}

function addProductRow() {
    const container = document.getElementById('productsContainer');
    const newRow = container.querySelector('.product-row').cloneNode(true);

    // Clear values
    newRow.querySelectorAll('input, select').forEach(input => {
        input.value = '';
    });

    // Enable remove button
    newRow.querySelector('.remove-product').classList.remove('disabled');

    // Add event listener to remove button
    newRow.querySelector('.remove-product').addEventListener('click', function() {
        newRow.remove();
        updateOrderCalculations();
    });

    container.appendChild(newRow);
    updateOrderCalculations();
}

function updateOrderCalculations() {
    const rows = document.querySelectorAll('.product-row');
    let subtotal = 0;

    rows.forEach(row => {
        const select = row.querySelector('.product-select');
        const quantityInput = row.querySelector('.quantity-input');
        const unitPriceInput = row.querySelector('.unit-price');
        const subtotalInput = row.querySelector('.subtotal');

        if (select.value && quantityInput.value) {
            const price = parseInt(select.selectedOptions[0].getAttribute('data-price'));
            const quantity = parseInt(quantityInput.value);
            const rowSubtotal = price * quantity;

            unitPriceInput.value = price.toLocaleString() + ' FCFA';
            subtotalInput.value = rowSubtotal.toLocaleString() + ' FCFA';
            subtotal += rowSubtotal;
        }
    });

    // Update totals
    document.getElementById('orderSubtotal').textContent = subtotal.toLocaleString() + ' FCFA';
    document.getElementById('orderTotal').textContent = subtotal.toLocaleString() + ' FCFA';
}

// Filters
function initializeFilters() {
    const searchInput = document.getElementById('searchProduct');
    const categoryFilter = document.getElementById('categoryFilter');
    const clearFiltersBtn = document.getElementById('clearFilters');

    if (searchInput) {
        searchInput.addEventListener('input', filterProducts);
    }
    if (categoryFilter) {
        categoryFilter.addEventListener('change', filterProducts);
    }
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', clearFilters);
    }
}

function filterProducts() {
    const searchTerm = document.getElementById('searchProduct').value.toLowerCase();
    const categoryFilter = document.getElementById('categoryFilter').value;
    const products = document.querySelectorAll('#products .col-lg-4');

    products.forEach(product => {
        const name = product.querySelector('.card-title').textContent.toLowerCase();
        const category = product.querySelector('.badge').textContent.toLowerCase();

        const matchesSearch = name.includes(searchTerm);
        const matchesCategory = !categoryFilter || category.includes(categoryFilter);

        if (matchesSearch && matchesCategory) {
            product.style.display = '';
        } else {
            product.style.display = 'none';
        }
    });
}

function clearFilters() {
    document.getElementById('searchProduct').value = '';
    document.getElementById('categoryFilter').value = '';
    filterProducts();
}

// Delivery filters
function filterDeliveries(status) {
    const cards = document.querySelectorAll('.delivery-card');
    const buttons = document.querySelectorAll('.btn-group .btn');

    // Update button states
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');

    cards.forEach(card => {
        if (status === 'all' || card.querySelector('.badge').classList.contains('bg-' + getStatusClass(status))) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

function getStatusClass(status) {
    const classes = {
        'pending': 'warning',
        'preparing': 'secondary',
        'shipped': 'info',
        'delivered': 'success'
    };
    return classes[status] || '';
}

// Status updates
function updateStatus(orderId, newStatus) {
    // In real app, would send to server
    alert(`Statut de la commande ${orderId} mis à jour: ${newStatus}`);
}

// Utility functions
function formatCurrency(amount) {
    return amount.toLocaleString() + ' FCFA';
}

// Animation helpers
function fadeIn(element) {
    element.style.opacity = '0';
    element.style.transform = 'translateY(20px)';
    element.style.transition = 'opacity 0.5s ease, transform 0.5s ease';

    setTimeout(() => {
        element.style.opacity = '1';
        element.style.transform = 'translateY(0)';
    }, 100);
}

// Initialize fade-in animations
document.querySelectorAll('.fade-in').forEach(element => {
    fadeIn(element);
});
