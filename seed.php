<?php
require_once 'conn.php';

try {
    // 1. Insert Categories
    $categories = ['Electronics', 'Accessories', 'Bags'];
    $categoryIds = [];
    foreach ($categories as $cat) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name) VALUES (:name)");
        $stmt->execute(['name' => $cat]);
        
        // Fetch ID
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = :name");
        $stmt->execute(['name' => $cat]);
        $categoryIds[$cat] = $stmt->fetchColumn();
    }
    
    // 2. Prepare destination directory
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // 3. Define the products and their source images
    $productsData = [
        [
            'cat' => 'Electronics',
            'name' => 'Luxe Modern Smartphone Pro',
            'desc' => 'Experience next-generation speed and photography with the advanced camera system.',
            'price' => 999.00,
            'source_img' => 'C:\Users\ngoum\.gemini\antigravity\brain\d4ac4d75-481b-4302-b217-89e0dc41179f\modern_smartphone_1774237370701.png',
            'target_name' => 'modern_smartphone.png'
        ],
        [
            'cat' => 'Accessories',
            'name' => 'Classic Leather Wristwatch',
            'desc' => 'A timeless piece with genuine leather strap and reliable automatic movement.',
            'price' => 249.50,
            'source_img' => 'C:\Users\ngoum\.gemini\antigravity\brain\d4ac4d75-481b-4302-b217-89e0dc41179f\classic_wristwatch_1774237547056.png',
            'target_name' => 'classic_wristwatch.png'
        ],
        [
            'cat' => 'Bags',
            'name' => 'Premium Brown Leather Backpack',
            'desc' => 'Durable, stylish, and perfect for both daily commutes and weekend getaways.',
            'price' => 120.00,
            'source_img' => 'C:\Users\ngoum\.gemini\antigravity\brain\d4ac4d75-481b-4302-b217-89e0dc41179f\leather_backpack_1774237619724.png',
            'target_name' => 'leather_backpack.png'
        ]
    ];
    
    $pdo->beginTransaction();
    
    // 4. Copy images and insert products
    foreach ($productsData as $data) {
        $categoryId = $categoryIds[$data['cat']];
        $targetPath = $uploadDir . $data['target_name'];
        $dbPath = 'uploads/' . $data['target_name'];
        
        if (file_exists($data['source_img'])) {
            copy($data['source_img'], $targetPath);
            
            $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category_id, image_path) VALUES (:name, :desc, :price, :cat, :img)");
            $stmt->execute([
                'name' => $data['name'],
                'desc' => $data['desc'],
                'price' => $data['price'],
                'cat' => $categoryId,
                'img' => $dbPath
            ]);
            echo "Inserted " . $data['name'] . "<br>";
        } else {
            echo "Source image not found for " . $data['name'] . "<br>";
        }
    }
    
    $pdo->commit();
    echo "Seeding complete.";

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Seeding failed: " . $e->getMessage();
}
?>
