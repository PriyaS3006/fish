<?php
session_start();

if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    header('Location: dashboard.php');
    exit;
}

$error = '';

// Permanent Admin Login
$admin_username = "priya";
$admin_password = "priya8832";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = 'Please enter username and password.';
    } else {

        // Check fixed admin login
        if ($username === $admin_username && $password === $admin_password) {

            $_SESSION['user_id'] = 1;
            $_SESSION['username'] = $admin_username;
            $_SESSION['role'] = 'admin';

            header('Location: dashboard.php');
            exit;

        } else {
            $error = 'Invalid admin credentials.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Fishy Shop</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <section class="form-section">
        <div class="container">
            <div class="form-box">
                <h2>Admin Login</h2>

                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Admin Username</label>
                        <input type="text" name="username" required>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>

                    <button type="submit" class="btn">Login</button>

                    <p><a href="../index.php">Back to Shop</a></p>
                </form>
            </div>
        </div>
    </section>
</body>

</html>