function showToast(message, type) {
    var toast = document.getElementById('toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toast';
        toast.className = 'toast';
        document.body.appendChild(toast);
    }
    toast.textContent = message;
    toast.style.background = type === 'error' ? '#dc3545' : '#28a745';
    toast.style.display = 'block';
    setTimeout(function() {
        toast.style.display = 'none';
    }, 3000);
}

function confirmDelete(message) {
    return confirm(message || 'Are you sure you want to delete this?');
}

function updateCartQuantity(input, productId) {
    var qty = parseInt(input.value);
    if (qty < 1) qty = 1;
    if (qty > 99) qty = 99;
    input.value = qty;

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'cart.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            location.reload();
        }
    };
    xhr.send('action=update&product_id=' + productId + '&quantity=' + qty);
}

function addToCart(productId) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'cart.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            if (xhr.responseText.trim() === 'login') {
                window.location.href = 'login.php';
            } else {
                showToast('Added to cart!');
            }
        }
    };
    xhr.send('action=add&product_id=' + productId);
}

function updateOrderStatus(orderId, status) {
    if (!status) return;
    
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_order_status.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    showToast('Order status updated successfully!', 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showToast('Error: ' + response.message, 'error');
                }
            } catch(e) {
                showToast('Error updating order', 'error');
            }
        }
    };
    xhr.send('order_id=' + orderId + '&status=' + status);
}
