<?php
session_start();
require_once 'conn.php';
$role = $_SESSION['role'] ?? 'guest';

// Fetch all products with their category names
$query = "
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.created_at DESC
";
$stmt = $pdo->query($query);
$products = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<div class="text-center mb-4">
    <h1 style="color: var(--primary); font-size: 2.5rem; margin-bottom: 0.5rem;">Welcome to LuxeStore</h1>
    <p style="color: #64748B; font-size: 1.1rem; max-width: 600px; margin: 0 auto;">Discover the premium selection of our newest items available today.</p>
</div>

<?php if (empty($products)): ?>
    <div class="alert" style="background:var(--card-bg); text-align: center; border: 1px solid var(--border-color);">
        <p>No products available yet.</p>
        <div style="margin-top: 1rem;">
            <a href="add_product.php" class="btn">Add a Product</a>
        </div>
    </div>
<?php else: ?>
    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <div class="product-image-container">
                    <?php if (!empty($product['image_path'])): ?>
                        <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                    <?php else: ?>
                        <!-- Fallback placeholder if no image is uploaded -->
                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #E2E8F0; color: #94A3B8;">
                            <span>No Image</span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <div class="product-category">
                        <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>
                    </div>
                    <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <?php if (!empty($product['description'])): ?>
                        <p style="color: #64748B; font-size: 0.9rem; margin-bottom: 1rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?php echo htmlspecialchars($product['description']); ?></p>
                    <?php endif; ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem;">
                        <span class="product-price" style="margin-top: 0;"><?php echo number_format($product['price'], 0, ',', ' '); ?> FCFA</span>
                        <?php if ($role === 'admin'): ?>
                            <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn" style="background: #3B82F6;">Modify</a>
                        <?php else: ?>
                            <a href="payment.php?id=<?php echo $product['id']; ?>" class="btn">Buy Now</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
