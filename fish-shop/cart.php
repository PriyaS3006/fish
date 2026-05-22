<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo 'login';
        exit;
    }
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add' && isset($_POST['product_id'])) {
        $pid = (int)$_POST['product_id'];
        $check = $conn->query("SELECT id, quantity FROM cart WHERE user_id=$user_id AND product_id=$pid");
        if ($check->num_rows > 0) {
            $row = $check->fetch_assoc();
            $newQty = $row['quantity'] + 1;
            $conn->query("UPDATE cart SET quantity=$newQty WHERE id={$row['id']}");
        } else {
            $conn->query("INSERT INTO cart (user_id, product_id) VALUES ($user_id, $pid)");
        }
        echo 'ok';
        exit;
    }

    if ($action === 'update' && isset($_POST['product_id'], $_POST['quantity'])) {
        $pid = (int)$_POST['product_id'];
        $qty = (int)$_POST['quantity'];
        if ($qty < 1) $qty = 1;
        $conn->query("UPDATE cart SET quantity=$qty WHERE user_id=$user_id AND product_id=$pid");
        exit;
    }

    if ($action === 'remove' && isset($_POST['product_id'])) {
        $pid = (int)$_POST['product_id'];
        $conn->query("DELETE FROM cart WHERE user_id=$user_id AND product_id=$pid");
        header('Location: cart.php');
        exit;
    }

    if ($action === 'checkout') {
        $items = $conn->query("SELECT c.quantity, p.price, p.id FROM cart c JOIN products p ON c.product_id=p.id WHERE c.user_id=$user_id");
        if ($items->num_rows === 0) {
            $error = 'Your cart is empty.';
        } else {
            $total = 0;
            $orderItems = [];
            while ($item = $items->fetch_assoc()) {
                $subtotal = $item['quantity'] * $item['price'];
                $total += $subtotal;
                $orderItems[] = $item;
            }
            $conn->query("INSERT INTO orders (user_id, total_amount) VALUES ($user_id, $total)");
            $order_id = $conn->insert_id;
            foreach ($orderItems as $item) {
                $conn->query("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ($order_id, {$item['id']}, {$item['quantity']}, {$item['price']})");
            }
            $conn->query("DELETE FROM cart WHERE user_id=$user_id");
            $success = 'Order placed successfully! Thank you for your purchase.';
        }
    }
}

$cart_items = $conn->query("SELECT c.*, p.name, p.price, p.image, p.description FROM cart c JOIN products p ON c.product_id=p.id WHERE c.user_id=$user_id");
$total = 0;
while ($item = $cart_items->fetch_assoc()) {
    $total += $item['quantity'] * $item['price'];
}
$cart_items->data_seek(0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - Fishy Shop</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <section class="cart-section">
        <div class="container">
            <h2>Your Shopping Cart</h2>
            <?php if (isset($success)): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
            <?php if (isset($error)): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
            <?php if ($cart_items->num_rows === 0): ?>
                <p style="text-align:center;font-size:18px;color:#888;padding:40px 0;">Your cart is empty. <a href="products.php">Browse fish</a></p>
            <?php else: ?>
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = $cart_items->fetch_assoc()):
                            $subtotal = $item['quantity'] * $item['price'];
                        ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($item['name']); ?></strong><br>
                                <small><?php echo htmlspecialchars(substr($item['description'], 0, 50)); ?></small>
                            </td>
                            <td>₹<?php echo number_format($item['price'], 2); ?></td>
                            <td>
                                <input type="number" value="<?php echo $item['quantity']; ?>" min="1" max="99"
                                    onchange="updateCartQuantity(this, <?php echo $item['product_id']; ?>)">
                            </td>
                            <td>₹<?php echo number_format($subtotal, 2); ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                    <button type="submit" class="btn btn-small btn-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <div class="cart-total">
                    Total: ₹<?php echo number_format($total, 2); ?>
                </div>
                <div style="text-align:right;margin-top:20px;">
                    <form method="POST">
                        <input type="hidden" name="action" value="checkout">
                        <button type="submit" class="btn">Place Order</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <?php include 'footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>
