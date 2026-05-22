<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../config/db.php';
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $category = trim($_POST['category']);
    $image = 'default_fish.jpg';

    if (empty($name) || empty($price)) {
        $error = 'Name and price are required.';
    } elseif (!is_numeric($price) || $price <= 0) {
        $error = 'Price must be a positive number.';
    } else {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $image = time() . '_' . basename($_FILES['image']['name']);
                move_uploaded_file($_FILES['image']['tmp_name'], '../images/' . $image);
            } else {
                $error = 'Only JPG, PNG, and GIF files are allowed.';
            }
        }

        if (empty($error)) {
            $stmt = $conn->prepare("INSERT INTO products (name, description, price, image, category) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdss", $name, $description, $price, $image, $category);
            if ($stmt->execute()) {
                header('Location: dashboard.php?msg=Product added successfully!');
                exit;
            } else {
                $error = 'Error adding product.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>🐠 Fishy <span>Shop</span> - Admin</h1>
            <nav>
                <a href="dashboard.php">Dashboard</a>
                <a href="add_product.php">Add Product</a>
                <a href="../index.php">View Shop</a>
                <a href="../logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <section class="form-section">
        <div class="container">
            <div class="form-box" style="max-width:600px;">
                <h2>Add New Fish</h2>
                <?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Fish Name</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Price ($)</label>
                        <input type="number" step="0.01" name="price" required>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category">
                            <option value="Freshwater">Freshwater</option>
                            <option value="Saltwater">Saltwater</option>
                            <option value="Brackish">Brackish</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Image</label>
                        <input type="file" name="image" accept="image/*">
                    </div>
                    <button type="submit" class="btn">Add Product</button>
                    <p><a href="dashboard.php">Back to Dashboard</a></p>
                </form>
            </div>
        </div>
    </section>
</body>
</html>
