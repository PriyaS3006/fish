<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fishy Shop - Home</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <section class="hero">
        <div class="container">
            <h2>Welcome to Fishy Shop!</h2>
            <p>Your one-stop shop for beautiful, healthy aquarium fish. We deliver happiness to your doorstep!</p>
            <a href="products.php" class="btn">Shop Now</a>
        </div>
    </section>

    <section class="products-section">
        <div class="container">
            <h2>Featured Fish</h2>
            <div class="products-grid">
                <?php
                include 'config/db.php';
                $result = $conn->query("SELECT * FROM products ORDER BY RAND() LIMIT 4");
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="product-card">';
                    echo '<img src="images/' . $row['image'] . '" alt="' . $row['name'] . '" onerror="this.src=\'https://via.placeholder.com/300x200?text=' . $row['name'] . '\'">';
                    echo '<div class="info">';
                    echo '<h3>' . $row['name'] . '</h3>';
                    echo '<p class="desc">' . substr($row['description'], 0, 80) . '...</p>';
                    echo '<p class="price">₹' . number_format($row['price'], 2) . '</p>';
                    echo '<div class="actions">';
                    echo '<a href="products.php" class="btn btn-small">View</a>';
                    echo '<button class="btn btn-small btn-primary" onclick="addToCart(' . $row['id'] . ')">Add to Cart</button>';
                    echo '</div></div></div>';
                }
                ?>
            </div>
        </div>
    </section>

    <section class="container">
        <div class="features">
            <div class="feature">
                <div class="icon">🐟</div>
                <h3>Healthy Fish</h3>
                <p>All our fish are raised in clean, optimal conditions.</p>
            </div>
            <div class="feature">
                <div class="icon">🚚</div>
                <h3>Fast Delivery</h3>
                <p>Safely delivered to your doorstep with care.</p>
            </div>
            <div class="feature">
                <div class="icon">💯</div>
                <h3>100% Satisfaction</h3>
                <p>Love your fish or get a replacement, guaranteed!</p>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>
