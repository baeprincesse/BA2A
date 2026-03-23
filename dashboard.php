<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'conn.php';
$role = $_SESSION['role'] ?? 'customer';
?>

<?php include 'includes/header.php'; ?>

<div style="max-width: 1000px; margin: 0 auto;">
    <div class="text-center mb-4">
        <h2 style="color: var(--primary);">Welcome to your Dashboard, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p style="color: #64748B;">Select an action below to continue.</p>
    </div>

    <div class="product-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
        
        <?php if ($role === 'admin'): ?>
            <a href="add_category.php" style="text-decoration: none;">
                <div class="card" style="text-align: center; height: 100%;">
                    <h3 style="color: #1E293B;">Add Category</h3>
                    <p style="color: #64748B; font-size: 0.9rem;">Create new product categories for the store.</p>
                </div>
            </a>
            
            <a href="add_product.php" style="text-decoration: none;">
                <div class="card" style="text-align: center; height: 100%;">
                    <h3 style="color: #1E293B;">Add Product</h3>
                    <p style="color: #64748B; font-size: 0.9rem;">Upload new products and images.</p>
                </div>
            </a>
        <?php endif; ?>

        <a href="index.php" style="text-decoration: none;">
            <div class="card" style="text-align: center; height: 100%;">
                <h3 style="color: #1E293B;">Shop</h3>
                <p style="color: #64748B; font-size: 0.9rem;">Browse our premium storefront.</p>
            </div>
        </a>

        <a href="logout.php" style="text-decoration: none;">
            <div class="card" style="text-align: center; height: 100%; border-color: #FECACA; background: #FEF2F2;">
                <h3 style="color: #991B1B;">Logout</h3>
                <p style="color: #B91C1C; font-size: 0.9rem;">Sign out of your account securely.</p>
            </div>
        </a>

    </div>
</div>

<?php include 'includes/footer.php'; ?>
