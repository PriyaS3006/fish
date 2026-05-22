<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
include '../config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Fishy Shop</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>AquaWorld Fish <span>Shop</span> - Admin</h1>
            <nav>
                <a href="dashboard.php">Dashboard</a>
                <a href="add_product.php">Add Product</a>
                <a href="../index.php">View Shop</a>
                <a href="../logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <section class="admin-section">
        <div class="container">
            <div class="admin-header">
                <h2>Manage Products</h2>
                <a href="add_product.php" class="btn">+ Add New Fish</a>
            </div>

            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
            <?php endif; ?>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
                    if ($result->num_rows === 0) {
                        echo '<tr><td colspan="6" style="text-align:center;">No products found.</td></tr>';
                    }
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . $row['id'] . '</td>';
                        echo '<td><img src="../images/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '" onerror="this.src=\'https://placehold.co/50x50/00b4d8/white?text=F\'"></td>';
                        echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['category']) . '</td>';
                        echo '<td>₹' . number_format($row['price'], 2) . '</td>';
                        echo '<td class="actions">';
                        echo '<a href="edit_product.php?id=' . $row['id'] . '" class="btn btn-small btn-primary">Edit</a>';
                        echo '<a href="delete_product.php?id=' . $row['id'] . '" class="btn btn-small btn-danger" onclick="return confirmDelete(\'Delete ' . htmlspecialchars($row['name'], ENT_QUOTES) . '?\')">Delete</a>';
                        echo '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Orders Section -->
    <section class="admin-section">
        <div class="container">
            <div class="admin-header">
                <h2>Recent Orders</h2>
            </div>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $orders_query = "SELECT o.id, o.user_id, o.total_amount, o.status, o.created_at, u.username, 
                                    GROUP_CONCAT(p.name, ' (', oi.quantity, 'x)' SEPARATOR ', ') as items
                                    FROM orders o
                                    JOIN users u ON o.user_id = u.id
                                    LEFT JOIN order_items oi ON o.id = oi.order_id
                                    LEFT JOIN products p ON oi.product_id = p.id
                                    GROUP BY o.id, o.user_id, o.total_amount, o.status, o.created_at, u.username
                                    ORDER BY o.created_at DESC";
                    $orders_result = $conn->query($orders_query);
                    
                    if ($orders_result->num_rows === 0) {
                        echo '<tr><td colspan="7" style="text-align:center;">No orders found.</td></tr>';
                    } else {
                        while ($order = $orders_result->fetch_assoc()) {
                            $status_class = 'status-' . $order['status'];
                            echo '<tr>';
                            echo '<td>#' . $order['id'] . '</td>';
                            echo '<td>' . htmlspecialchars($order['username']) . '</td>';
                            echo '<td>' . htmlspecialchars($order['items']) . '</td>';
                            echo '<td>$' . number_format($order['total_amount'], 2) . '</td>';
                            echo '<td><span class="badge ' . $status_class . '">' . ucfirst($order['status']) . '</span></td>';
                            echo '<td>' . date('M d, Y', strtotime($order['created_at'])) . '</td>';
                            echo '<td>';
                            echo '<select onchange="updateOrderStatus(' . $order['id'] . ', this.value)" class="status-select">';
                            echo '<option value="">Update Status</option>';
                            echo '<option value="pending" ' . ($order['status'] === 'pending' ? 'disabled' : '') . '>Pending</option>';
                            echo '<option value="completed" ' . ($order['status'] === 'completed' ? 'disabled' : '') . '>Completed</option>';
                            echo '<option value="cancelled" ' . ($order['status'] === 'cancelled' ? 'disabled' : '') . '>Cancelled</option>';
                            echo '</select>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>
    <script src="../js/script.js"></script>
</body>
</html>
