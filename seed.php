<?php
/**
 * seed.php - Inserts sample categories and products.
 * Run after init_db.php: php seed.php
 * Images are NOT required for seeding - products will show "No Image" placeholder
 * until you upload real images via the admin UI (edit_product.php).
 */

require_once 'conn.php'; // Uses ecom_user credentials from conn.php

try {
    // 1. Insert Categories (IGNORE duplicates)
    $categories = ['Electronics', 'Accessories', 'Bags'];
    $categoryIds = [];

    foreach ($categories as $cat) {
        $pdo->prepare("INSERT IGNORE INTO categories (name) VALUES (?)")->execute([$cat]);
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
        $stmt->execute([$cat]);
        $categoryIds[$cat] = $stmt->fetchColumn();
    }
    echo "Categories seeded.\n";

    // 2. Ensure uploads directory exists
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    // 3. Sample products (no Windows image paths - images are managed via admin UI)
    $products = [
        [
            'cat'   => 'Electronics',
            'name'  => 'Luxe Modern Smartphone Pro',
            'desc'  => 'Experience next-generation speed and photography with the advanced camera system.',
            'price' => 350000,
        ],
        [
            'cat'   => 'Accessories',
            'name'  => 'Classic Leather Wristwatch',
            'desc'  => 'A timeless piece with genuine leather strap and reliable automatic movement.',
            'price' => 85000,
        ],
        [
            'cat'   => 'Bags',
            'name'  => 'Premium Brown Leather Backpack',
            'desc'  => 'Durable, stylish, and perfect for both daily commutes and weekend getaways.',
            'price' => 45000,
        ],
        [
            'cat'   => 'Electronics',
            'name'  => 'Wireless Noise-Cancelling Headphones',
            'desc'  => 'Immersive sound quality with 30-hour battery life and premium comfort.',
            'price' => 120000,
        ],
        [
            'cat'   => 'Accessories',
            'name'  => 'Polarized Sunglasses',
            'desc'  => 'UV400 protection with a sleek frame design for any occasion.',
            'price' => 22000,
        ],
    ];

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT IGNORE INTO products (name, description, price, category_id, image_path)
        VALUES (:name, :desc, :price, :cat, NULL)
    ");

    foreach ($products as $p) {
        $stmt->execute([
            'name'  => $p['name'],
            'desc'  => $p['desc'],
            'price' => $p['price'],
            'cat'   => $categoryIds[$p['cat']],
        ]);
        echo "Inserted: " . $p['name'] . "\n";
    }

    $pdo->commit();
    echo "\nSeeding complete! Use the admin UI (edit_product.php) to upload product images.\n";

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Seeding failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
