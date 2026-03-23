<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
require_once 'conn.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$productId = $_GET['id'];
$message = '';

// Fetch the existing product
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute(['id' => $productId]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: index.php");
    exit;
}

// Fetch categories for the dropdown
$stmtCat = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmtCat->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    
    // Existing image path
    $imagePath = $product['image_path'];
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetFile = $uploadDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Basic validation
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = 'uploads/' . $fileName;
                // Optionally delete old image from disk to save space here
            } else {
                $message = "<div class='alert alert-error'>Failed to upload new image.</div>";
            }
        } else {
            $message = "<div class='alert alert-error'>Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.</div>";
        }
    }

    if (empty($message) && !empty($name) && !empty($price) && !empty($category_id)) {
        try {
            $updateStmt = $pdo->prepare("UPDATE products SET name = :name, description = :description, price = :price, category_id = :category_id, image_path = :image_path WHERE id = :id");
            $updateStmt->execute([
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'category_id' => $category_id,
                'image_path' => $imagePath,
                'id' => $productId
            ]);
            $message = "<div class='alert alert-success'>Product updated successfully!</div>";
            
            // Refresh product info
            $stmt->execute(['id' => $productId]);
            $product = $stmt->fetch();
        } catch (PDOException $e) {
            $message = "<div class='alert alert-error'>Error: " . $e->getMessage() . "</div>";
        }
    } elseif(empty($message)) {
        $message = "<div class='alert alert-error'>Please fill in all required fields.</div>";
    }
}
?>

<?php include 'includes/header.php'; ?>

<div style="max-width: 800px; margin: 0 auto;">
    <div class="card">
        <h2 style="margin-top:0; color:var(--primary);">Modify Product #<?php echo htmlspecialchars($product['id']); ?></h2>
        <?php echo $message; ?>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Product Name *</label>
                <input type="text" id="name" name="name" class="form-control" required value="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            
            <div class="form-group">
                <label for="category_id">Category *</label>
                <select id="category_id" name="category_id" class="form-control" required>
                    <option value="">-- Select Category --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category['id']); ?>" <?php if($product['category_id'] == $category['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="price">Price (FCFA) *</label>
                <input type="number" id="price" name="price" class="form-control" step="0.01" min="0" required value="<?php echo htmlspecialchars($product['price']); ?>">
            </div>

            <div class="form-group">
                <label>Current Image</label><br>
                <?php if (!empty($product['image_path'])): ?>
                    <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="Current Product Image" style="max-width: 150px; border-radius: 8px; margin-bottom: 1rem;"><br>
                <?php else: ?>
                    <p style="color: #64748B;">No image attached.</p>
                <?php endif; ?>
                <label for="image">Upload New Image (Optional)</label>
                <input type="file" id="image" name="image" class="form-control" accept="image/*">
            </div>

            <div class="form-group">
                <label for="description">Description (Optional)</label>
                <textarea id="description" name="description" class="form-control" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            
            <button type="submit" class="btn" style="width: 100%; background: #059669;">Update Product</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
