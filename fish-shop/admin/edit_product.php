<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

include '../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
if (!$product) {
    header('Location: dashboard.php?msg=Product not found.');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $category = trim($_POST['category']);
    $image = $product['image'];

    if (empty($name) || empty($price)) {
        $error = 'Name and price are required.';
    } elseif (!is_numeric($price) || $price <= 0) {
        $error = 'Price must be a positive number.';
    } else {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                if ($image !== 'default_fish.jpg' && file_exists('../images/' . $image)) {
                    unlink('../images/' . $image);
                }
                $image = time() . '_' . basename($_FILES['image']['name']);
                move_uploaded_file($_FILES['image']['tmp_name'], '../images/' . $image);
            } else {
                $error = 'Only JPG, PNG, and GIF files are allowed.';
            }
        }

        if (empty($error)) {
            $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, image=?, category=? WHERE id=?");
            $stmt->bind_param("ssdssi", $name, $description, $price, $image, $category, $id);
            if ($stmt->execute()) {
                header('Location: dashboard.php?msg=Product updated successfully!');
                exit;
            } else {
                $error = 'Error updating product.';
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
    <title>Edit Product - Admin</title>
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
                <h2>Edit Fish</h2>
                <?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Fish Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Price ($)</label>
                        <input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category">
                            <option value="Freshwater" <?php echo $product['category'] === 'Freshwater' ? 'selected' : ''; ?>>Freshwater</option>
                            <option value="Saltwater" <?php echo $product['category'] === 'Saltwater' ? 'selected' : ''; ?>>Saltwater</option>
                            <option value="Brackish" <?php echo $product['category'] === 'Brackish' ? 'selected' : ''; ?>>Brackish</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Current Image</label>
                        <div><img src="../images/<?php echo htmlspecialchars($product['image']); ?>" style="width:100px;height:100px;object-fit:cover;border-radius:5px;" onerror="this.src='https://placehold.co/100x100/00b4d8/white?text=F'"></div>
                    </div>
                    <div class="form-group">
                        <label>Change Image (optional)</label>
                        <input type="file" name="image" accept="image/*">
                    </div>
                    <button type="submit" class="btn">Update Product</button>
                    <p><a href="dashboard.php">Back to Dashboard</a></p>
                </form>
            </div>
        </div>
    </section>
</body>
</html>
