<?php
session_start();
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'config/db.php';
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'] ?? 'user';

    if (empty($username) || empty($password)) {
        $error = 'Please enter username and password.';
    } else {
        $query = "SELECT * FROM users WHERE username='$username'";
        if ($role === 'admin') {
            $query .= " AND role='admin'";
        }
        $result = $conn->query($query);
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                if ($user['role'] === 'admin') {
                    header('Location: admin/dashboard.php');
                } else {
                    header('Location: index.php');
                }
                exit;
            }
        }
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Fishy Shop</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <section class="form-section">
        <div class="container">
            <div class="form-box">
                <h2>Login</h2>
                <?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
                <form method="POST">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label>Login as:</label>
                        <input type="radio" name="role" value="user" checked> User
                        <input type="radio" name="role" value="admin"> Admin
                    </div>
                    <button type="submit" class="btn">Login</button>
                    <p>Don't have an account? <a href="register.php">Register</a></p>
                </form>
            </div>
        </div>
    </section>
    <?php include 'footer.php'; ?>
</body>
</html>
