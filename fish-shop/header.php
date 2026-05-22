<header>
    <div class="container">
        <h1><a href="index.php">AquaWorld Fish <span>Shop</span></a></h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="products.php">Products</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="cart.php">Cart</a>
                <a href="logout.php">Logout (<?php echo $_SESSION['username']; ?>)</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="admin/dashboard.php">Admin</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
