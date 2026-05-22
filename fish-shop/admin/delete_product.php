<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

include '../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();

if ($product) {
    if ($product['image'] !== 'default_fish.jpg' && file_exists('../images/' . $product['image'])) {
        unlink('../images/' . $product['image']);
    }
    $conn->query("DELETE FROM products WHERE id=$id");
}

header('Location: dashboard.php?msg=Product deleted successfully!');
exit;
?>
