<?php
session_start();
require_once 'conn.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            header("Location: dashboard.php");
            exit;
        } else {
            $message = "<div class='alert alert-error'>Invalid username or password.</div>";
        }
    } else {
        $message = "<div class='alert alert-error'>Please fill in all fields.</div>";
    }
}
?>

<?php include 'includes/header.php'; ?>

<div style="max-width: 400px; margin: 4rem auto;">
    <div class="card">
        <h2 style="margin-top:0; color:var(--primary); text-align: center;">Login</h2>
        <?php echo $message; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn" style="width: 100%;">Login</button>
            <div class="text-center mt-4" style="margin-top: 1rem;">
                <p>Don't have an account? <a href="signup.php" style="color: var(--primary);">Sign up here</a></p>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
