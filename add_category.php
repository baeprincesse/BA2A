<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
require_once 'conn.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    if (!empty($name)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (:name)");
            $stmt->execute(['name' => $name]);
            $message = "<div class='alert alert-success'>Category added successfully!</div>";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $message = "<div class='alert alert-error'>Category already exists.</div>";
            } else {
                $message = "<div class='alert alert-error'>Error: " . $e->getMessage() . "</div>";
            }
        }
    } else {
        $message = "<div class='alert alert-error'>Please enter a category name.</div>";
    }
}
?>

<?php include 'includes/header.php'; ?>

<div style="max-width: 600px; margin: 0 auto;">
    <div class="card">
        <h2 style="margin-top:0; color:var(--primary);">Add New Category</h2>
        <?php echo $message; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Category Name</label>
                <input type="text" id="name" name="name" class="form-control" required placeholder="e.g. Electronics, Clothing">
            </div>
            <button type="submit" class="btn" style="width: 100%;">Add Category</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
