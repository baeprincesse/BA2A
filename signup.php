<?php
session_start();
require_once 'conn.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, 'customer')");
            $stmt->execute(['username' => $username, 'password' => $hash]);
            $message = "<div class='alert alert-success'>Registration successful! You can now <a href='login.php'>login</a>.</div>";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $message = "<div class='alert alert-error'>Username already exists.</div>";
            } else {
                $message = "<div class='alert alert-error'>Error: " . $e->getMessage() . "</div>";
            }
        }
    } else {
        $message = "<div class='alert alert-error'>Please fill in all fields.</div>";
    }
}
?>

<?php include 'includes/header.php'; ?>

<div style="max-width: 400px; margin: 4rem auto;">
    <div class="card">
        <h2 style="margin-top:0; color:var(--primary); text-align: center;">Customer Sign Up</h2>
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
            <button type="submit" class="btn" style="width: 100%;">Sign Up</button>
            <div class="text-center mt-4" style="margin-top: 1rem;">
                <p>Already have an account? <a href="login.php" style="color: var(--primary);">Login here</a></p>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
