<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Fishy Shop</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <section class="products-section">
        <div class="container">
            <h2>All Fish</h2>
            <div class="products-grid">
                <?php
                include 'config/db.php';
                $result = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
                if ($result->num_rows === 0) {
                    echo '<p style="text-align:center;font-size:18px;color:#888;">No products available yet.</p>';
                }
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="product-card">';
                    echo '<img src="images/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '" onerror="this.src=\'https://placehold.co/300x200/00b4d8/white?text=' . urlencode($row['name']) . '\'">';
                    echo '<div class="info">';
                    echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                    echo '<p class="category">' . htmlspecialchars($row['category']) . '</p>';
                    echo '<p class="desc">' . htmlspecialchars($row['description']) . '</p>';
                    echo '<p class="price">$' . number_format($row['price'], 2) . '</p>';
                    echo '<div class="actions">';
                    if (isset($_SESSION['user_id'])) {
                        echo '<button class="btn btn-small btn-primary" onclick="addToCart(' . $row['id'] . ')">Add to Cart</button>';
                    } else {
                        echo '<a href="login.php" class="btn btn-small btn-primary">Login to Buy</a>';
                    }
                    echo '</div></div></div>';
                }
                ?>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>
